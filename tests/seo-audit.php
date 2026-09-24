<?php

declare(strict_types=1);

/*
 * Crawls the site and checks the on-page SEO rules in CLAUDE.md §6.
 *   php tests/seo-audit.php http://127.0.0.1:8080
 * Exits 1 if any error-level problem is found (warnings do not fail the run).
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

$base = rtrim((string) ($argv[1] ?? 'http://127.0.0.1:8080'), '/');
$host = parse_url($base, PHP_URL_HOST);

$errors = [];
$warnings = [];
$titles = [];
$descriptions = [];
$visited = [];
$queue = ['/'];
$checkedLinks = [];

function fetch(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 30, CURLOPT_USERAGENT => 'DRE-SEO-Audit']);
    $body = (string) curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    return ['status' => $status, 'body' => $body, 'type' => $type];
}

function headStatus(string $url): int
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 20, CURLOPT_USERAGENT => 'DRE-SEO-Audit']);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return $status;
}

function tag(string $html, string $pattern): ?string
{
    return preg_match($pattern, $html, $m) ? html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8') : null;
}

echo "SEO audit of {$base}\n" . str_repeat('=', 60) . "\n";

while ($queue !== []) {
    $path = array_shift($queue);
    if (isset($visited[$path])) {
        continue;
    }
    $visited[$path] = true;
    $page = fetch($base . $path);
    $err = static function (string $message) use (&$errors, $path): void {
        $errors[] = "{$path}: {$message}";
    };
    $warn = static function (string $message) use (&$warnings, $path): void {
        $warnings[] = "{$path}: {$message}";
    };

    if ($page['status'] !== 200) {
        $err('HTTP ' . $page['status']);
        continue;
    }
    if (!str_contains($page['type'], 'text/html')) {
        continue;
    }
    $html = $page['body'];

    $robots = tag($html, '/<meta name="robots" content="([^"]*)"/i') ?? 'index,follow';
    $indexable = !str_contains($robots, 'noindex');

    // Collect internal links for crawling and link checking.
    preg_match_all('/<a\b[^>]*href="([^"#?]+)/i', $html, $links);
    foreach ($links[1] as $href) {
        if (str_starts_with($href, 'http')) {
            if (parse_url($href, PHP_URL_HOST) !== $host) {
                continue;
            }
            $href = (string) parse_url($href, PHP_URL_PATH);
        }
        if ($href === '' || !str_starts_with($href, '/') || str_starts_with($href, '/admin/') || str_starts_with($href, '/assets/') || str_starts_with($href, '/uploads/')) {
            continue;
        }
        if (!isset($checkedLinks[$href])) {
            $checkedLinks[$href] = headStatus($base . $href);
            if ($checkedLinks[$href] >= 400) {
                $errors[] = "broken internal link {$href} (HTTP {$checkedLinks[$href]}) — first seen on {$path}";
            } elseif ($checkedLinks[$href] === 301 && !str_ends_with($href, '.xml') && !str_ends_with($href, '.txt')) {
                $warnings[] = "link to a redirecting URL {$href} — first seen on {$path}";
            }
        }
        if (!isset($visited[$href]) && $checkedLinks[$href] === 200 && !str_contains($href, '.')) {
            $queue[] = $href;
        }
    }

    $title = tag($html, '/<title>(.*?)<\/title>/is');
    $description = tag($html, '/<meta name="description" content="([^"]*)"/i');
    $canonical = tag($html, '/<link rel="canonical" href="([^"]*)"/i');
    $h1Count = preg_match_all('/<h1\b/i', $html);

    if ($title === null || $title === '') {
        $err('missing <title>');
    } elseif (mb_strlen($title) > 62) {
        $warn('title is ' . mb_strlen($title) . ' characters (over 60)');
    }
    if ($description === null || $description === '') {
        $indexable ? $err('missing meta description') : null;
    } elseif (mb_strlen($description) > 160) {
        $warn('meta description is ' . mb_strlen($description) . ' characters (over 155)');
    } elseif (mb_strlen($description) < 70 && $indexable) {
        $warn('meta description is short (' . mb_strlen($description) . ' characters)');
    }
    if ($h1Count !== 1) {
        $err("{$h1Count} H1 headings (expected exactly 1)");
    }
    if ($indexable && ($canonical === null || $canonical === '')) {
        $err('missing canonical');
    }
    if ($indexable && $canonical !== null && !str_ends_with($canonical, $path)) {
        $warn("canonical does not match the path ({$canonical})");
    }
    if ($indexable) {
        if (isset($titles[$title])) {
            $err("duplicate title, also used by {$titles[$title]}");
        }
        $titles[$title] = $path;
        if ($description !== null && isset($descriptions[$description])) {
            $err("duplicate meta description, also used by {$descriptions[$description]}");
        }
        $descriptions[(string) $description] = $path;
    }

    // Heading order (no skipped levels)
    preg_match_all('/<h([1-4])\b/i', $html, $levels);
    $previous = 0;
    foreach ($levels[1] as $level) {
        $level = (int) $level;
        if ($previous !== 0 && $level > $previous + 1) {
            $warn("heading level jumps from H{$previous} to H{$level}");
            break;
        }
        $previous = $level;
    }

    // Images must have alt attributes (empty alt is allowed for decorative images)
    preg_match_all('/<img\b[^>]*>/i', $html, $imgs);
    foreach ($imgs[0] as $img) {
        if (!preg_match('/\balt=/i', $img)) {
            $err('image without an alt attribute: ' . mb_substr(strip_tags($img), 0, 60));
        }
    }

    // Conversion elements
    if (!str_contains($html, 'tel:')) {
        $err('no phone link on the page');
    }
    if (!str_contains($html, 'wa.me/')) {
        $warn('no WhatsApp link on the page');
    }

    // Structured data must parse
    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m)) {
        json_decode($m[1], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $err('invalid JSON-LD: ' . json_last_error_msg());
        }
    } elseif ($indexable) {
        $err('no JSON-LD structured data');
    }

    // Unfinished content must never be live
    if (str_contains($html, '{{OWNER_TO_CONFIRM}}')) {
        $err('placeholder {{OWNER_TO_CONFIRM}} is visible on a live page');
    }
    if ($indexable && strlen(strip_tags($html)) < 1500) {
        $warn('very little text content — check this page is not thin');
    }
}

// Sitemap cross-check
$sitemap = fetch($base . '/sitemap.xml');
if ($sitemap['status'] !== 200) {
    $errors[] = '/sitemap.xml: HTTP ' . $sitemap['status'];
} else {
    preg_match_all('#<loc>([^<]+)</loc>#', $sitemap['body'], $locs);
    $sitemapPaths = array_map(static fn (string $u): string => (string) parse_url($u, PHP_URL_PATH), $locs[1]);
    foreach ($sitemapPaths as $sp) {
        $status = $checkedLinks[$sp] ?? headStatus($base . $sp);
        if ($status !== 200) {
            $errors[] = "sitemap lists {$sp} which returns HTTP {$status}";
        }
    }
    foreach (array_keys($visited) as $path) {
        $page = fetch($base . $path);
        if ($page['status'] === 200 && str_contains($page['type'], 'text/html')
            && !str_contains((string) tag($page['body'], '/<meta name="robots" content="([^"]*)"/i'), 'noindex')
            && !in_array($path, $sitemapPaths, true)) {
            $warnings[] = "indexable page {$path} is not in sitemap.xml";
        }
    }
}

$robotsTxt = fetch($base . '/robots.txt');
if ($robotsTxt['status'] !== 200) {
    $errors[] = '/robots.txt: HTTP ' . $robotsTxt['status'];
} elseif (!str_contains($robotsTxt['body'], 'Sitemap:')) {
    $warnings[] = 'robots.txt does not reference the sitemap';
}

echo 'Pages crawled: ' . count($visited) . ', internal links checked: ' . count($checkedLinks) . "\n\n";
if ($warnings !== []) {
    echo "WARNINGS (" . count($warnings) . "):\n  - " . implode("\n  - ", $warnings) . "\n\n";
}
if ($errors !== []) {
    echo "ERRORS (" . count($errors) . "):\n  - " . implode("\n  - ", $errors) . "\n";
    exit(1);
}
echo "No SEO errors found.\n";
