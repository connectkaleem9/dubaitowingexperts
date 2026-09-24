<?php
/** @var array $fields @var array $values */
use Admin\Controllers\SettingsController;

$c = new SettingsController();
?>
<form class="a-form a-card" method="post" action="/admin/settings/">
    <?= csrf_field() ?>
    <p class="a-muted">Business name, phone number and WhatsApp number are fixed in the site configuration so they stay identical everywhere (important for local SEO). Ask your developer to change them.</p>
    <dl class="a-dl">
        <dt>Business name</dt><dd><?= e(business('name')) ?></dd>
        <dt>Phone</dt><dd><?= e(business('phone_display')) ?> (<?= e(business('phone_e164')) ?>)</dd>
        <dt>WhatsApp</dt><dd><?= e(business('whatsapp')) ?></dd>
    </dl>
    <?php foreach ($fields as $key => [$label, $type, $hint, $rule]): $name = $c->inputName($key); $value = $values[$key] ?? ''; ?>
        <?php if ($type === 'media'): ?>
            <?php
            $options = [];
            foreach ($library as $m) {
                $options[(int) $m['id']] = '#' . (int) $m['id'] . ' ' . str_limit((string) ($m['alt_text'] ?: $m['original_name']), 60);
            }
            ?>
            <?= af_select($name, $label, $value, $options, '— none —') ?>
            <p class="a-muted"><small><?= e($hint) ?></small></p>
        <?php elseif ($type === 'textarea'): ?>
            <?= af_textarea($name, $label, $value, 3, $hint) ?>
        <?php elseif ($type === 'select' && $key === 'consent_default'): ?>
            <?= af_select($name, $label, $value ?: 'denied', ['denied' => 'Ask first (show cookie banner)', 'granted' => 'On by default (no banner)']) ?>
            <p class="a-muted"><small><?= e($hint) ?></small></p>
        <?php else: ?>
            <?= af_input($name, $label, $value, ['maxlength' => 255], $hint) ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save settings</button>
    </div>
</form>
