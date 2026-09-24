<?php
/** @var array $service — image slot falls back to a branded placeholder until real photos exist. */
$headingTag = $headingTag ?? 'h3';
$image = $image ?? null;
// The photo band only renders when a real image exists — an empty placeholder on every card
// would dominate the page (the design assumes photography the owner has not supplied yet).
?>
<article class="card">
    <?php if ($image): ?>
        <div class="card__media"><?= media_img($image, '(min-width: 1100px) 300px, (min-width: 560px) 45vw, 100vw') ?></div>
    <?php else: ?>
        <span class="card__icon"><?= icon($service['icon'] ?: 'truck') ?></span>
    <?php endif; ?>
    <div class="card__head">
        <?php if ($image): ?><?= icon($service['icon'] ?: 'truck') ?><?php endif; ?>
        <<?= $headingTag ?>><?= e($service['name']) ?></<?= $headingTag ?>>
    </div>
    <p><?= e(str_limit($service['excerpt'], 88)) ?></p>
    <a class="card__link" href="/services/<?= e($service['slug']) ?>/">Learn More <?= icon('arrow') ?><span class="sr-only"> about <?= e($service['name']) ?></span></a>
</article>
