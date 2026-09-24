<?php
/**
 * @var array $service @var array|null $image @var list<array> $areas @var list<array> $related
 * @var list<array> $faqs @var list<array> $reviews @var list<array> $projects @var array $projectImages
 */
$wa = $service['whatsapp_message'] ?: null;
?>
<?php // The service photo is the hero's background (see .hero--bg in site.css), not a card beside the copy. ?>
<section class="hero hero--compact<?= $image ? ' hero--bg' : '' ?>"<?= hero_bg_style($image) ?>>
    <div class="container hero__grid">
        <div>
            <h1><?= e($service['h1']) ?></h1>
            <?php if ($service['intro']): ?><p class="hero__lead"><?= e($service['intro']) ?></p><?php endif; ?>
            <div class="btn-row">
                <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero"><?= icon('phone') ?>Call now</a>
                <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($wa)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero"><?= icon('whatsapp') ?>WhatsApp us</a>
            </div>
            <p class="hero__phone">Or call <a href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero_number"><?= e(business('phone_display')) ?></a></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <article class="prose">
            <?= $service['body'] /* sanitised on save */ ?>

            <?php if ($areas !== []): ?>
                <h2>Where we provide <?= e(mb_strtolower($service['name'])) ?></h2>
                <p>We cover all of Dubai. These area pages include local notes on access, parking and main roads:</p>
                <ul class="chip-list">
                    <?php foreach ($areas as $a): ?>
                        <li><a class="area-chip" href="/areas/<?= e($a['slug']) ?>/"><?= icon('pin') ?><?= e($a['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
        <aside class="split__aside">
            <?= partial('contact-box', ['heading' => 'Need ' . mb_strtolower($service['name']) . '?', 'waMessage' => $wa]) ?>
        </aside>
    </div>
</section>

<?php if ($projects !== []): ?>
<section class="section section--surface" aria-labelledby="sp-h">
    <div class="container">
        <h2 id="sp-h">Recent <?= e(mb_strtolower($service['name'])) ?> jobs</h2>
        <div class="grid grid--3">
            <?php foreach ($projects as $p): ?>
                <?= partial('project-card', ['project' => $p, 'image' => $projectImages[(int) $p['featured_image_id']] ?? null]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($reviews !== []): ?>
<section class="section" aria-labelledby="sr-h">
    <div class="container">
        <h2 id="sr-h">Customer reviews</h2>
        <div class="grid grid--3">
            <?php foreach ($reviews as $r): ?><?= partial('review-card', ['review' => $r]) ?><?php endforeach; ?>
        </div>
        <p class="section__foot"><a href="/reviews/">All reviews</a></p>
    </div>
</section>
<?php endif; ?>

<?php if ($faqs !== []): ?>
<section class="section section--surface" aria-labelledby="sf-h">
    <div class="container prose">
        <h2 id="sf-h"><?= e($service['name']) ?> questions</h2>
        <?= partial('faq-list', ['faqs' => $faqs]) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($related !== []): ?>
<section class="section" aria-labelledby="rel-h">
    <div class="container">
        <h2 id="rel-h">Related services</h2>
        <div class="grid grid--4">
            <?php foreach ($related as $s): ?><?= partial('service-card', ['service' => $s]) ?><?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section--surface">
    <div class="container split">
        <?= partial('lead-form', ['services' => nav_services(), 'selectedService' => (string) $service['id'], 'formType' => 'quote', 'heading' => 'Get a quote for ' . mb_strtolower($service['name']), 'sourcePath' => '/services/' . $service['slug'] . '/']) ?>
    </div>
</section>
