<?php /** @var list<array> $items */ ?>
<?php if ($items === []): ?>
    <div class="a-card"><p>No guides yet. Guides answer questions people search for before they need recovery — they bring visitors who later call you.</p></div>
<?php else: ?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Title</th><th>URL</th><th>Published</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $p): ?>
            <tr>
                <td><a href="/admin/posts/<?= (int) $p['id'] ?>/"><?= e($p['title']) ?></a></td>
                <td class="a-muted">/blog/<?= e($p['slug']) ?>/</td>
                <td><?= e(format_date($p['published_at'])) ?: '—' ?></td>
                <td><?= a_status($p['status']) ?></td>
                <td class="a-row-actions">
                    <a class="a-btn a-btn--sm" href="/admin/posts/<?= (int) $p['id'] ?>/">Edit</a>
                    <?= a_post_button('/admin/posts/' . (int) $p['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this guide?') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
