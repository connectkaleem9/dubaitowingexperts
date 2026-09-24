<?php /** @var list<array> $areas */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Areas We Cover in Dubai</h1>
        <p class="hero__lead">We recover and tow vehicles across Dubai — from residential communities and tower car parks to main roads and highways. Choose your area for local details.</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero"><?= icon('phone') ?>Call now</a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero"><?= icon('whatsapp') ?>WhatsApp your location</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($areas === []): ?>
            <p>Area pages are coming soon. We cover Dubai — call <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a> with your location.</p>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($areas as $a): ?>
                    <article class="card">
                        <span class="card__icon"><?= icon('pin') ?></span>
                        <h2 class="h3"><?= e($a['name']) ?></h2>
                        <p><?= e($a['excerpt']) ?></p>
                        <a class="card__link" href="/areas/<?= e($a['slug']) ?>/">Recovery in <?= e($a['name']) ?> <?= icon('arrow') ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($others)): ?>
            <div class="section__foot">
                <h2 class="h3">We also cover</h2>
                <div class="area-grid">
                    <?php foreach ($others as $a): ?><?= partial('area-card', ['area' => $a]) ?><?php endforeach; ?>
                </div>
                <p class="muted small">Don't see your area? We cover all of Dubai — call or WhatsApp your location and we will come to you.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
