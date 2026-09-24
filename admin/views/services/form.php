<?php
/** @var array|null $service @var array $icons @var array|null $image @var list<array> $library @var list<array> $areas @var list<int> $linkedAreas */
$s = $service ?? [];
$action = $service ? '/admin/services/' . (int) $service['id'] . '/' : '/admin/services/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="a-grid-3">
        <?= af_input('name', 'Service name *', $s['name'] ?? '', ['required' => true, 'maxlength' => 120], 'Used in menus and cards, e.g. "Car Recovery".') ?>
        <?= af_input('slug', 'URL slug', $s['slug'] ?? '', ['maxlength' => 120], 'Changing this changes the page URL — add a redirect if the page is live.') ?>
        <?= af_select('icon', 'Icon', $s['icon'] ?? 'truck', $icons) ?>
    </div>
    <?= af_input('h1', 'Page heading (H1) *', $s['h1'] ?? '', ['required' => true, 'maxlength' => 160], 'e.g. "Car Recovery in Dubai".') ?>
    <?= af_textarea('excerpt', 'Summary *', $s['excerpt'] ?? '', 2, 'Up to 300 characters. Used on service cards and as the meta description (aim for 120–155 characters).') ?>
    <?= af_textarea('intro', 'Intro paragraph', $s['intro'] ?? '', 3, 'Shown under the H1 at the top of the page.') ?>
    <?= af_textarea('body', 'Page content', $s['body'] ?? '', 18, 'Use H2 headings, short paragraphs and lists. Only describe what you really offer — no prices, response times or 24/7 claims unless they are true.', true) ?>
    <div class="a-grid-2">
        <?= af_input('whatsapp_message', 'WhatsApp pre-filled message', $s['whatsapp_message'] ?? '', ['maxlength' => 300], 'e.g. "Hello Dubai Towing Experts, I need car recovery. My location is: "') ?>
        <?= af_input('sort_order', 'Sort order', $s['sort_order'] ?? 0, ['type' => 'number']) ?>
    </div>
    <?= af_image('image', 'Service image', $image, $library, 'Shown next to the heading and used for social sharing.') ?>

    <fieldset class="a-fieldset">
        <legend>Areas that list this service</legend>
        <div class="a-checks">
            <?php foreach ($areas as $a): ?>
                <label><input type="checkbox" name="areas[]" value="<?= (int) $a['id'] ?>"<?= in_array((int) $a['id'], $linkedAreas, true) ? ' checked' : '' ?>>
                    <?= e($a['name']) ?><?= $a['is_published'] ? '' : ' (draft)' ?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <?= af_checkbox('is_published', 'Published (visible on the website)', (bool) ($s['is_published'] ?? false)) ?>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save service</button>
        <a class="a-btn" href="/admin/services/">Cancel</a>
    </div>
</form>
