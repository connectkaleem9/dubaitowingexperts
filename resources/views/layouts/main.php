<?php
/** @var App\Services\Seo $seo */
/** @var string $content */
$conversion = $conversion ?? null;
$waMessage = $waMessage ?? null;
?>
<!doctype html>
<html lang="en-AE" class="no-js">
<head>
<?= partial('head', ['seo' => $seo]) ?>
</head>
<body data-page-type="<?= e($seo->pageType) ?>"<?= $conversion ? ' data-conversion="' . e($conversion) . '"' : '' ?>>
<?= partial('analytics-body') ?>
<?= partial('icons') ?>
<a class="skip-link" href="#main">Skip to content</a>
<?= partial('topbar') ?>
<?= partial('header', ['waMessage' => $waMessage]) ?>
<main id="main" tabindex="-1">
<?php if ($seo->path !== '/' && $seo->breadcrumbs !== []): ?>
<?= partial('breadcrumbs', ['seo' => $seo]) ?>
<?php endif; ?>
<?= $content ?>
</main>
<?= partial('footer') ?>
<?= partial('mobile-cta', ['waMessage' => $waMessage]) ?>
<?= partial('consent') ?>
<script src="<?= e(asset('js/app.js')) ?>" defer nonce="<?= e(csp_nonce()) ?>"></script>
</body>
</html>
