<?php /** @var list<array> $items @var array $pager @var string $status @var string $q @var array $counts */ ?>
<form class="a-filters" method="get" action="/admin/projects/">
    <label>Status
        <select name="status">
            <option value="">All (<?= array_sum($counts) ?>)</option>
            <option value="published"<?= $status === 'published' ? ' selected' : '' ?>>Published (<?= (int) $counts['published'] ?>)</option>
            <option value="draft"<?= $status === 'draft' ? ' selected' : '' ?>>Draft (<?= (int) $counts['draft'] ?>)</option>
        </select>
    </label>
    <label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Title or location"></label>
    <button class="a-btn" type="submit">Filter</button>
</form>

<?php if ($items === []): ?>
    <div class="a-card"><p>No projects yet. <a href="/admin/projects/new/">Add your first real recovery job</a> with photos — it builds trust and gives visitors proof of your work.</p></div>
<?php else: ?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Title</th><th>Service</th><th>Location</th><th>Date</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $p): ?>
            <tr>
                <td><a href="/admin/projects/<?= (int) $p['id'] ?>/"><?= e($p['title']) ?></a></td>
                <td><?= e($p['service_name'] ?? '—') ?></td>
                <td><?= e($p['area_name'] ?? $p['location_text'] ?? '—') ?></td>
                <td><?= e(format_date($p['project_date'])) ?: '—' ?></td>
                <td><?= a_status($p['status']) ?></td>
                <td class="a-row-actions">
                    <?= a_post_button('/admin/projects/' . (int) $p['id'] . '/status/', $p['status'] === 'published' ? 'Unpublish' : 'Publish', 'a-btn a-btn--sm') ?>
                    <?= a_post_button('/admin/projects/' . (int) $p['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this project permanently?') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= a_pager($pager, '/admin/projects/', ['status' => $status, 'q' => $q]) ?>
<?php endif; ?>
