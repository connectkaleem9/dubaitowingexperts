<?php
/** @var array $page @var array|null $service @var list<array> $services @var list<array> $reviews */
$wa = $page['whatsapp'];
?>
<section class="hero">
    <div class="container hero__grid hero__grid--media">
        <div>
            <h1><?= e($page['h1']) ?></h1>
            <p class="hero__lead"><?= e($page['lead']) ?></p>
            <div class="btn-row">
                <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="landing_hero"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
                <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($wa)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="landing_hero"><?= icon('whatsapp') ?>WhatsApp us</a>
            </div>
            <ul class="trust-list">
                <?php foreach ($page['points'] as $pt): ?><li><?= icon('check') ?><?= e($pt) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <div>
            <?= partial('lead-form', [
                'services' => $services,
                'selectedService' => $service ? (string) $service['id'] : '',
                'formType' => 'landing',
                'heading' => 'Get a call back with a price',
                'intro' => 'Takes 30 seconds. We call you back — or call us now for the fastest help.',
                'sourcePath' => current_path(),
                'compact' => true,
            ]) ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>How it works</h2>
        <ol class="steps steps--row">
            <?php foreach ($page['steps'] as $step): ?><li><h3><?= e($step) ?></h3></li><?php endforeach; ?>
        </ol>
    </div>
</section>

<?php if ($reviews !== []): ?>
<section class="section section--surface">
    <div class="container">
        <h2>What customers say</h2>
        <div class="grid grid--3">
            <?php foreach ($reviews as $r): ?><?= partial('review-card', ['review' => $r]) ?><?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <?= partial('cta-band', ['heading' => 'Ready when you are', 'text' => 'Call now or send your location on WhatsApp.', 'waMessage' => $wa, 'location' => 'landing_bottom']) ?>
    </div>
</section>
