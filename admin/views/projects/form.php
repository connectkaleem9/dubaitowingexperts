<?php
/**
 * Trimmed to what the owner asked for (2026-09-25): title, service, area, date and the two photos.
 * The URL slug is made from the title, and the card photo is taken from the "after" image.
 *
 * @var array|null $project @var array $services @var array $areas
 * @var array|null $before @var array|null $after
 */
$p = $project ?? [];
$action = $project ? '/admin/projects/' . (int) $project['id'] . '/' : '/admin/projects/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?= af_input('title', 'Title *', $p['title'] ?? '', ['required' => true, 'maxlength' => 160], 'Describe the job, e.g. "SUV recovered from a Business Bay basement to an Al Quoz garage".') ?>
    <div class="a-grid-3">
        <?= af_select('service_id', 'Service', $p['service_id'] ?? '', $services, '— none —') ?>
        <?= af_select('area_id', 'Area', $p['area_id'] ?? '', $areas, '— none —') ?>
        <?= af_input('project_date', 'Date of job', $p['project_date'] ?? '', ['type' => 'date']) ?>
    </div>
    <?= af_select('status', 'Status', $p['status'] ?? 'draft', ['draft' => 'Draft (hidden)', 'published' => 'Published']) ?>

    <fieldset class="a-fieldset">
        <legend>Photos</legend>
        <p class="a-muted">JPG, PNG or WebP (max 8 MB). Photos are resized, converted to WebP and stripped of location data automatically. The "after" photo is the one shown on the project card.</p>
        <div class="a-grid-2">
            <?= af_image('before', 'Before photo', $before) ?>
            <?= af_image('after', 'After photo', $after) ?>
        </div>
    </fieldset>

    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save project</button>
        <a class="a-btn" href="/admin/projects/">Cancel</a>
    </div>
</form>
