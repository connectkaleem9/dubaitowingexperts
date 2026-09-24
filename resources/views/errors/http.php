<?php /** @var int $status @var string $heading @var string $message */ ?>
<section class="section error-page">
    <div class="container">
        <p class="muted">Error <?= (int) $status ?></p>
        <h1><?= e($heading) ?></h1>
        <p><?= $message !== '' ? e($message) : ($status === 404 ? 'The page you are looking for does not exist or has moved.' : 'Something went wrong with that request.') ?></p>
        <?php if ($status === 404): ?>
            <p>Try one of these pages instead:</p>
            <ul class="chip-list chip-list--center">
                <li><a class="area-chip" href="/">Home</a></li>
                <li><a class="area-chip" href="/services/">Services</a></li>
                <li><a class="area-chip" href="/areas/">Areas</a></li>
                <li><a class="area-chip" href="/contact/">Contact</a></li>
            </ul>
        <?php endif; ?>
        <p class="section__foot">Need recovery now?</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="error_page"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="error_page"><?= icon('whatsapp') ?>WhatsApp us</a>
        </div>
    </div>
</section>
