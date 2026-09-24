<?php
/** @var array $project @var array|null $image */
$image = $image ?? null;
$compact = $compact ?? false;
$headingTag = $headingTag ?? 'h3'; // h2 on the projects listing, where the cards follow the page H1
$location = $project['area_name'] ?? null ?: ($project['location_text'] ?? '');
?>
<article class="card<?= $compact ? ' card--flat' : '' ?>">
    <div class="card__media">
        <?php if ($image): ?>
            <?= media_img($image, $compact ? '(min-width: 1100px) 260px, 45vw' : '(min-width: 1100px) 380px, (min-width: 560px) 50vw, 100vw') ?>
        <?php else: ?>
            <span class="media-ph" aria-hidden="true"><?= icon('image') ?></span>
        <?php endif; ?>
    </div>
    <<?= $headingTag ?> class="h3"><?= e($project['title']) ?></<?= $headingTag ?>>
    <div class="card__meta">
        <?php if (!empty($project['service_name'])): ?><span><?= e($project['service_name']) ?></span><?php endif; ?>
        <?php if ($location !== ''): ?><span><?= e($location) ?></span><?php endif; ?>
        <?php if (!$compact && !empty($project['project_date'])): ?><time datetime="<?= e($project['project_date']) ?>"><?= e(format_date($project['project_date'], 'M Y')) ?></time><?php endif; ?>
    </div>
    <?php if (!$compact && $project['excerpt'] !== ''): ?><p><?= e($project['excerpt']) ?></p><?php endif; ?>
    <a class="card__link" href="/projects/<?= e($project['slug']) ?>/">View job <?= icon('arrow') ?></a>
</article>
