<?php
/** @var App\Services\Seo $seo */
$robots = config('app.force_noindex') ? 'noindex,nofollow' : $seo->robots;
?>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="<?= e(implode('; ', App\Middleware\SecurityHeaders::cspDirectives(csp_nonce()))) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($seo->title) ?></title>
<?php if ($seo->description !== ''): ?>
<meta name="description" content="<?= e($seo->description) ?>">
<?php endif; ?>
<meta name="robots" content="<?= e($robots) ?>">
<?php if ($seo->isIndexable()): ?>
<link rel="canonical" href="<?= e($seo->canonical()) ?>">
<?php endif; ?>
<meta property="og:type" content="<?= e($seo->ogType) ?>">
<meta property="og:site_name" content="<?= e(business('name')) ?>">
<meta property="og:title" content="<?= e($seo->title) ?>">
<?php if ($seo->description !== ''): ?>
<meta property="og:description" content="<?= e($seo->description) ?>">
<?php endif; ?>
<meta property="og:url" content="<?= e($seo->canonical()) ?>">
<meta property="og:image" content="<?= e($seo->ogImageUrl()) ?>">
<meta property="og:locale" content="en_AE">
<?php if ($seo->publishedTime): ?>
<meta property="article:published_time" content="<?= e(date(DATE_ATOM, (int) strtotime($seo->publishedTime))) ?>">
<?php endif; ?>
<?php if ($seo->modifiedTime): ?>
<meta property="article:modified_time" content="<?= e(date(DATE_ATOM, (int) strtotime($seo->modifiedTime))) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#0B2545">
<meta name="format-detection" content="telephone=no">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="alternate icon" href="/favicon.ico" sizes="16x16 32x32 48x48">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="preload" href="/assets/fonts/poppins-700.woff2" as="font" type="font/woff2" crossorigin>
<?php if ($seo->preloadImage): ?>
<link rel="preload" href="<?= e($seo->preloadImage) ?>" as="image" fetchpriority="high">
<?php endif; ?>
<link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<script nonce="<?= e(csp_nonce()) ?>">document.documentElement.className=document.documentElement.className.replace('no-js','js');</script>
<script type="application/ld+json"><?= $seo->jsonLd() ?></script>
<?= partial('analytics-head', ['seo' => $seo]) ?>
