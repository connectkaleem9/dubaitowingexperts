<?php
/** @var array $review */
$photos = $photos ?? [];
$initials = mb_strtoupper(mb_substr(trim((string) $review['name']), 0, 1));
$parts = preg_split('/\s+/', trim((string) $review['name'])) ?: [];
if (count($parts) > 1) {
    $initials .= mb_strtoupper(mb_substr(end($parts), 0, 1));
}
?>
<figure class="review">
    <div class="review__person">
        <span class="review__avatar" aria-hidden="true"><?= e($initials) ?></span>
        <div>
            <cite><?= e($review['name']) ?></cite>
            <?= partial('stars', ['rating' => $review['rating']]) ?>
        </div>
    </div>
    <blockquote><p><?= nl2br(e($review['body']), false) ?></p></blockquote>
    <?php if ($photos !== []): ?>
        <div class="review__photos">
            <?php foreach ($photos as $p): ?><?= media_img($p, '92px') ?><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php
    // Only join the parts that exist, so the caption never starts with a stray separator.
    $parts = [];
    if (!empty($review['service_name'])) {
        $parts[] = e($review['service_name']);
    }
    if (!empty($review['area_text'])) {
        $parts[] = e($review['area_text']);
    }
    if (!empty($review['approved_at'])) {
        $parts[] = '<time datetime="' . e(substr((string) $review['created_at'], 0, 10)) . '">' . e(format_date($review['created_at'], 'M Y')) . '</time>';
    }
    ?>
    <?php if ($parts !== []): ?><figcaption><?= implode(' · ', $parts) ?></figcaption><?php endif; ?>
</figure>
