<?php /** @var list<array> $services @var list<array> $areas @var list<array> $faqs */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Contact Dubai Towing Experts</h1>
        <p class="hero__lead">The fastest way to get help is to call. You can also WhatsApp your location pin, or send the form and we will call you back.</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero"><?= icon('whatsapp') ?>WhatsApp us</a>
            <button type="button" class="btn btn--ghost btn--lg" data-share-location data-wa="<?= e(whatsapp_href()) ?>"><?= icon('pin') ?>Send my location</button>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <?= partial('lead-form', ['services' => $services, 'formType' => 'contact', 'sourcePath' => '/contact/']) ?>
        <aside class="split__aside">
            <div class="form--card form">
                <h2 class="h3">Contact details</h2>
                <address class="footer-nap">
                    <strong><?= e(business('name')) ?></strong>
                    <?php if (business('street_address')): ?><span><?= e(business('street_address')) ?></span><?php endif; ?>
                    <span><?= e(business('city')) ?>, <?= e(business('country_name')) ?></span>
                    <span>Phone: <a href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="contact_details"><?= e(business('phone_display')) ?></a></span>
                    <span>WhatsApp: <a href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="contact_details"><?= e(business('phone_display')) ?></a></span>
                    <?php if (business('email')): ?><span>Email: <a href="mailto:<?= e(business('email')) ?>"><?= e(business('email')) ?></a></span><?php endif; ?>
                    <?php if (business('opening_hours')): ?><span>Hours: <?= e(business('opening_hours')) ?></span><?php endif; ?>
                </address>
                <?php if ($areas !== []): ?>
                    <h2 class="h3">Areas we cover</h2>
                    <ul class="chip-list">
                        <?php foreach ($areas as $a): ?><li><a class="area-chip" href="/areas/<?= e($a['slug']) ?>/"><?= e($a['name']) ?></a></li><?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="muted">We cover Dubai. Tell us your location when you call.</p>
                <?php endif; ?>
                <?php if (business('google_maps_url')): ?>
                    <p><a href="<?= e(business('google_maps_url')) ?>" target="_blank" rel="noopener">View us on Google Maps</a></p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>

<?php if ($faqs !== []): ?>
<section class="section section--surface" aria-labelledby="cf-h">
    <div class="container prose">
        <h2 id="cf-h">Before you call</h2>
        <?= partial('faq-list', ['faqs' => $faqs]) ?>
    </div>
</section>
<?php endif; ?>
