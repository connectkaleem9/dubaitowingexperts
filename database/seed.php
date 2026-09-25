<?php

declare(strict_types=1);

/*
 * Seeds initial content. Safe to re-run: each table is only seeded when it is empty.
 * Run via: php database/migrate.php --seed   (or directly: php database/seed.php)
 */

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}
if (!defined('BASE_PATH')) {
    require dirname(__DIR__) . '/app/bootstrap.php';
}

use App\Core\Database;
use App\Services\HtmlSanitizer;

$isEmpty = static fn (string $table): bool => (int) Database::value("SELECT COUNT(*) FROM `{$table}`") === 0;

// Settings (only missing keys are added; existing values are never overwritten)
$settings = [
    'whatsapp_default_message' => 'Hello Dubai Towing Experts, I need recovery assistance in Dubai. My location is: ',
    'ga4_id' => '',
    'search_console_token' => '',
    'gtm_id' => '',
    'consent_default' => 'denied',
    'notify_email' => '',
    'fleet_description' => '',
    'legal_updated' => '21 September 2026',
    'business.email' => '',
    'business.street_address' => '',
    'business.opening_hours' => 'Mo-Su 00:00-23:59',
    'business.google_maps_url' => '',
    'business.social' => '',
];
foreach ($settings as $key => $value) {
    Database::run('INSERT IGNORE INTO settings (`key`, `value`) VALUES (:k, :v)', ['k' => $key, 'v' => $value]);
}
echo "Settings ensured.\n";

if ($isEmpty('services')) {
    foreach (require __DIR__ . '/seeds/services.php' as $s) {
        $s['body'] = HtmlSanitizer::clean($s['body']);
        $s['is_published'] = 1;
        Database::insert('services', $s);
    }
    echo "Services seeded.\n";
}

if ($isEmpty('areas')) {
    foreach (require __DIR__ . '/seeds/areas.php' as $a) {
        $a['body'] = HtmlSanitizer::clean($a['body']);
        Database::insert('areas', $a);
    }
    echo "Areas seeded.\n";
}

if ($isEmpty('service_area')) {
    Database::run(
        'INSERT INTO service_area (service_id, area_id)
         SELECT s.id, a.id FROM services s CROSS JOIN areas a WHERE s.is_published = 1 AND a.is_published = 1'
    );
    echo "Service-area links seeded.\n";
}

if ($isEmpty('faqs')) {
    foreach (require __DIR__ . '/seeds/faqs.php' as $f) {
        $serviceId = isset($f['service']) ? Database::value('SELECT id FROM services WHERE slug = :s', ['s' => $f['service']]) : null;
        $areaId = isset($f['area']) ? Database::value('SELECT id FROM areas WHERE slug = :s', ['s' => $f['area']]) : null;
        unset($f['service'], $f['area']);
        $f['answer'] = HtmlSanitizer::clean($f['answer']);
        $f['service_id'] = $serviceId === null ? null : (int) $serviceId;
        $f['area_id'] = $areaId === null ? null : (int) $areaId;
        $f['is_published'] = 1;
        Database::insert('faqs', $f);
    }
    echo "FAQs seeded.\n";
}

if ($isEmpty('posts')) {
    foreach (require __DIR__ . '/seeds/posts.php' as $p) {
        $p['body'] = HtmlSanitizer::clean($p['body']);
        $p['status'] = 'published';
        Database::insert('posts', $p);
    }
    echo "Blog posts seeded.\n";
}

echo "Seeding complete. Projects and reviews are intentionally empty — add real ones in the admin.\n";
