<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/** Builds sitemap.xml from static indexable pages + published DB content. Excludes noindex paths. */
final class Sitemap
{
    /** Static indexable pages (utility pages like /thank-you/ and /landing/* are excluded on purpose). */
    public const STATIC_PAGES = [
        '/' => '1.0',
        '/services/' => '0.9',
        '/areas/' => '0.8',
        '/projects/' => '0.7',
        '/reviews/' => '0.7',
        '/faq/' => '0.6',
        '/about/' => '0.5',
        '/contact/' => '0.8',
        '/blog/' => '0.5',
        '/privacy-policy/' => '0.2',
        '/terms-and-conditions/' => '0.2',
        '/cookie-policy/' => '0.2',
        '/disclaimer/' => '0.2',
    ];

    /** @return list<array{loc: string, lastmod: ?string, priority: string}> */
    public static function entries(): array
    {
        $noindex = array_column(
            Database::all("SELECT path FROM seo_metadata WHERE robots LIKE '%noindex%'"),
            'path'
        );
        // Listing pages are noindex while empty (see controllers), so leave them out too.
        $emptyListings = array_keys(array_filter([
            '/projects/' => !Database::value("SELECT 1 FROM projects WHERE status = 'published' LIMIT 1"),
            '/reviews/' => !Database::value("SELECT 1 FROM reviews WHERE status = 'approved' LIMIT 1"),
            '/blog/' => !Database::value("SELECT 1 FROM posts WHERE status = 'published' AND published_at <= NOW() LIMIT 1"),
            '/areas/' => !Database::value('SELECT 1 FROM areas WHERE is_published = 1 LIMIT 1'),
        ]));
        $entries = [];
        foreach (self::STATIC_PAGES as $path => $priority) {
            if (!in_array($path, $emptyListings, true)) {
                $entries[] = ['path' => $path, 'lastmod' => null, 'priority' => $priority];
            }
        }
        $sources = [
            ['SELECT slug, updated_at FROM services WHERE is_published = 1', '/services/%s/', '0.9'],
            ['SELECT slug, updated_at FROM areas WHERE is_published = 1', '/areas/%s/', '0.7'],
            // Only projects that carry a description: the rest are noindex, so listing them here
            // would advertise URLs we are asking Google not to index.
            ["SELECT slug, updated_at FROM projects WHERE status = 'published'"
                . " AND (COALESCE(excerpt, '') <> '' OR COALESCE(body, '') <> '')", '/projects/%s/', '0.5'],
            ["SELECT slug, updated_at FROM posts WHERE status = 'published' AND published_at <= NOW()", '/blog/%s/', '0.5'],
        ];
        foreach ($sources as [$sql, $pattern, $priority]) {
            foreach (Database::all($sql) as $row) {
                $entries[] = ['path' => sprintf($pattern, $row['slug']), 'lastmod' => $row['updated_at'], 'priority' => $priority];
            }
        }
        $out = [];
        foreach ($entries as $e) {
            if (in_array($e['path'], $noindex, true)) {
                continue;
            }
            $out[] = [
                'loc' => url($e['path']),
                'lastmod' => $e['lastmod'] ? date('Y-m-d', (int) strtotime((string) $e['lastmod'])) : null,
                'priority' => $e['priority'],
            ];
        }
        return $out;
    }

    public static function xml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (self::entries() as $e) {
            $xml .= "  <url>\n    <loc>" . htmlspecialchars($e['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if ($e['lastmod']) {
                $xml .= '    <lastmod>' . $e['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <priority>' . $e['priority'] . "</priority>\n  </url>\n";
        }
        return $xml . "</urlset>\n";
    }
}
