<?php /** @var string|null $name */ ?>
<section class="section error-page">
    <div class="container prose mx-auto">
        <h1>Thank you<?= $name ? ', ' . e($name) : '' ?></h1>
        <p class="hero__lead muted">We have received your request and will contact you as soon as possible.</p>
        <p>If your situation is urgent, please call us now — it is the fastest way to get help.</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="thank_you"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="thank_you"><?= icon('whatsapp') ?>WhatsApp your location</a>
        </div>
        <p class="section__foot"><a href="/">Back to the home page</a></p>
    </div>
</section>
