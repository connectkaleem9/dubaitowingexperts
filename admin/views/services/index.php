<?php /** @var list<array> $items */ ?>
<div class="a-card">
    <p class="a-muted">Only publish services you actually offer. Each service needs its own genuine content — never duplicate a page and change the name.</p>
</div>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Order</th><th>Name</th><th>URL</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $s): ?>
            <tr>
                <td><?= (int) $s['sort_order'] ?></td>
                <td><a href="/admin/services/<?= (int) $s['id'] ?>/"><?= e($s['name']) ?></a></td>
                <td class="a-muted">/services/<?= e($s['slug']) ?>/</td>
                <td><?= a_status($s['is_published'] ? 'published' : 'draft') ?></td>
                <td class="a-row-actions">
                    <a class="a-btn a-btn--sm" href="/admin/services/<?= (int) $s['id'] ?>/">Edit</a>
                    <?= a_post_button('/admin/services/' . (int) $s['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this service? Projects and reviews linked to it will keep working but lose the link.') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
