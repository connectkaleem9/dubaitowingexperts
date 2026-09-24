<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use App\Models\SeoMeta;

/**
 * Per-page SEO data. Controllers build one with Seo::page(); admin overrides from
 * seo_metadata (matched by path) win over the controller defaults.
 */
final class Seo
{
    public string $ogType = 'website';
    public ?string $ogImage = null;
    /** @var list<array{0: string, 1: string}> [name, path] — Home is prepended automatically */
    public array $breadcrumbs = [];
    /** @var list<array<string, mixed>> Extra JSON-LD graph nodes */
    public array $schema = [];
    /** Page type exposed to analytics (home, service, area, project, landing, ...). */
    public string $pageType = 'page';
    public ?string $publishedTime = null;
    public ?string $modifiedTime = null;
    /**
     * LCP image to preload. A hero shown as a CSS background is only discovered once the
     * stylesheet has parsed, which delays the largest paint; preloading restores the head start
     * an <img fetchpriority="high"> would have had.
     */
    public ?string $preloadImage = null;

    private function __construct(
        public string $path,
        public string $title,
        public string $description,
        public string $robots = 'index,follow',
    ) {
    }

    public static function page(string $path, string $title, string $description, string $robots = 'index,follow'): self
    {
        $seo = new self($path, $title, $description, $robots);
        $override = SeoMeta::forPath($path);
        if ($override !== null) {
            $seo->title = $override['title'] ?: $seo->title;
            $seo->description = $override['meta_description'] ?: $seo->description;
            $seo->robots = $override['robots'] ?: $seo->robots;
            if ($override['og_image_id']) {
                $seo->ogImage = media_url(Media::find((int) $override['og_image_id']), 1600);
            }
        }
        return $seo;
    }

    /**
     * Appends " | Dubai Towing Experts" only when the result still fits in ~60 characters,
     * so long content titles are not truncated by Google mid-sentence.
     */
    public static function withBrand(string $title, int $limit = 60): string
    {
        $suffix = ' | ' . config('business.name');
        return mb_strlen($title . $suffix) <= $limit ? $title . $suffix : str_limit($title, $limit);
    }

    /** Minimal instance for error/utility pages that must not be indexed. */
    public static function simple(string $title, string $path, string $robots = 'noindex,follow'): self
    {
        return new self($path, $title . ' | ' . config('business.name'), '', $robots);
    }

    public function canonical(): string
    {
        return url($this->path);
    }

    public function isIndexable(): bool
    {
        return !str_contains($this->robots, 'noindex') && !config('app.force_noindex');
    }

    public function ogImageUrl(): string
    {
        return $this->ogImage ?? url(setting('default_og_image', '/assets/img/og-default.jpg'));
    }

    public function crumbs(string $name, string $path): self
    {
        $this->breadcrumbs[] = [$name, $path];
        return $this;
    }

    public function type(string $pageType): self
    {
        $this->pageType = $pageType;
        return $this;
    }

    /** @param array<string, mixed> $node */
    public function addSchema(array $node): self
    {
        $this->schema[] = $node;
        return $this;
    }

    /** Full JSON-LD @graph for the page. */
    public function jsonLd(): string
    {
        $graph = [Schema::business(), Schema::website(), Schema::webPage($this)];
        if ($this->path !== '/' && $this->breadcrumbs !== []) {
            $graph[] = Schema::breadcrumbs($this);
        }
        foreach ($this->schema as $node) {
            $graph[] = $node;
        }
        return Schema::encode(['@context' => 'https://schema.org', '@graph' => $graph]);
    }
}
