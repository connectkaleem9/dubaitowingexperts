<?php
$heading = $heading ?? 'Get help now';
$waMessage = $waMessage ?? null;
?>
<div class="contact-box">
    <h2><?= e($heading) ?></h2>
    <p>Call or send your location on WhatsApp — 24 hours a day. We confirm the price and give you an arrival estimate before we dispatch.</p>
    <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="sidebar"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
    <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($waMessage)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="sidebar"><?= icon('whatsapp') ?>WhatsApp your location</a>
    <a class="btn btn--ghost" href="#lead-form">Request a call back</a>
</div>
