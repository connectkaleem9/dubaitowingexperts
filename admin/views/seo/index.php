<?php /** @var list<array> $overrides @var list<string> $paths @var list<array> $library @var int $sitemapCount */ ?>
<section class="a-card">
    <h2>How SEO works on this site</h2>
    <p class="a-muted">Every page already has a title and description generated from its content. Use this screen only when you want to override them for a specific page, or hide a page from Google. Your sitemap currently lists <strong><?= (int) $sitemapCount ?></strong> pages: <a href="/sitemap.xml" target="_blank" rel="noopener">view sitemap.xml ↗</a> · <a href="/robots.txt" target="_blank" rel="noopener">robots.txt ↗</a></p>
</section>

<section class="a-card">
    <h2>Add or update an override</h2>
    <form class="a-form" method="post" action="/admin/seo/">
        <?= csrf_field() ?>
        <div class="a-field">
            <label for="f-path">Page path *</label>
            <input id="f-path" name="path" list="seo-paths" required maxlength="255" placeholder="/services/car-recovery/" value="<?= e(old('path')) ?>">
            <datalist id="seo-paths"><?php foreach ($paths as $p): ?><option value="<?= e($p) ?>"></option><?php endforeach; ?></datalist>
            <?= af_error('path') ?>
        </div>
        <?= af_input('title', 'Title tag', '', ['maxlength' => 70], 'Aim for 50–60 characters. Leave blank to keep the automatic title.') ?>
        <?= af_textarea('meta_description', 'Meta description', '', 2, 'Aim for 120–155 characters.') ?>
        <div class="a-grid-2">
            <?= af_select('robots', 'Search engine visibility', '', ['index,follow' => 'Index (default)', 'noindex,follow' => 'Hide from search results', 'noindex,nofollow' => 'Hide and do not follow links'], 'Use page default') ?>
            <?= af_select('og_image_id', 'Social sharing image', '', array_column(array_map(static fn (array $m): array => ['id' => $m['id'], 'label' => '#' . $m['id'] . ' ' . str_limit((string) ($m['alt_text'] ?: $m['original_name']), 50)], $library), 'label', 'id'), '— default —') ?>
        </div>
        <button class="a-btn a-btn--primary" type="submit">Save override</button>
    </form>
</section>

<?php if ($overrides !== []): ?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Path</th><th>Title</th><th>Description</th><th>Robots</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($overrides as $o): ?>
            <tr>
                <td><a href="<?= e($o['path']) ?>" target="_blank" rel="noopener"><?= e($o['path']) ?></a></td>
                <td><?= e($o['title'] ?: '—') ?><?php if ($o['title']): ?> <span class="a-muted">(<?= mb_strlen((string) $o['title']) ?>)</span><?php endif; ?></td>
                <td><?= e(str_limit((string) $o['meta_description'], 80)) ?: '—' ?><?php if ($o['meta_description']): ?> <span class="a-muted">(<?= mb_strlen((string) $o['meta_description']) ?>)</span><?php endif; ?></td>
                <td><?= e($o['robots'] ?: 'default') ?></td>
                <td class="a-muted"><?= e(format_date($o['updated_at'])) ?></td>
                <td><?= a_post_button('/admin/seo/' . (int) $o['id'] . '/delete/', 'Remove', 'a-btn a-btn--sm a-btn--danger', 'Remove this override?') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
