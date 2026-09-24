<?php
/** @var array|null $post @var array|null $image @var list<array> $library */
$p = $post ?? [];
$action = $post ? '/admin/posts/' . (int) $post['id'] . '/' : '/admin/posts/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="a-grid-2">
        <?= af_input('title', 'Title *', $p['title'] ?? '', ['required' => true, 'maxlength' => 160]) ?>
        <?= af_input('slug', 'URL slug', $p['slug'] ?? '', ['maxlength' => 160]) ?>
    </div>
    <?= af_textarea('excerpt', 'Summary *', $p['excerpt'] ?? '', 2, 'Used on the blog list and as the meta description (aim for 120–155 characters).') ?>
    <?= af_textarea('body', 'Content *', $p['body'] ?? '', 22, 'Write for a driver who needs help. Use H2 headings and short paragraphs. Link to the relevant service page.', true) ?>
    <div class="a-grid-3">
        <?= af_select('status', 'Status', $p['status'] ?? 'draft', ['draft' => 'Draft', 'published' => 'Published']) ?>
        <?= af_input('published_at', 'Publish date/time', isset($p['published_at']) && $p['published_at'] ? substr((string) $p['published_at'], 0, 16) : '', ['type' => 'datetime-local'], 'Leave blank to publish now.') ?>
    </div>
    <?= af_image('image', 'Featured image', $image, $library) ?>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save guide</button>
        <a class="a-btn" href="/admin/posts/">Cancel</a>
    </div>
</form>
