<?php
/** @var array|null $faq @var array $services @var array $areas */
$f = $faq ?? [];
$action = $faq ? '/admin/faqs/' . (int) $faq['id'] . '/' : '/admin/faqs/new/';
?>
<form class="a-form a-card" method="post" action="<?= e($action) ?>">
    <?= csrf_field() ?>
    <?= af_input('question', 'Question *', $f['question'] ?? '', ['required' => true, 'maxlength' => 255], 'Write it the way a customer would ask it.') ?>
    <?= af_textarea('answer', 'Answer *', $f['answer'] ?? '', 6, 'Be specific and honest. Do not promise prices, arrival times or 24/7 availability unless they are true.', true) ?>
    <div class="a-grid-3">
        <?= af_input('category', 'Category *', $f['category'] ?? 'Getting help', ['required' => true, 'maxlength' => 60], 'Groups questions on the FAQ page.') ?>
        <?= af_select('service_id', 'Also show on service page', $f['service_id'] ?? '', $services, '— none —') ?>
        <?= af_select('area_id', 'Also show on area page', $f['area_id'] ?? '', $areas, '— none —') ?>
    </div>
    <div class="a-grid-3">
        <?= af_input('sort_order', 'Sort order', $f['sort_order'] ?? 0, ['type' => 'number']) ?>
        <?= af_checkbox('show_on_home', 'Show on the home page', (bool) ($f['show_on_home'] ?? false)) ?>
        <?= af_checkbox('is_published', 'Published', (bool) ($f['is_published'] ?? true)) ?>
    </div>
    <div class="a-form__actions">
        <button class="a-btn a-btn--primary" type="submit">Save FAQ</button>
        <a class="a-btn" href="/admin/faqs/">Cancel</a>
    </div>
</form>
