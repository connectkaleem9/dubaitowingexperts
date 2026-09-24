<?php /** @var list<array> $items @var array $pager @var string $action @var list<string> $actionList */ ?>
<form class="a-filters" method="get" action="/admin/activity/">
    <label>Action
        <select name="action">
            <option value="">All</option>
            <?php foreach ($actionList as $a): ?><option value="<?= e($a) ?>"<?= $action === $a ? ' selected' : '' ?>><?= e($a) ?></option><?php endforeach; ?>
        </select>
    </label>
    <button class="a-btn" type="submit">Filter</button>
</form>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Item</th><th>Details</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($items as $l): ?>
            <tr>
                <td><?= e(format_date($l['created_at'], 'j M Y, H:i:s')) ?></td>
                <td><?= e($l['admin_name'] ?? '—') ?></td>
                <td><?= e($l['action']) ?></td>
                <td class="a-muted"><?= e(trim(($l['entity_type'] ?? '') . ' ' . ($l['entity_id'] ? '#' . $l['entity_id'] : ''))) ?: '—' ?></td>
                <td class="a-muted"><?= e(str_limit((string) $l['details'], 60)) ?: '—' ?></td>
                <td class="a-muted"><?= e($l['ip'] ?? '—') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= a_pager($pager, '/admin/activity/', ['action' => $action]) ?>
