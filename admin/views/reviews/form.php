<?php
/** @var array|null $review @var array $services */
$r = $review ?? [];
$images = $images ?? [];
$action = $review ? '/admin/reviews/' . (int) $review['id'] . '/' : '/admin/reviews/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>">
    <?= csrf_field() ?>
    <?php if (!$review): ?>
        <div class="a-alert a-alert--warn">Only add reviews that a real customer gave you (for example by WhatsApp or in person) and agreed you may publish. Never write reviews yourself.</div>
    <?php else: ?>
        <p class="a-muted">Submitted <?= e(format_date($r['created_at'], 'j M Y, H:i')) ?>
            <?php if ($r['email']): ?> · <?= e($r['email']) ?><?php endif; ?>
            <?php if ($r['phone']): ?> · <?= e($r['phone']) ?><?php endif; ?>
            · consent given <?= e(format_date($r['consent_at'], 'j M Y')) ?></p>
        <p class="a-muted">Edit only to remove personal data or offensive words — don't change the customer's opinion.</p>
    <?php endif; ?>
    <div class="a-grid-3">
        <?= af_input('name', 'Customer name *', $r['name'] ?? '', ['required' => true, 'maxlength' => 100]) ?>
        <?= af_select('rating', 'Rating *', $r['rating'] ?? '', [5 => '5 ★', 4 => '4 ★', 3 => '3 ★', 2 => '2 ★', 1 => '1 ★'], '— choose —') ?>
        <?= af_select('service_id', 'Service', $r['service_id'] ?? '', $services, '— none —') ?>
    </div>
    <?= af_textarea('body', 'Review text *', $r['body'] ?? '', 6) ?>
    <div class="a-grid-3">
        <?= af_input('area_text', 'Area', $r['area_text'] ?? '', ['maxlength' => 120]) ?>
        <?= af_select('status', 'Status', $r['status'] ?? 'approved', ['pending' => 'Pending', 'approved' => 'Approved (published)', 'rejected' => 'Rejected', 'hidden' => 'Hidden']) ?>
        <?= af_checkbox('is_featured', 'Featured (shown first, on the home page)', (bool) ($r['is_featured'] ?? false)) ?>
    </div>
    <?= af_input('admin_note', 'Internal note', $r['admin_note'] ?? '', ['maxlength' => 500], 'Never shown publicly.') ?>
    <?php if (!$review): ?>
        <div class="a-field a-check<?= isset(errors()['genuine']) ? ' has-error' : '' ?>">
            <label><input type="checkbox" name="genuine" value="1"> I confirm this is a genuine review from a real customer, who agreed to it being published.</label>
            <?= af_error('genuine') ?>
        </div>
    <?php endif; ?>
    <?php if ($images !== []): ?>
        <div class="a-gallery a-gallery--sm"><?php foreach ($images as $img): ?><?= media_img($img, '120px') ?><?php endforeach; ?></div>
    <?php endif; ?>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save review</button>
        <a class="a-btn" href="/admin/reviews/">Cancel</a>
    </div>
</form>
