<?php

declare(strict_types=1);

/*
 * Unit + functional tests.
 *   php tests/run.php                      unit tests only
 *   php tests/run.php http://127.0.0.1:8080  also runs HTTP functional/security tests
 * The HTTP tests write to the database they are pointed at â€” never run them against production.
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Services\HtmlSanitizer;
use App\Services\Schema;
use App\Services\Seo;
use App\Services\Sitemap;
use App\Validation\Validator;

$baseUrl = rtrim((string) ($argv[1] ?? ''), '/');
$passed = 0;
$failed = [];

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        $passed++;
        echo "  ok   {$name}\n";
    } catch (Throwable $e) {
        $failed[] = $name . ' â€” ' . $e->getMessage();
        echo "  FAIL {$name}\n       " . $e->getMessage() . "\n";
    }
}

function assertTrue(bool $condition, string $message = 'expected true'): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertSame(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function assertContains(string $needle, string $haystack, string $message = ''): void
{
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'missing "' . $needle . '"');
    }
}

function assertNotContains(string $needle, string $haystack, string $message = ''): void
{
    if (str_contains($haystack, $needle)) {
        throw new RuntimeException(($message !== '' ? $message . ': ' : '') . 'unexpectedly found "' . $needle . '"');
    }
}

/** Minimal cookie-aware HTTP client. @return array{status:int, body:string, headers:array<string,string>} */
function http(string $method, string $url, array $post = [], string $cookieJar = ''): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => $method,
    ]);
    if ($post !== []) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookieJar !== '') {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }
    $response = (string) curl_exec($ch);
    if ($response === '' && curl_errno($ch)) {
        throw new RuntimeException('HTTP error: ' . curl_error($ch));
    }
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    $rawHeaders = substr($response, 0, $headerSize);
    $headers = [];
    foreach (explode("\r\n", $rawHeaders) as $line) {
        if (str_contains($line, ':')) {
            [$k, $v] = explode(':', $line, 2);
            $headers[strtolower(trim($k))] = trim($v);
        }
    }
    return ['status' => $status, 'body' => substr($response, $headerSize), 'headers' => $headers];
}

function csrfFrom(string $html): string
{
    preg_match('/name="_token" value="([a-f0-9]{64})"/', $html, $m);
    return $m[1] ?? '';
}

function tsFrom(string $html): string
{
    preg_match('/name="_ts" value="([^"]+)"/', $html, $m);
    return html_entity_decode($m[1] ?? '', ENT_QUOTES);
}

/**
 * A form token that looks like the visitor spent $seconds filling the form.
 * Real submissions faster than 3 seconds are treated as bots (FormGuard), so tests
 * that want the "human" path sign an older timestamp with the app key.
 */
function humanTs(int $seconds = 20): string
{
    $ts = (string) (time() - $seconds);
    return $ts . '.' . hash_hmac('sha256', $ts, (string) config('app.key', 'dre'));
}

echo "\n== Validation ==\n";
test('required fields are enforced', function (): void {
    $v = Validator::make(['name' => ' '], ['name' => 'required|max:10']);
    assertTrue($v->fails());
    assertContains('required', $v->errors()['name']);
});
test('valid UAE phone numbers pass, junk fails', function (): void {
    assertTrue(!Validator::make(['phone' => '052 585 1934'], ['phone' => 'phone'])->fails());
    assertTrue(!Validator::make(['phone' => '+971525851934'], ['phone' => 'phone'])->fails());
    assertTrue(Validator::make(['phone' => 'call me'], ['phone' => 'phone'])->fails());
    assertTrue(Validator::make(['phone' => '123'], ['phone' => 'phone'])->fails());
});
test('email, slug, rating range and "in" rules', function (): void {
    assertTrue(Validator::make(['email' => 'not-an-email'], ['email' => 'email'])->fails());
    assertTrue(Validator::make(['slug' => 'Bad Slug'], ['slug' => 'slug'])->fails());
    assertTrue(Validator::make(['rating' => '6'], ['rating' => 'int|min_value:1|max_value:5'])->fails());
    assertTrue(Validator::make(['x' => 'z'], ['x' => 'in:a,b'])->fails());
});
test('nullable fields skip rules when empty', function (): void {
    assertTrue(!Validator::make(['email' => ''], ['email' => 'nullable|email'])->fails());
});
test('array input cannot bypass string rules', function (): void {
    assertTrue(Validator::make(['name' => ['a']], ['name' => 'required|max:5'])->fails());
});

