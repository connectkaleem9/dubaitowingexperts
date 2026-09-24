<?php
/**
 * Brand logo supplied by the owner (docs/design/reference/).
 * $href => link target (null renders a non-linked span); $variant => 'header' (navy) or 'footer' (white).
 */
$href = $href ?? '/';
$variant = $variant ?? 'header';
$tagName = $href === null ? 'span' : 'a';
$file = $variant === 'footer' ? '/assets/img/logo-footer.webp' : '/assets/img/logo-header.webp';
?>
<<?= $tagName ?> class="logo logo--<?= e($variant) ?>"<?= $href !== null ? ' href="' . e($href) . '"' : '' ?>>
    <img src="<?= e($file) ?>" width="760" height="<?= $variant === 'footer' ? 347 : 343 ?>" alt="<?= e(business('name')) ?> — car recovery and towing in Dubai"<?= $variant === 'header' ? ' fetchpriority="high"' : ' loading="lazy"' ?>>
</<?= $tagName ?>>
