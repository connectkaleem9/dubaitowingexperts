<?php /** @var array $user @var list<array> $logins @var list<array> $failed @var list<array> $checks */ ?>
<div class="a-grid-2">
    <section class="a-card">
        <h2>Change your password</h2>
        <form class="a-form" method="post" action="/admin/security/password/">
            <?= csrf_field() ?>
            <?= af_input('current_password', 'Current password *', '', ['type' => 'password', 'required' => true, 'autocomplete' => 'current-password']) ?>
            <?= af_input('password', 'New password *', '', ['type' => 'password', 'required' => true, 'minlength' => 12, 'autocomplete' => 'new-password'], 'At least 12 characters. A passphrase of several words works well.') ?>
            <?= af_input('password_confirm', 'Confirm new password *', '', ['type' => 'password', 'required' => true, 'minlength' => 12, 'autocomplete' => 'new-password']) ?>
            <button class="a-btn a-btn--primary" type="submit">Change password</button>
        </form>
        <p class="a-muted">Signed in as <?= e($user['name']) ?> (<?= e($user['email']) ?>) · role <?= e($user['role']) ?></p>
    </section>

    <section class="a-card">
        <h2>Security checks</h2>
        <ul class="a-list">
            <?php foreach ($checks as [$label, $pass, $advice]): ?>
                <li><?= $pass ? '✅' : '⚠️' ?> <?= e($label) ?><?php if (!$pass): ?><br><small class="a-muted"><?= e($advice) ?></small><?php endif; ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

<div class="a-grid-2">
    <section class="a-card">
        <h2>Recent sign-ins</h2>
        <ul class="a-list">
            <?php foreach ($logins as $l): ?>
                <li><?= e(format_date($l['created_at'], 'j M Y, H:i')) ?> — <?= e($l['admin_name'] ?? 'unknown') ?> <span class="a-muted"><?= e($l['ip'] ?? '') ?></span></li>
            <?php endforeach; ?>
            <?php if ($logins === []): ?><li class="a-muted">Nothing recorded yet.</li><?php endif; ?>
        </ul>
    </section>
    <section class="a-card">
        <h2>Failed sign-in attempts</h2>
        <ul class="a-list">
            <?php foreach ($failed as $l): ?>
                <li><?= e(format_date($l['created_at'], 'j M Y, H:i')) ?> — <?= e($l['details'] ?? ($l['admin_name'] ?? 'unknown')) ?> <span class="a-muted"><?= e($l['ip'] ?? '') ?></span></li>
            <?php endforeach; ?>
            <?php if ($failed === []): ?><li class="a-muted">None.</li><?php endif; ?>
        </ul>
        <p class="a-muted">Accounts lock for 15 minutes after 5 failed attempts.</p>
    </section>
</div>
