<?php $waMessage = $waMessage ?? null; ?>
<div class="mobile-cta" role="region" aria-label="Quick contact">
    <a class="btn btn--call" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="sticky_bar"><?= icon('phone') ?>Call now</a>
    <a class="btn btn--wa" href="<?= e(whatsapp_href($waMessage)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="sticky_bar"><?= icon('whatsapp') ?>WhatsApp</a>
</div>
