<?php /** @var list<array> $services */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Car Recovery &amp; Towing Services in Dubai</h1>
        <p class="hero__lead">From a flat tyre on the highway to moving a car that won't start, choose the service that matches your situation — or just call and tell us what happened.</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero"><?= icon('whatsapp') ?>WhatsApp us</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($services === []): ?>
            <p>Our service pages are being updated. Please call <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a> and tell us what you need.</p>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($services as $s): ?>
                    <?= partial('service-card', ['service' => $s, 'headingTag' => 'h2']) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section--surface">
    <div class="container">
        <?= partial('cta-band', ['heading' => 'Not sure which service you need?', 'text' => 'Describe the problem on the phone or WhatsApp and we will tell you what to do next.']) ?>
    </div>
</section>
