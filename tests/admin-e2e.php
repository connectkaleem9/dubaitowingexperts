<?php

declare(strict_types=1);

/*
 * End-to-end admin check: sign in â†’ create a project with an image â†’ publish â†’ verify on the
 * public site â†’ unpublish â†’ moderate a review â†’ save settings â†’ check roles and the activity log.
 *
 *   php tests/admin-e2e.php <base-url> <admin-email> <admin-password>
 *
 * It creates and then deletes test records, so run it against a development database only.
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

[$script, $base, $adminEmail, $adminPassword] = array_pad($argv, 4, null);
$base = rtrim((string) ($base ?: 'http://127.0.0.1:8080'), '/');
if (!$adminEmail || !$adminPassword) {
    fwrite(STDERR, "Usage: php tests/admin-e2e.php <base-url> <admin-email> <admin-password>\n");
    exit(1);
}
if (config('app.env') === 'production') {
    fwrite(STDERR, "Refusing to run against a production environment.\n");
    exit(1);
}
$jar = sys_get_temp_dir() . '/dre-admin-' . bin2hex(random_bytes(4)) . '.txt';
$fails = [];

function req(string $method, string $url, array $fields = [], array $files = []): array
{
    global $jar;
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 60, CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    ];
    if ($files !== []) {
        foreach ($files as $key => $path) {
            $fields[$key] = new CURLFile($path, 'image/jpeg', basename($path));
        }
        $opts[CURLOPT_POSTFIELDS] = $fields;
    } elseif ($fields !== []) {
        $opts[CURLOPT_POSTFIELDS] = http_build_query($fields);
    }
    curl_setopt_array($ch, $opts);
    $raw = (string) curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $hs = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    preg_match('/^Location: (.*)$/mi', substr($raw, 0, $hs), $m);
    return ['status' => $status, 'body' => substr($raw, $hs), 'location' => trim($m[1] ?? '')];
}

function token(string $html): string
{
    preg_match('/name="_token" value="([a-f0-9]{64})"/', $html, $m);
    return $m[1] ?? '';
}

function check(string $name, bool $ok, string $detail = ''): void
{
    global $fails;
    echo ($ok ? '  ok   ' : '  FAIL ') . $name . ($ok ? '' : " â€” {$detail}") . "\n";
    if (!$ok) {
        $fails[] = $name;
    }
}

// A small JPEG to upload
$tmpImage = sys_get_temp_dir() . '/dre-test-photo.jpg';
$im = imagecreatetruecolor(1400, 900);
imagefill($im, 0, 0, imagecolorallocate($im, 20, 60, 100));
imagefilledrectangle($im, 100, 500, 1300, 800, imagecolorallocate($im, 255, 180, 0));
imagejpeg($im, $tmpImage, 85);
imagedestroy($im);

echo "== Admin end-to-end ==\n";

$login = req('GET', $base . '/admin/login/');
$r = req('POST', $base . '/admin/login/', ['_token' => token($login['body']), 'email' => $adminEmail, 'password' => $adminPassword]);
check('owner can sign in', $r['status'] === 302 && $r['location'] === '/admin/', 'status ' . $r['status'] . ' -> ' . $r['location']);

// Signing in lands on Projects; the dashboard moved to its own path when the menu was trimmed.
$landing = req('GET', $base . '/admin/');
check('admin home goes to projects', $landing['status'] === 302 && $landing['location'] === '/admin/projects/',
    'status ' . $landing['status'] . ' -> ' . $landing['location']);
$dash = req('GET', $base . '/admin/dashboard/');
check('dashboard loads', $dash['status'] === 200 && str_contains($dash['body'], 'New inquiries'), 'status ' . $dash['status']);

$form = req('GET', $base . '/admin/projects/new/');
$slug = 'test-recovery-job-' . bin2hex(random_bytes(3));
$create = req('POST', $base . '/admin/projects/new/', [
    '_token' => token($form['body']),
    'title' => 'Sedan recovered from a Marina basement',
    'slug' => $slug,
    'service_id' => (string) Database::value("SELECT id FROM services WHERE slug = 'car-recovery'"),
    'area_id' => (string) Database::value("SELECT id FROM areas WHERE slug = 'dubai-marina'"),
    'project_date' => date('Y-m-d'),
    'status' => 'published',
], ['after' => $tmpImage]);
check('project created with an uploaded image', $create['status'] === 302, 'status ' . $create['status']);

$project = Database::one('SELECT * FROM projects WHERE slug = :s', ['s' => $slug]);
check('project stored as published', $project !== null && $project['status'] === 'published');
// There is no featured-image field any more: the card photo follows the "after" shot.
check('after photo becomes the card image and is converted to WebP',
    $project !== null && $project['after_image_id'] !== null
    && (int) $project['featured_image_id'] === (int) $project['after_image_id']
    && (string) Database::value('SELECT mime FROM media WHERE id = :i', ['i' => (int) $project['featured_image_id']]) === 'image/webp');

$media = Database::one('SELECT * FROM media WHERE id = :i', ['i' => (int) ($project['featured_image_id'] ?? 0)]);
$variantsExist = $media !== null;
if ($media !== null) {
    foreach (explode(',', (string) $media['widths']) as $w) {
        $variantsExist = $variantsExist && is_file(BASE_PATH . '/public/uploads/' . $media['path'] . '-' . (int) $w . '.webp');
    }
}
check('resized image files written to disk', $variantsExist);

$public = req('GET', $base . '/projects/' . $slug . '/');
check('published project is live on the website', $public['status'] === 200 && str_contains($public['body'], 'Sedan recovered'), 'status ' . $public['status']);
check('project image served responsively', str_contains($public['body'], 'srcset='));
// A project with no description is a thin page: noindex, and kept out of the sitemap.
check('project without a description is noindex', str_contains($public['body'], 'name="robots" content="noindex,follow"'));
check('thin project stays out of the sitemap', !str_contains(req('GET', $base . '/sitemap.xml')['body'], '/projects/' . $slug . '/'));

$list = req('GET', $base . '/admin/projects/');
$unpub = req('POST', $base . '/admin/projects/' . (int) $project['id'] . '/status/', ['_token' => token($list['body'])]);
check('project can be unpublished', $unpub['status'] === 302 && (string) Database::value('SELECT status FROM projects WHERE id = :i', ['i' => (int) $project['id']]) === 'draft');
check('unpublished project 404s publicly', req('GET', $base . '/projects/' . $slug . '/')['status'] === 404);

// Review moderation
$reviewId = Database::insert('reviews', [
    'name' => 'Moderation Test', 'rating' => 5, 'body' => 'Quick help when my car broke down near Marina Gate, and the price was agreed up front.',
    'status' => 'pending', 'consent_at' => date('Y-m-d H:i:s'),
]);
$reviewsPage = req('GET', $base . '/admin/reviews/?status=pending');
check('pending review is listed for moderation', str_contains($reviewsPage['body'], 'Moderation Test'));
$approve = req('POST', $base . '/admin/reviews/' . $reviewId . '/moderate/', ['_token' => token($reviewsPage['body']), 'action' => 'approve', 'back' => '/admin/reviews/']);
check('review can be approved', $approve['status'] === 302 && (string) Database::value('SELECT status FROM reviews WHERE id = :i', ['i' => $reviewId]) === 'approved');
check('approved review is published publicly', str_contains(req('GET', $base . '/reviews/')['body'], 'Moderation Test'));

// Settings + SEO + media modules load
foreach (['/admin/leads/', '/admin/services/', '/admin/areas/', '/admin/faqs/', '/admin/posts/', '/admin/media/', '/admin/seo/', '/admin/settings/', '/admin/users/', '/admin/activity/', '/admin/security/'] as $path) {
    $res = req('GET', $base . $path);
    check('admin page loads: ' . $path, $res['status'] === 200, 'status ' . $res['status']);
}

// Settings save — post the CURRENT values back, changing only one field, so the test never
// wipes real settings (the form saves every field it is given).
$settings = req('GET', $base . '/admin/settings/');
$controller = new Admin\Controllers\SettingsController();
$before = [];
$post = ['_token' => token($settings['body'])];
foreach (array_keys(Admin\Controllers\SettingsController::FIELDS) as $key) {
    $before[$key] = (string) (App\Models\Setting::get($key) ?? '');
    $post[$controller->inputName($key)] = $before[$key];
}
$post[$controller->inputName('notify_email')] = 'e2e-test@local.test';
$post[$controller->inputName('consent_default')] = $before['consent_default'] ?: 'denied';
$save = req('POST', $base . '/admin/settings/', $post);
check('settings save', $save['status'] === 302 && (string) Database::value("SELECT value FROM settings WHERE `key` = 'notify_email'") === 'e2e-test@local.test');
foreach ($before as $key => $value) {
    App\Models\Setting::set($key, $value);
}
check('settings restored after the test', (string) (App\Models\Setting::get('notify_email') ?? '') === $before['notify_email']);

// Activity log recorded the admin actions
$logCount = (int) Database::value("SELECT COUNT(*) FROM activity_logs WHERE action IN ('project_created','review_approve','settings_updated')");
check('admin actions are recorded in the activity log', $logCount >= 3, "found {$logCount}");

// Editor role cannot reach owner-only screens
$editorPass = 'EditorPass1234567';
$editorId = App\Models\Admin::create('Editor Test', 'editor@local.test', $editorPass, 'editor');
$jar = sys_get_temp_dir() . '/dre-editor-' . bin2hex(random_bytes(4)) . '.txt';
$login2 = req('GET', $base . '/admin/login/');
req('POST', $base . '/admin/login/', ['_token' => token($login2['body']), 'email' => 'editor@local.test', 'password' => $editorPass]);
$forbidden = req('GET', $base . '/admin/settings/');
check('editor is blocked from owner-only settings', $forbidden['status'] === 403, 'status ' . $forbidden['status']);
check('editor can still use the projects module', req('GET', $base . '/admin/projects/')['status'] === 200);

// Clean up test data
Database::run('DELETE FROM projects WHERE slug = :s', ['s' => $slug]);
Database::run('DELETE FROM reviews WHERE id = :i', ['i' => $reviewId]);
Database::run('DELETE FROM admins WHERE id = :i', ['i' => $editorId]);
@unlink($tmpImage);

echo "\n" . ($fails === [] ? "Admin end-to-end: all checks passed.\n" : count($fails) . " FAILED: " . implode(', ', $fails) . "\n");
exit($fails === [] ? 0 : 1);
