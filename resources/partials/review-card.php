<?php
/**
 * Review card for the reviews grid: quote mark, stars, the review itself, then the person.
 * @var array $review
 */
$photos = $photos ?? [];
// The carousel lays the same pieces out side by side (see .review--wide in site.css).
$wide = !empty($wide);
$initials = mb_strtoupper(mb_substr(trim((string) $review['name']), 0, 1));
$parts = preg_split('/\s+/', trim((string) $review['name'])) ?: [];
if (count($parts) > 1) {
    $initials .= mb_strtoupper(mb_substr(end($parts), 0, 1));
}
// Only the parts that exist, so the line never starts with a stray separator.
$meta = [];
if (!empty($review['service_name'])) {
    $meta[] = e($review['service_name']);
}
if (!empty($review['area_text'])) {
    $meta[] = e($review['area_text']);
}
if (!empty($review['approved_at'])) {
    $meta[] = '<time datetime="' . e(substr((string) $review['created_at'], 0, 10)) . '">'
        . e(format_date($review['created_at'], 'M Y')) . '</time>';
}
?>
<figure class="review<?= $wide ? ' review--wide' : '' ?>">
    <span class="review__quote" aria-hidden="true">&ldquo;</span>
    <?= partial('stars', ['rating' => $review['rating']]) ?>
    <blockquote><p><?= nl2br(e($review['body']), false) ?></p></blockquote>
    <?php if ($photos !== []): ?>
        <div class="review__photos">
            <?php foreach ($photos as $p): ?><?= media_img($p, '92px') ?><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <figcaption class="review__person">
        <span class="review__avatar" aria-hidden="true"><?= e($initials) ?></span>
        <span>
            <cite><?= e($review['name']) ?></cite>
            <?php if ($meta !== []): ?><span class="review__meta"><?= implode(' · ', $meta) ?></span><?php endif; ?>
        </span>
    </figcaption>
</figure>