echo "\n== HTML sanitiser (stored XSS) ==\n";
test('script tags and handlers are removed', function (): void {
    $out = HtmlSanitizer::clean('<p onclick="steal()">Hi</p><script>alert(1)</script>');
    assertNotContains('script', $out);
    assertNotContains('onclick', $out);
    assertContains('<p>Hi</p>', $out);
});
test('javascript: links are stripped but text kept', function (): void {
    $out = HtmlSanitizer::clean('<a href="javascript:alert(1)">click</a>');
    assertNotContains('javascript:', $out);
    assertContains('click', $out);
});
test('allowed structure survives, H1 becomes H2', function (): void {
    $out = HtmlSanitizer::clean('<h1>Title</h1><h2>Sub</h2><ul><li>One</li></ul><strong>b</strong>');
    assertNotContains('<h1>', $out);
    assertContains('<h2>Title</h2>', $out);
    assertContains('<li>One</li>', $out);
    assertContains('<strong>b</strong>', $out);
});
test('external links get rel="noopener noreferrer"', function (): void {
    $out = HtmlSanitizer::clean('<a href="https://example.com/x">x</a>');
    assertContains('noopener', $out);
});
test('plain text is wrapped in paragraphs', function (): void {
    assertContains('<p>Hello</p>', HtmlSanitizer::clean('Hello'));
});
test('iframes and forms are dropped', function (): void {
    $out = HtmlSanitizer::clean('<iframe src="https://evil.test"></iframe><form><input></form><p>ok</p>');
    assertNotContains('iframe', $out);
    assertNotContains('<form', $out);
    assertContains('<p>ok</p>', $out);
});

echo "\n== Helpers ==\n";
test('slugify produces clean URL slugs', function (): void {
    assertSame('dubai-marina', slugify('Dubai  Marina!'));
    assertSame('al-quoz', slugify('Al Quoz'));
});
test('escaping helper escapes quotes and tags', function (): void {
    assertSame('&lt;b&gt;&quot;x&quot;&lt;/b&gt;', e('<b>"x"</b>'));
});
test('phone and WhatsApp links use the business number', function (): void {
    assertSame('tel:' . config('business.phone_e164'), tel_href());
    assertContains('https://wa.me/' . config('business.whatsapp'), whatsapp_href('hi'));
    assertContains('text=hi', whatsapp_href('hi'));
});
test('str_limit does not cut mid-word without ellipsis', function (): void {
    assertTrue(mb_strlen(str_limit(str_repeat('word ', 50), 60)) <= 61);
});

echo "\n== SEO & schema ==\n";
test('canonical URLs are absolute and on the canonical host', function (): void {
    $seo = Seo::page('/services/car-recovery/', 'T', 'D');
    assertSame(rtrim((string) config('app.url'), '/') . '/services/car-recovery/', $seo->canonical());
});
test('noindex pages are marked not indexable', function (): void {
    assertTrue(!Seo::page('/thank-you/', 'T', 'D', 'noindex,follow')->isIndexable());
});
test('JSON-LD is valid JSON with the expected node types', function (): void {
    $seo = Seo::page('/services/car-recovery/', 'T', 'D')->crumbs('Services', '/services/')->crumbs('Car Recovery', '/services/car-recovery/');
    $seo->addSchema(Schema::service(['id' => 1, 'name' => 'Car Recovery', 'excerpt' => 'x'], '/services/car-recovery/'));
    $data = json_decode($seo->jsonLd(), true, 512, JSON_THROW_ON_ERROR);
    $types = array_column($data['@graph'], '@type');
    assertTrue(in_array('AutomotiveBusiness', $types, true), 'business node missing');
    assertTrue(in_array('BreadcrumbList', $types, true), 'breadcrumbs missing');
    assertTrue(in_array('Service', $types, true), 'service node missing');
});
test('schema never invents an address or rating', function (): void {
    $json = Schema::encode(Schema::business());
    assertNotContains('streetAddress', $json);
    assertNotContains('aggregateRating', $json);
    assertNotContains('review', $json);
});
test('opening hours are published only from confirmed settings', function (): void {
    $json = Schema::encode(Schema::business());
    $hours = (string) business('opening_hours');
    // The owner confirmed 24/7 on 2026-09-22 (decision D-010). If hours are ever cleared in
    // settings, the schema must stop claiming them rather than falling back to a guess.
    if ($hours === '') {
        assertNotContains('openingHours', $json);
    } else {
        assertContains('"openingHours":"' . $hours . '"', $json);
    }
});
test('sitemap includes services and excludes utility pages', function (): void {
    $xml = Sitemap::xml();
    assertContains('/services/car-recovery/', $xml);
    assertNotContains('/thank-you/', $xml);
    assertNotContains('/landing/', $xml);
    assertNotContains('/admin/', $xml);
});
test('unpublished areas stay out of the sitemap', function (): void {
    assertNotContains('/areas/jumeirah/', Sitemap::xml());
});

