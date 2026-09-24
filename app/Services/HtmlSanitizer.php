<?php

declare(strict_types=1);

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list sanitiser for rich text entered in the admin (service/area/project/blog bodies, FAQ answers).
 * Keeps structure tags and safe links only; strips everything else (scripts, styles, event handlers).
 */
final class HtmlSanitizer
{
    private const ALLOWED = [
        'p' => [], 'br' => [], 'h2' => ['id'], 'h3' => ['id'], 'h4' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [],
        'a' => ['href', 'title', 'rel', 'target'],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['scope'], 'td' => [],
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }
        // Plain text with no tags → wrap paragraphs.
        if (!preg_match('/<[a-z][\s\S]*>/i', $html)) {
            $paras = preg_split('/\R{2,}/', $html) ?: [];
            return implode("\n", array_map(static fn (string $p): string => '<p>' . nl2br(e(trim($p)), false) . '</p>', $paras));
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>', LIBXML_NONET | LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementById('__root');
        if ($root === null) {
            return '';
        }
        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        // Iterate over a static copy since we mutate children.
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg', 'math', 'template', 'noscript'], true)) {
                $node->removeChild($child);
                continue;
            }
            self::walk($child);
            if ($tag === 'h1') {
                // Only the page template may output an H1.
                $h2 = $child->ownerDocument->createElement('h2');
                while ($child->firstChild) {
                    $h2->appendChild($child->firstChild);
                }
                $node->replaceChild($h2, $child);
                continue;
            }
            if (!array_key_exists($tag, self::ALLOWED)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                if (!in_array(strtolower($attr->name), self::ALLOWED[$tag], true)) {
                    $child->removeAttribute($attr->name);
                }
            }
            if ($tag === 'a') {
                $href = trim($child->getAttribute('href'));
                if (!preg_match('#^(https?://|/|\#|tel:|mailto:)#i', $href)) {
                    $child->removeAttribute('href');
                }
                $isExternal = (bool) preg_match('#^https?://#i', $href) && !str_contains($href, (string) config('business.domain'));
                if ($isExternal) {
                    $child->setAttribute('rel', 'noopener noreferrer');
                    if ($child->getAttribute('target') !== '_blank') {
                        $child->removeAttribute('target');
                    }
                } else {
                    $child->removeAttribute('target');
                    $child->removeAttribute('rel');
                }
            }
        }
    }
}
