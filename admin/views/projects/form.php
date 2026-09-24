<?php
/**
 * @var array|null $project @var array $services @var array $areas
 * @var array|null $featured @var array|null $before @var array|null $after @var list<array> $gallery @var list<array> $library
 */
$p = $project ?? [];
$action = $project ? '/admin/projects/' . (int) $project['id'] . '/' : '/admin/projects/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="a-grid-2">
        <?= af_input('title', 'Title *', $p['title'] ?? '', ['required' => true, 'maxlength' => 160], 'Describe the job, e.g. "SUV recovered from a Business Bay basement to an Al Quoz garage".') ?>
        <?= af_input('slug', 'URL slug', $p['slug'] ?? '', ['maxlength' => 160, 'pattern' => '[a-z0-9]+(-[a-z0-9]+)*'], 'Leave blank to create from the title. Lowercase letters, numbers and hyphens.') ?>
    </div>
    <div class="a-grid-3">
        <?= af_select('service_id', 'Service', $p['service_id'] ?? '', $services, '— none —') ?>
        <?= af_select('area_id', 'Area page', $p['area_id'] ?? '', $areas, '— none —') ?>
        <?= af_input('location_text', 'Location text', $p['location_text'] ?? '', ['maxlength' => 160], 'Shown if no area page is chosen, e.g. "Sheikh Zayed Road near Al Barsha".') ?>
    </div>
    <div class="a-grid-3">
        <?= af_input('vehicle_type', 'Vehicle', $p['vehicle_type'] ?? '', ['maxlength' => 100], 'e.g. "Nissan Patrol" or "Sedan"') ?>
        <?= af_input('project_date', 'Date of job', $p['project_date'] ?? '', ['type' => 'date']) ?>
        <?= af_select('status', 'Status', $p['status'] ?? 'draft', ['draft' => 'Draft (hidden)', 'published' => 'Published']) ?>
    </div>
    <?= af_textarea('excerpt', 'Short summary', $p['excerpt'] ?? '', 2, 'One or two sentences. Used on project cards and as the search result description.') ?>
    <?= af_textarea('body', 'Full description', $p['body'] ?? '', 12, 'What happened, where, what you did, where the vehicle was taken. Only real details.', true) ?>

    <fieldset class="a-fieldset">
        <legend>Images</legend>
        <p class="a-muted">Upload JPG, PNG or WebP (max 8 MB). Images are resized, converted to WebP and stripped of location data automatically. Alt text is taken from the project title — you can edit it in the media library.</p>
        <div class="a-grid-3">
            <?= af_image('featured', 'Featured image', $featured, $library) ?>
            <?= af_image('before', 'Before image', $before, $library) ?>
            <?= af_image('after', 'After image', $after, $library) ?>
        </div>
    </fieldset>

    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save project</button>
        <a class="a-btn" href="/admin/projects/">Cancel</a>
    </div>
</form>

<?php if ($project): ?>
<section class="a-card" id="gallery">
    <h2>Gallery</h2>
    <?php if ($gallery !== []): ?>
        <div class="a-gallery">
            <?php foreach ($gallery as $g): ?>
                <figure>
                    <?= media_img($g, '200px') ?>
                    <figcaption><?= e($g['alt_text']) ?></figcaption>
                    <?= a_post_button('/admin/projects/' . (int) $project['id'] . '/gallery/' . (int) $g['link_id'] . '/delete/', 'Remove', 'a-btn a-btn--sm a-btn--danger', 'Remove this image from the gallery?') ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="a-muted">No gallery images yet.</p>
    <?php endif; ?>
    <form method="post" action="/admin/projects/<?= (int) $project['id'] ?>/gallery/" enctype="multipart/form-data" class="a-form">
        <?= csrf_field() ?>
        <div class="a-field">
            <label for="gallery-files">Add photos (up to 20 at a time)</label>
            <input id="gallery-files" type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp" multiple required>
        </div>
        <button class="a-btn" type="submit">Upload to gallery</button>
    </form>
</section>
<?php endif; ?>