echo "\n== Rate limiting ==\n";
test('a bucket is allowed up to its limit and then refused', function (): void {
    $bucket = 'test-' . bin2hex(random_bytes(6));
    for ($i = 1; $i <= 3; $i++) {
        assertTrue(App\Models\RateLimit::hit($bucket, 3, 60), "hit {$i} should be allowed");
    }
    assertTrue(!App\Models\RateLimit::hit($bucket, 3, 60), 'the 4th hit should be refused');
    App\Models\RateLimit::clear($bucket);
    assertTrue(App\Models\RateLimit::hit($bucket, 3, 60), 'clearing the bucket should reset it');
    App\Models\RateLimit::clear($bucket);
});

echo "\n== Database ==\n";
test('SQL identifiers are validated before use', function (): void {
    try {
        Database::insert('users; DROP TABLE admins', ['a' => 1]);
        throw new RuntimeException('no exception thrown');
    } catch (InvalidArgumentException) {
        // expected
    }
});
test('seeded services are published with content', function (): void {
    $services = App\Models\Service::published();
    assertTrue(count($services) >= 6, 'expected at least 6 services');
    foreach ($services as $s) {
        assertTrue(strlen((string) $s['body']) > 500, $s['slug'] . ' has thin content');
        assertTrue($s['excerpt'] !== '', $s['slug'] . ' has no excerpt');
    }
});
test('published area pages are not thin', function (): void {
    foreach (App\Models\Area::published() as $a) {
        assertTrue(str_word_count(strip_tags((string) $a['body'])) >= 150, $a['slug'] . ' is a thin area page');
    }
});
test('no two published pages share a meta description', function (): void {
    $excerpts = array_column(App\Models\Service::published(), 'excerpt');
    $excerpts = array_merge($excerpts, array_column(App\Models\Area::published(), 'excerpt'));
    assertSame(count($excerpts), count(array_unique($excerpts)), 'duplicate descriptions');
});

