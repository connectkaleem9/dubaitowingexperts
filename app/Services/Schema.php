<?php

declare(strict_types=1);

namespace App\Services;

/**
 * JSON-LD builders. Rules (docs/seo/schema-map.md):
 * - Only facts that are confirmed (no streetAddress / openingHours / ratings unless supplied).
 * - No self-serving Review/AggregateRating on the LocalBusiness (Google policy).
 */
final class Schema
{
    public static function businessId(): string
    {
        return url('/') . '#business';
    }

    public static function business(): array
    {
        $address = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => business('street_address'),
            'addressLocality' => business('city'),
            'addressRegion' => business('region'),
            'addressCountry' => business('country'),
        ]);
        $node = [
            '@type' => 'AutomotiveBusiness',
            '@id' => self::businessId(),
            'name' => business('name'),
            'url' => url('/'),
            'telephone' => business('phone_e164'),
            'image' => url('/assets/img/og-default.jpg'),
            'logo' => url('/assets/img/logo-header.webp'),
            'address' => $address,
            'areaServed' => ['@type' => 'City', 'name' => 'Dubai', 'sameAs' => 'https://en.wikipedia.org/wiki/Dubai'],
        ];
        if ($email = business('email')) {
            $node['email'] = $email;
        }
        if ($hours = business('opening_hours')) {
            $node['openingHours'] = $hours;
        }
        $social = array_values(array_filter(array_map('trim', explode("\n", (string) setting('business.social', '')))));
        if ($maps = business('google_maps_url')) {
            $social[] = $maps;
        }
        if ($social !== []) {
            $node['sameAs'] = $social;
        }
        return $node;
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => business('name'),
            'publisher' => ['@id' => self::businessId()],
            'inLanguage' => 'en-AE',
        ];
    }

    public static function webPage(Seo $seo): array
    {
        $node = [
            '@type' => $seo->pageType === 'faq' ? ['WebPage', 'FAQPage'] : ($seo->pageType === 'contact' ? 'ContactPage' : ($seo->pageType === 'about' ? 'AboutPage' : 'WebPage')),
            '@id' => $seo->canonical() . '#webpage',
            'url' => $seo->canonical(),
            'name' => $seo->title,
            'isPartOf' => ['@id' => url('/') . '#website'],
            'about' => ['@id' => self::businessId()],
            'inLanguage' => 'en-AE',
        ];
        if ($seo->description !== '') {
            $node['description'] = $seo->description;
        }
        if ($seo->path !== '/' && $seo->breadcrumbs !== []) {
            $node['breadcrumb'] = ['@id' => $seo->canonical() . '#breadcrumb'];
        }
        return $node;
    }

    public static function breadcrumbs(Seo $seo): array
    {
        $items = [['Home', '/']];
        foreach ($seo->breadcrumbs as $crumb) {
            $items[] = $crumb;
        }
        $list = [];
        foreach ($items as $i => [$name, $path]) {
            $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => url($path)];
        }
        return ['@type' => 'BreadcrumbList', '@id' => $seo->canonical() . '#breadcrumb', 'itemListElement' => $list];
    }

    public static function service(array $service, string $path, array $areas = []): array
    {
        $areaServed = [['@type' => 'City', 'name' => 'Dubai']];
        foreach ($areas as $a) {
            $areaServed[] = ['@type' => 'Place', 'name' => $a['name'] . ', Dubai'];
        }
        return [
            '@type' => 'Service',
            '@id' => url($path) . '#service',
            'name' => $service['name'],
            'serviceType' => $service['name'],
            'description' => $service['excerpt'],
            'url' => url($path),
            'provider' => ['@id' => self::businessId()],
            'areaServed' => $areaServed,
        ];
    }

    /** FAQ entities to merge into a FAQPage (only on /faq/, where the Q&A is the main content). */
    public static function faqEntities(array $faqs): array
    {
        return array_map(static fn (array $f): array => [
            '@type' => 'Question',
            'name' => $f['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags((string) $f['answer'], '<p><br><ul><ol><li><a><strong><em>')],
        ], $faqs);
    }

    public static function article(array $post, string $path, ?string $imageUrl): array
    {
        $node = [
            '@type' => 'Article',
            '@id' => url($path) . '#article',
            'headline' => $post['title'],
            'description' => $post['excerpt'],
            'datePublished' => date(DATE_ATOM, strtotime((string) $post['published_at'])),
            'dateModified' => date(DATE_ATOM, strtotime((string) $post['updated_at'])),
            'author' => ['@type' => 'Organization', 'name' => business('name'), 'url' => url('/')],
            'publisher' => ['@id' => self::businessId()],
            'mainEntityOfPage' => ['@id' => url($path) . '#webpage'],
        ];
        if ($imageUrl) {
            $node['image'] = $imageUrl;
        }
        return $node;
    }

    public static function encode(array $data): string
    {
        return (string) json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
