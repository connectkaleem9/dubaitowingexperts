<?php
/** @var list<array> $items @var array $pager @var string $status @var string $q @var array $counts */
use App\Models\Lead;
?>
<form class="a-filters" method="get" action="/admin/leads/">
    <label>Status
        <select name="status">
            <option value="">All (<?= array_sum($counts) ?>)</option>
            <?php foreach (Lead::STATUSES as $k => $label): ?>
                <option value="<?= e($k) ?>"<?= $status === $k ? ' selected' : '' ?>><?= e($label) ?> (<?= (int) $counts[$k] ?>)</option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Name, phone, location or message"></label>
    <button class="a-btn" type="submit">Filter</button>
</form>

<?php if ($items === []): ?>
    <div class="a-card"><p class="a-muted">No enquiries yet.</p></div>
<?php else: ?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Received</th><th>Name</th><th>Phone</th><th>Service</th><th>Location</th><th>Source</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($items as $l): ?>
            <tr>
                <td><a href="/admin/leads/<?= (int) $l['id'] ?>/"><?= e(format_date($l['created_at'], 'j M Y, H:i')) ?></a></td>
                <td><?= e($l['name']) ?></td>
                <td><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $l['phone'])) ?>"><?= e($l['phone']) ?></a>
                    · <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $l['phone'])) ?>" target="_blank" rel="noopener">WhatsApp</a></td>
                <td><?= e($l['service_name'] ?? $l['service_text'] ?? '—') ?></td>
                <td><?= e(str_limit($l['location'], 40)) ?></td>
                <td class="a-muted"><?= e($l['utm_source'] ?: ($l['gclid'] ? 'google ads' : 'direct')) ?></td>
                <td><?= a_status($l['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= a_pager($pager, '/admin/leads/', ['status' => $status, 'q' => $q]) ?>
<?php endif; ?>
