<?php
/** @var array $projects @var array $reviews @var array $leads @var int $leadsToday @var int $leadsWeek @var list<array> $recentLeads @var list<array> $setupWarnings */
use App\Models\Lead;
?>
<?php foreach ($setupWarnings as [$msg, $link]): ?>
    <div class="a-alert a-alert--warn"><?= e($msg) ?><?php if ($link): ?> <a href="<?= e($link) ?>">Fix now</a><?php endif; ?></div>
<?php endforeach; ?>

<section class="a-stats" aria-label="Summary">
    <a class="a-stat" href="/admin/leads/?status=new"><strong><?= (int) $leads['new'] ?></strong><span>New inquiries</span></a>
    <a class="a-stat" href="/admin/leads/"><strong><?= $leadsToday ?> / <?= $leadsWeek ?></strong><span>Leads today / last 7 days</span></a>
    <a class="a-stat" href="/admin/reviews/?status=pending"><strong><?= (int) $reviews['pending'] ?></strong><span>Pending reviews</span></a>
    <a class="a-stat" href="/admin/reviews/?status=approved"><strong><?= (int) $reviews['approved'] ?></strong><span>Published reviews</span></a>
    <a class="a-stat" href="/admin/projects/"><strong><?= array_sum($projects) ?></strong><span>Total projects</span></a>
    <a class="a-stat" href="/admin/projects/?status=published"><strong><?= (int) $projects['published'] ?></strong><span>Published projects</span></a>
</section>

<section class="a-card">
    <div class="a-card__head">
        <h2>Recent leads</h2>
        <a href="/admin/leads/">All contact requests →</a>
    </div>
    <?php if ($recentLeads === []): ?>
        <p class="a-muted">No enquiries yet. Leads from the website forms will appear here.</p>
    <?php else: ?>
        <div class="a-table-wrap">
            <table class="a-table">
                <thead><tr><th>Received</th><th>Name</th><th>Phone</th><th>Service</th><th>Location</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentLeads as $l): ?>
                    <tr>
                        <td><a href="/admin/leads/<?= (int) $l['id'] ?>/"><?= e(format_date($l['created_at'], 'j M, H:i')) ?></a></td>
                        <td><?= e($l['name']) ?></td>
                        <td><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $l['phone'])) ?>"><?= e($l['phone']) ?></a></td>
                        <td><?= e($l['service_name'] ?? $l['service_text'] ?? '—') ?></td>
                        <td><?= e(str_limit($l['location'], 40)) ?></td>
                        <td><?= a_status($l['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="a-grid-2">
    <div class="a-card">
        <h2>Lead pipeline</h2>
        <ul class="a-list">
            <?php foreach (Lead::STATUSES as $key => $label): ?>
                <li><a href="/admin/leads/?status=<?= e($key) ?>"><?= e($label) ?></a> <strong><?= (int) $leads[$key] ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="a-card">
        <h2>Quick actions</h2>
        <ul class="a-list">
            <li><a href="/admin/projects/new/">Add a project</a></li>
            <li><a href="/admin/reviews/?status=pending">Moderate reviews</a></li>
            <li><a href="/admin/faqs/new/">Add an FAQ</a></li>
            <li><a href="/admin/media/">Upload images</a></li>
        </ul>
    </div>
</section>
