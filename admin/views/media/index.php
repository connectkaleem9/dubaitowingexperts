<?php /** @var list<array> $items @var array $pager @var string $q */ ?>
<section class="a-card">
    <h2>Upload images</h2>
    <form class="a-form" method="post" action="/admin/media/upload/" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="a-grid-2">
            <div class="a-field">
                <label for="files">Images (JPG, PNG or WebP, max 8 MB each, up to 20)</label>
                <input id="files" type="file" name="files[]" accept="image/jpeg,image/png,image/webp" multiple required>
            </div>
            <?= af_input('alt_text', 'Describe the image (alt text)', '', ['maxlength' => 255], 'e.g. "Flatbed recovery truck loading a sedan in Dubai Marina". Used by screen readers and image search.') ?>
        </div>
        <button class="a-btn a-btn--primary" type="submit">Upload</button>
        <p class="a-muted">Images are converted to WebP at several sizes and stripped of camera location data automatically.</p>
    </form>
</section>

<form class="a-filters" method="get" action="/admin/media/">
    <label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Alt text or file name"></label>
    <button class="a-btn" type="submit">Search</button>
</form>

<?php if ($items === []): ?>
    <div class="a-card"><p class="a-muted">No images yet.</p></div>
<?php else: ?>
<div class="a-media-grid">
    <?php foreach ($items as $m): ?>
        <figure class="a-card a-media">
            <?= media_img($m, '240px') ?>
            <figcaption>
                <form class="a-form" method="post" action="/admin/media/<?= (int) $m['id'] ?>/">
                    <?= csrf_field() ?>
                    <input type="hidden" name="page" value="<?= (int) $pager['page'] ?>">
                    <label class="sr-only" for="alt-<?= (int) $m['id'] ?>">Alt text for image <?= (int) $m['id'] ?></label>
                    <input id="alt-<?= (int) $m['id'] ?>" name="alt_text" type="text" maxlength="255" value="<?= e($m['alt_text']) ?>" placeholder="Describe this image">
                    <div class="a-row-actions">
                        <button class="a-btn a-btn--sm" type="submit">Save alt text</button>
                    </div>
                </form>
                <?= a_post_button('/admin/media/' . (int) $m['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this image permanently?') ?>
                <small class="a-muted">#<?= (int) $m['id'] ?> · <?= (int) $m['width'] ?>×<?= (int) $m['height'] ?> · <?= e(round(((int) $m['size_bytes']) / 1024)) ?> KB · <?= e(format_date($m['created_at'])) ?></small>
            </figcaption>
        </figure>
    <?php endforeach; ?>
</div>
<?= a_pager($pager, '/admin/media/', ['q' => $q]) ?>
<?php endif; ?>
