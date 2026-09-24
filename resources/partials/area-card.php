<?php
/**
 * Area tile — pin icon + place name only, no image (owner's instruction 2026-09-23).
 * Areas whose page is published link to it; the rest are shown as plain tiles so the full
 * coverage list is visible without creating thin pages or dead links.
 *
 * @var array $area
 */
$published = (bool) ($area['is_published'] ?? false);
?>
<?php if ($published): ?>
    <a class="area-card" href="/areas/<?= e($area['slug']) ?>/"><?= icon('pin') ?><span><?= e($area['name']) ?></span></a>
<?php else: ?>
    <span class="area-card area-card--static"><?= icon('pin') ?><span><?= e($area['name']) ?></span></span>
<?php endif; ?>
