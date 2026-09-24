<?php
/**
 * Distraction-free layout for Google Ads landing pages: logo + call button only, no main nav.
 * @var App\Services\Seo $seo @var string $content
 */
$waMessage = $waMessage ?? null;
?>
<!doctype html>
<html lang="en-AE" class="no-js">
<head>
<?= partial('head', ['seo' => $seo]) ?>
</head>
<body data-page-type="<?= e($seo->pageType) ?>">
<?= partial('analytics-body') ?>
<?= partial('icons') ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header landing-header">
    <div class="container site-header__bar">
        <span class="logo">
            <img class="logo__mark" src="/assets/img/logo.svg" width="40" height="40" alt="">
            <span class="logo__text">Dubai Recovery<small>Experts</small></span>
        </span>
        <div class="header-actions">
            <a class="btn btn--call" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="header"><?= icon('phone') ?><span class="btn__label"><?= e(business('phone_display')) ?></span><span class="sr-only"> – call now</span></a>
        </div>
    </div>
</header>
<main id="main" tabindex="-1">
<?= $content ?>
</main>
<footer class="site-footer">
    <div class="container footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= e(business('name')) ?> · <?= e(business('city')) ?>, <?= e(business('country_name')) ?> · <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a></span>
        <nav aria-label="Legal"><a href="/privacy-policy/">Privacy</a> · <a href="/terms-and-conditions/">Terms</a> · <a href="/cookie-policy/">Cookies</a> · <a href="/">Main website</a></nav>
    </div>
</footer>
<?= partial('mobile-cta', ['waMessage' => $waMessage]) ?>
<?= partial('consent') ?>
<script src="<?= e(asset('js/app.js')) ?>" defer nonce="<?= e(csp_nonce()) ?>"></script>
</body>
</html>
