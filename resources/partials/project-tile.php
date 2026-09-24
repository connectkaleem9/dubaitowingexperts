<?php
/**
 * Compact project tile used in the home-page projects slider (image + title + area/service).
 * @var array $project @var array|null $image
 */
$image = $image ?? null;
$meta = $project['area_name'] ?? null ?: ($project['location_text'] ?: ($project['service_name'] ?? ''));
?>
<a class="project-tile" href="/projects/<?= e($project['slug']) ?>/">
    <?php if ($image): ?>
        <?= media_img($image, '220px') ?>
    <?php else: ?>
        <span class="media-ph" aria-hidden="true"><?= icon('image') ?></span>
    <?php endif; ?>
    <span class="project-tile__title"><?= e(str_limit($project['title'], 40)) ?></span>
    <?php if ($meta !== ''): ?><span class="project-tile__meta"><?= e($meta) ?></span><?php endif; ?>
</a>
