<?php
$heading = $heading ?? 'Need recovery now?';
$text = $text ?? 'Call or WhatsApp your location, any time of day or night. We confirm the price before we set off.';
$waMessage = $waMessage ?? null;
$location = $location ?? 'cta_band';
?>
<div class="cta-band">
    <div>
        <h2><?= e($heading) ?></h2>
        <p><?= e($text) ?></p>
    </div>
    <div class="btn-row">
        <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="<?= e($location) ?>"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
        <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($waMessage)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="<?= e($location) ?>"><?= icon('whatsapp') ?>WhatsApp us</a>
    </div>
</div>
