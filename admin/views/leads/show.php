<?php
/** @var array $lead */
use App\Core\Auth;
use App\Models\Lead;

$phone = preg_replace('/[^\d+]/', '', (string) $lead['phone']);
$wa = preg_replace('/\D/', '', (string) $lead['phone']);
?>
<div class="a-grid-2">
    <section class="a-card">
        <h2>Enquiry details</h2>
        <dl class="a-dl">
            <dt>Received</dt><dd><?= e(format_date($lead['created_at'], 'j M Y, H:i')) ?></dd>
            <dt>Name</dt><dd><?= e($lead['name']) ?></dd>
            <dt>Phone</dt><dd><a href="tel:<?= e($phone) ?>"><?= e($lead['phone']) ?></a> · <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a></dd>
            <?php if ($lead['email']): ?><dt>Email</dt><dd><a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a></dd><?php endif; ?>
            <dt>Preferred contact</dt><dd><?= e(ucfirst($lead['preferred_contact'])) ?></dd>
            <dt>Service</dt><dd><?= e($lead['service_name'] ?? $lead['service_text'] ?? '—') ?></dd>
            <dt>Location</dt><dd><?= e($lead['location']) ?> · <a href="https://maps.google.com/?q=<?= e(rawurlencode($lead['location'] . ', Dubai')) ?>" target="_blank" rel="noopener">Map</a></dd>
            <dt>Vehicle</dt><dd><?= e($lead['vehicle_type'] ?: '—') ?></dd>
            <dt>Message</dt><dd><?= $lead['message'] ? nl2br(e($lead['message']), false) : '—' ?></dd>
        </dl>
    </section>
    <section class="a-card">
        <h2>Status &amp; notes</h2>
        <form class="a-form" method="post" action="/admin/leads/<?= (int) $lead['id'] ?>/">
            <?= csrf_field() ?>
            <?= af_select('status', 'Status', $lead['status'], Lead::STATUSES) ?>
            <?= af_textarea('admin_notes', 'Internal notes', $lead['admin_notes'] ?? '', 8, 'Never shown publicly.') ?>
            <div class="a-form__actions">
                <button class="a-btn a-btn--primary" type="submit">Save</button>
                <a class="a-btn" href="/admin/leads/">Back to list</a>
                <?php if (Auth::isOwner()): ?>
                    <?= a_post_button('/admin/leads/' . (int) $lead['id'] . '/delete/', 'Delete enquiry', 'a-btn a-btn--danger', 'Delete this enquiry permanently?') ?>
                <?php endif; ?>
            </div>
        </form>
        <h3>Where it came from</h3>
        <dl class="a-dl a-muted">
            <dt>Page</dt><dd><?= e($lead['source_path'] ?: '—') ?></dd>
            <dt>Form</dt><dd><?= e($lead['form_type']) ?></dd>
            <dt>Campaign</dt><dd><?= e(trim(($lead['utm_source'] ?? '') . ' ' . ($lead['utm_medium'] ?? '') . ' ' . ($lead['utm_campaign'] ?? '')) ?: '—') ?></dd>
            <dt>GCLID</dt><dd><?= e($lead['gclid'] ?: '—') ?></dd>
        </dl>
    </section>
</div>
