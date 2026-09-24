<?php
/** @var array|null $area @var array|null $image @var list<array> $library @var int $minWords */
$a = $area ?? [];
$action = $area ? '/admin/areas/' . (int) $area['id'] . '/' : '/admin/areas/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="a-alert a-alert--warn">To publish, the page needs at least <?= (int) $minWords ?> words of genuinely local content and a summary.</div>
    <div class="a-grid-3">
        <?= af_input('name', 'Area name *', $a['name'] ?? '', ['required' => true, 'maxlength' => 120]) ?>
        <?= af_input('slug', 'URL slug', $a['slug'] ?? '', ['maxlength' => 120]) ?>
        <?= af_input('sort_order', 'Sort order', $a['sort_order'] ?? 0, ['type' => 'number']) ?>
    </div>
    <?= af_input('h1', 'Page heading (H1) *', $a['h1'] ?? '', ['required' => true, 'maxlength' => 160], 'e.g. "Car Recovery in Dubai Marina".') ?>
    <?= af_textarea('excerpt', 'Summary', $a['excerpt'] ?? '', 2, 'Becomes the meta description — aim for 120–155 characters.') ?>
    <?= af_textarea('intro', 'Intro paragraph', $a['intro'] ?? '', 3) ?>
    <?= af_textarea('body', 'Page content', $a['body'] ?? '', 18, 'Local roads and highways, parking and basement access, landmarks, typical recovery situations, where cars are usually taken.', true) ?>
    <?= af_input('whatsapp_message', 'WhatsApp pre-filled message', $a['whatsapp_message'] ?? '', ['maxlength' => 300]) ?>
    <?= af_image('image', 'Area image', $image, $library) ?>
    <?= af_checkbox('is_published', 'Published (visible on the website)', (bool) ($a['is_published'] ?? false)) ?>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save area</button>
        <a class="a-btn" href="/admin/areas/">Cancel</a>
    </div>
</form>
