<?php
/**
 * @var array $area @var array|null $image @var list<array> $services @var list<array> $faqs
 * @var list<array> $projects @var array $projectImages @var list<array> $reviews @var list<array> $nearby
 */
$wa = $area['whatsapp_message'] ?: 'Hello Dubai Towing Experts, I need recovery in ' . $area['name'] . '. My location is: ';
?>
<?php // The area photo is the hero's background (see .hero--bg in site.css), not a card beside the copy. ?>
<section class="hero hero--compact<?= $image ? ' hero--bg' : '' ?>"<?= hero_bg_style($image) ?>>
    <div class="container hero__grid">
        <div>
            <p class="eyebrow"><?= icon('pin') ?><?= e($area['name']) ?>, Dubai</p>
            <h1><?= e($area['h1']) ?></h1>
            <?php if ($area['intro']): ?><p class="hero__lead"><?= e($area['intro']) ?></p><?php endif; ?>
            <div class="btn-row">
                <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero"><?= icon('phone') ?>Call now</a>
                <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href($wa)) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero"><?= icon('whatsapp') ?>WhatsApp us</a>
                <button type="button" class="btn btn--ghost btn--lg" data-share-location data-wa="<?= e(whatsapp_href($wa)) ?>"><?= icon('pin') ?>Send my location</button>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <article class="prose">
            <?= $area['body'] /* sanitised on save */ ?>
        </article>
        <aside class="split__aside">
            <?= partial('contact-box', ['heading' => 'Recovery in ' . $area['name'], 'waMessage' => $wa]) ?>
        </aside>
    </div>
</section>

<?php if ($services !== []): ?>
<section class="section section--surface" aria-labelledby="as-h">
    <div class="container">
        <h2 id="as-h">Services available in <?= e($area['name']) ?></h2>
        <div class="grid grid--3">
            <?php foreach ($services as $s): ?><?= partial('service-card', ['service' => $s]) ?><?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($projects !== []): ?>
<section class="section" aria-labelledby="ap-h">
    <div class="container">
        <h2 id="ap-h">Recent jobs in <?= e($area['name']) ?></h2>
        <div class="grid grid--3">
            <?php foreach ($projects as $p): ?>
                <?= partial('project-card', ['project' => $p, 'image' => $projectImages[(int) $p['featured_image_id']] ?? null]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($faqs !== []): ?>
<section class="section section--surface" aria-labelledby="af-h">
    <div class="container prose">
        <h2 id="af-h">Questions about recovery in <?= e($area['name']) ?></h2>
        <?= partial('faq-list', ['faqs' => $faqs]) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($reviews !== []): ?>
<section class="section" aria-labelledby="ar-h">
    <div class="container">
        <h2 id="ar-h">Customer reviews</h2>
        <div class="grid grid--3">
            <?php foreach ($reviews as $r): ?><?= partial('review-card', ['review' => $r]) ?><?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section--surface">
    <div class="container split">
        <?= partial('lead-form', ['services' => nav_services(), 'formType' => 'quote', 'heading' => 'Request recovery in ' . $area['name'], 'sourcePath' => '/areas/' . $area['slug'] . '/']) ?>
        <?php if ($nearby !== []): ?>
            <nav class="split__aside" aria-labelledby="nb-h">
                <h2 id="nb-h" class="h3">Other areas we cover</h2>
                <ul class="chip-list">
                    <?php foreach ($nearby as $n): ?>
                        <li><a class="area-chip" href="/areas/<?= e($n['slug']) ?>/"><?= e($n['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>