if ($baseUrl === '') {
    echo "\n(Skipping HTTP tests â€” pass a base URL, e.g. php tests/run.php http://127.0.0.1:8080)\n";
} else {
    echo "\n== HTTP: pages ==\n";
    test('home page returns 200 with one H1 and a canonical', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/');
        assertSame(200, $r['status']);
        assertSame(1, substr_count($r['body'], '<h1'), 'exactly one H1 expected');
        assertContains('rel="canonical"', $r['body']);
    });
    test('security headers are sent', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/');
        assertContains('nosniff', $r['headers']['x-content-type-options'] ?? '');
        assertContains("object-src 'none'", $r['headers']['content-security-policy'] ?? '');
        assertContains('strict-origin', $r['headers']['referrer-policy'] ?? '');
    });
    test('unknown URL returns a helpful 404', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/definitely-not-a-page/');
        assertSame(404, $r['status']);
        assertContains('Page not found', $r['body']);
    });
    test('merged service URL 301s to its replacement', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/services/vehicle-recovery/');
        assertSame(301, $r['status']);
        assertSame('/services/car-recovery/', $r['headers']['location'] ?? '');
    });
    test('missing trailing slash 301s', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/services/car-recovery');
        assertSame(301, $r['status']);
    });
    test('thank-you page is noindex', function () use ($baseUrl): void {
        assertContains('name="robots" content="noindex', http('GET', $baseUrl . '/thank-you/')['body']);
    });
    test('Ads landing pages are noindex,follow', function () use ($baseUrl): void {
        assertContains('content="noindex,follow"', http('GET', $baseUrl . '/landing/car-recovery-dubai/')['body']);
    });

    echo "\n== HTTP: lead form ==\n";
    // Repeated test runs would otherwise hit the public form rate limit (5 leads / 10 min per IP),
    // which is exactly what it is there for — clear the counters so the form tests start clean.
    Database::run('DELETE FROM rate_limits');
    $jar = sys_get_temp_dir() . '/dre-test-' . bin2hex(random_bytes(4)) . '.txt';
    test('POST without a CSRF token is rejected (419)', function () use ($baseUrl, $jar): void {
        http('GET', $baseUrl . '/contact/', [], $jar);
        $r = http('POST', $baseUrl . '/contact/', ['name' => 'X', 'phone' => '0501234567', 'location' => 'Marina', 'preferred_contact' => 'phone'], $jar);
        assertSame(419, $r['status']);
    });
    test('honeypot submissions are dropped silently', function () use ($baseUrl, $jar): void {
        $page = http('GET', $baseUrl . '/contact/', [], $jar);
        $before = (int) Database::value('SELECT COUNT(*) FROM contact_submissions');
        $r = http('POST', $baseUrl . '/contact/', [
            '_token' => csrfFrom($page['body']), '_ts' => tsFrom($page['body']), 'website' => 'http://spam.test',
            'name' => 'Spam Bot', 'phone' => '0501234567', 'location' => 'Nowhere', 'preferred_contact' => 'phone',
        ], $jar);
        assertSame(302, $r['status']);
        assertSame('/thank-you/', $r['headers']['location'] ?? '');
        assertSame($before, (int) Database::value('SELECT COUNT(*) FROM contact_submissions'), 'spam was stored');
    });
    test('invalid data returns to the form with errors and no record', function () use ($baseUrl, $jar): void {
        $page = http('GET', $baseUrl . '/contact/', [], $jar);
        $before = (int) Database::value('SELECT COUNT(*) FROM contact_submissions');
        $r = http('POST', $baseUrl . '/contact/', [
            '_token' => csrfFrom($page['body']), '_ts' => humanTs(),
            'name' => '', 'phone' => 'nope', 'location' => '', 'preferred_contact' => 'phone',
        ], $jar);
        assertSame(302, $r['status']);
        assertSame($before, (int) Database::value('SELECT COUNT(*) FROM contact_submissions'));
        assertContains('Enter a valid phone number', http('GET', $baseUrl . '/contact/', [], $jar)['body']);
    });
    test('a valid enquiry is stored and reaches the thank-you page', function () use ($baseUrl, $jar): void {
        $page = http('GET', $baseUrl . '/contact/', [], $jar);
        $name = 'Test Lead ' . bin2hex(random_bytes(3));
        $r = http('POST', $baseUrl . '/contact/', [
            '_token' => csrfFrom($page['body']), '_ts' => humanTs(),
            'name' => $name, 'phone' => '052 585 1934', 'location' => 'Dubai Marina, Marina Gate 1',
            'service_id' => '', 'vehicle_type' => 'SUV / 4x4', 'message' => 'Car will not start', 'preferred_contact' => 'whatsapp',
            'form_type' => 'quote', 'source_path' => '/contact/',
        ], $jar);
        assertSame(302, $r['status']);
        assertSame('/thank-you/', $r['headers']['location'] ?? '');
        $row = Database::one('SELECT * FROM contact_submissions WHERE name = :n', ['n' => $name]);
        assertTrue($row !== null, 'lead was not stored');
        assertSame('quote', $row['form_type']);
        assertTrue($row['ip_hash'] !== null && !str_contains((string) $row['ip_hash'], '.'), 'raw IP stored');
        assertContains('data-conversion="quote_request"', http('GET', $baseUrl . '/thank-you/', [], $jar)['body']);
    });

    echo "\n== HTTP: reviews ==\n";
    test('a submitted review is stored as pending, not published', function () use ($baseUrl, $jar): void {
        $page = http('GET', $baseUrl . '/reviews/', [], $jar);
        $name = 'Reviewer ' . bin2hex(random_bytes(3));
        $r = http('POST', $baseUrl . '/reviews/', [
            '_token' => csrfFrom($page['body']), '_ts' => humanTs(),
            'name' => $name, 'rating' => '5', 'body' => 'They recovered my car from the Marina basement quickly and the price was agreed first.',
            'consent' => '1',
        ], $jar);
        assertSame(302, $r['status']);
        $row = Database::one('SELECT * FROM reviews WHERE name = :n', ['n' => $name]);
        assertTrue($row !== null, 'review not stored');
        assertSame('pending', $row['status']);
        assertNotContains($name, http('GET', $baseUrl . '/reviews/')['body'], 'pending review is publicly visible');
    });
    test('XSS in a review is escaped when rendered', function () use ($baseUrl, $jar): void {
        $marker = 'xss' . bin2hex(random_bytes(3));
        $payload = '<img src=x onerror=alert("' . $marker . '")>';
        Database::insert('reviews', [
            'name' => 'XSS ' . $marker, 'rating' => 5, 'body' => $payload . ' Great service, very helpful team.',
            'status' => 'approved', 'consent_at' => date('Y-m-d H:i:s'), 'approved_at' => date('Y-m-d H:i:s'),
        ]);
        $body = http('GET', $baseUrl . '/reviews/')['body'];
        assertContains('&lt;img src=x', $body);
        assertNotContains('<img src=x onerror', $body);
        Database::run('DELETE FROM reviews WHERE name = :n', ['n' => 'XSS ' . $marker]);
    });

    echo "\n== HTTP: admin security ==\n";
    test('admin pages redirect anonymous visitors to the login page', function () use ($baseUrl): void {
        foreach (['/admin/', '/admin/leads/', '/admin/projects/new/', '/admin/settings/'] as $path) {
            $r = http('GET', $baseUrl . $path);
            assertSame(302, $r['status'], $path);
            assertSame('/admin/login/', $r['headers']['location'] ?? '', $path);
        }
    });
    test('admin pages are noindex and not cached', function () use ($baseUrl): void {
        $r = http('GET', $baseUrl . '/admin/login/');
        assertContains('noindex', $r['body']);
        assertContains('no-store', $r['headers']['cache-control'] ?? '');
    });
    test('wrong password is refused with a generic message', function () use ($baseUrl): void {
        $jar2 = sys_get_temp_dir() . '/dre-test-login-' . bin2hex(random_bytes(4)) . '.txt';
        $page = http('GET', $baseUrl . '/admin/login/', [], $jar2);
        $r = http('POST', $baseUrl . '/admin/login/', ['_token' => csrfFrom($page['body']), 'email' => 'nobody@example.com', 'password' => 'wrong-password'], $jar2);
        assertSame(302, $r['status']);
        assertContains('Email or password is incorrect', http('GET', $baseUrl . '/admin/login/', [], $jar2)['body']);
        @unlink($jar2);
    });
    test('SQL injection in a login attempt does not break anything', function () use ($baseUrl): void {
        $jar2 = sys_get_temp_dir() . '/dre-test-sqli-' . bin2hex(random_bytes(4)) . '.txt';
        $page = http('GET', $baseUrl . '/admin/login/', [], $jar2);
        $r = http('POST', $baseUrl . '/admin/login/', ['_token' => csrfFrom($page['body']), 'email' => "' OR 1=1 -- ", 'password' => "' OR '1'='1"], $jar2);
        assertSame(302, $r['status']);
        assertSame('/admin/login/', $r['headers']['location'] ?? '');
        assertTrue((int) Database::value('SELECT COUNT(*) FROM admins') >= 0);
        @unlink($jar2);
    });
    @unlink($jar);
}

echo "\n" . str_repeat('-', 60) . "\n";
echo $failed === [] ? "All {$passed} tests passed.\n" : count($failed) . " FAILED of " . ($passed + count($failed)) . ":\n  - " . implode("\n  - ", $failed) . "\n";
exit($failed === [] ? 0 : 1);
