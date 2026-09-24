<?php /** @var list<array> $items */ ?>
<?php if ($items === []): ?>
    <div class="a-card"><p>No FAQs yet. <a href="/admin/faqs/new/">Add your first question</a> — answer only what you can truthfully answer.</p></div>
<?php else: ?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Question</th><th>Category</th><th>Shown on</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $f): ?>
            <tr>
                <td><a href="/admin/faqs/<?= (int) $f['id'] ?>/"><?= e($f['question']) ?></a></td>
                <td><?= e($f['category']) ?></td>
                <td class="a-muted">
                    <?= e($f['service_name'] ?? '') ?><?= $f['area_name'] ? e($f['area_name']) : '' ?>
                    <?= $f['show_on_home'] ? 'Home + FAQ page' : ($f['service_name'] || $f['area_name'] ? ' + FAQ page' : 'FAQ page') ?>
                </td>
                <td><?= a_status($f['is_published'] ? 'published' : 'draft') ?></td>
                <td class="a-row-actions">
                    <a class="a-btn a-btn--sm" href="/admin/faqs/<?= (int) $f['id'] ?>/">Edit</a>
                    <?= a_post_button('/admin/faqs/' . (int) $f['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this FAQ?') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
