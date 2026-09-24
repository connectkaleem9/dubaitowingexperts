<?php
/** @var list<array> $items @var array $images @var array $pager @var string $status @var string $q @var int $rating @var array $counts */
$back = current_path() . (($qs = http_build_query(array_filter(['status' => $status, 'q' => $q, 'rating' => $rating ?: null, 'page' => $pager['page'] > 1 ? $pager['page'] : null]))) ? '?' . $qs : '');
?>
<form class="a-filters" method="get" action="/admin/reviews/">
    <label>Status
        <select name="status">
            <option value="">All (<?= array_sum($counts) ?>)</option>
            <?php foreach ($counts as $k => $c): ?>
                <option value="<?= e($k) ?>"<?= $status === $k ? ' selected' : '' ?>><?= e(ucfirst($k)) ?> (<?= (int) $c ?>)</option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Rating
        <select name="rating">
            <option value="">Any</option>
            <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"<?= $rating === $i ? ' selected' : '' ?>><?= $i ?> ★</option><?php endfor; ?>
        </select>
    </label>
    <label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Name, email or text"></label>
    <button class="a-btn" type="submit">Filter</button>
</form>

<?php if ($items === []): ?>
    <div class="a-card"><p class="a-muted">No reviews match.</p></div>
<?php endif; ?>

<?php foreach ($items as $r): ?>
    <article class="a-card a-review">
        <header class="a-review__head">
            <div>
                <strong><?= e($r['name']) ?></strong> · <?= str_repeat('★', (int) $r['rating']) . str_repeat('☆', 5 - (int) $r['rating']) ?>
                <?= a_status($r['status']) ?><?php if ($r['is_featured']): ?> <span class="a-badge a-badge--featured">Featured</span><?php endif; ?>
                <div class="a-muted"><?= e(format_date($r['created_at'], 'j M Y, H:i')) ?>
                    <?php if ($r['service_name']): ?> · <?= e($r['service_name']) ?><?php endif; ?>
                    <?php if ($r['area_text']): ?> · <?= e($r['area_text']) ?><?php endif; ?>
                    <?php if ($r['email']): ?> · <?= e($r['email']) ?><?php endif; ?>
                    <?php if ($r['phone']): ?> · <?= e($r['phone']) ?><?php endif; ?>
                </div>
            </div>
            <a class="a-btn a-btn--sm" href="/admin/reviews/<?= (int) $r['id'] ?>/">Edit</a>
        </header>
        <p><?= nl2br(e($r['body']), false) ?></p>
        <?php if (!empty($images[(int) $r['id']])): ?>
            <div class="a-gallery a-gallery--sm"><?php foreach ($images[(int) $r['id']] as $img): ?><?= media_img($img, '120px') ?><?php endforeach; ?></div>
        <?php endif; ?>
        <div class="a-row-actions">
            <?php if ($r['status'] !== 'approved'): ?><?= a_post_button('/admin/reviews/' . (int) $r['id'] . '/moderate/', 'Approve', 'a-btn a-btn--sm a-btn--ok', '', ['action' => 'approve', 'back' => $back]) ?><?php endif; ?>
            <?php if ($r['status'] !== 'rejected'): ?><?= a_post_button('/admin/reviews/' . (int) $r['id'] . '/moderate/', 'Reject', 'a-btn a-btn--sm', '', ['action' => 'reject', 'back' => $back]) ?><?php endif; ?>
            <?php if ($r['status'] === 'approved'): ?><?= a_post_button('/admin/reviews/' . (int) $r['id'] . '/moderate/', 'Hide', 'a-btn a-btn--sm', '', ['action' => 'hide', 'back' => $back]) ?><?php endif; ?>
            <?php if ($r['status'] === 'approved'): ?><?= a_post_button('/admin/reviews/' . (int) $r['id'] . '/moderate/', $r['is_featured'] ? 'Unfeature' : 'Feature', 'a-btn a-btn--sm', '', ['action' => $r['is_featured'] ? 'unfeature' : 'feature', 'back' => $back]) ?><?php endif; ?>
            <?= a_post_button('/admin/reviews/' . (int) $r['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this review permanently?') ?>
        </div>
    </article>
<?php endforeach; ?>
<?= a_pager($pager, '/admin/reviews/', ['status' => $status, 'q' => $q, 'rating' => $rating ?: null]) ?>
