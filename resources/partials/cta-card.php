<?php
/** Blue "Stuck on the Road?" card from the design. */
$heading = $heading ?? 'Stuck on the Road? We\'re Just a Call Away!';
$text = $text ?? '24/7 car recovery and towing services across Dubai. Tell us where you are and we will agree the price before we set off.';
$waMessage = $waMessage ?? null;
$location = $location ?? 'cta_card';
?>
<div class="cta-card">
    <span class="cta-card__sky" aria-hidden="true"><?= skyline() ?></span>
    <h2><?= e($heading) ?></h2>
    <p><?= e($text) ?></p>
    <div class="btn-row">
        <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="<?= e($location) ?>">
            <?= icon('phone') ?><span class="btn__stack">Call Now<small><?= e(business('phone_display')) ?></small></span>
        </a>
        <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($waMessage)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="<?= e($location) ?>">
            <?= icon('whatsapp') ?><span class="btn__stack">WhatsApp Now<small>Chat with us</small></span>
        </a>
    </div>
</div>
