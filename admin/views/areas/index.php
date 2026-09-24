<?php /** @var list<array> $items */ ?>
<div class="a-card">
    <p class="a-muted">Publish an area page only when it has real local content — roads, parking, access and typical situations in that area. Thin pages that only swap the area name can harm your rankings.</p>
</div>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Order</th><th>Area</th><th>URL</th><th>Words</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $a): $words = str_word_count(strip_tags((string) $a['body'])); ?>
            <tr>
                <td><?= (int) $a['sort_order'] ?></td>
                <td><a href="/admin/areas/<?= (int) $a['id'] ?>/"><?= e($a['name']) ?></a></td>
                <td class="a-muted">/areas/<?= e($a['slug']) ?>/</td>
                <td<?= $words < 150 ? ' class="a-warn"' : '' ?>><?= $words ?></td>
                <td><?= a_status($a['is_published'] ? 'published' : 'draft') ?></td>
                <td class="a-row-actions">
                    <a class="a-btn a-btn--sm" href="/admin/areas/<?= (int) $a['id'] ?>/">Edit</a>
                    <?= a_post_button('/admin/areas/' . (int) $a['id'] . '/delete/', 'Delete', 'a-btn a-btn--sm a-btn--danger', 'Delete this area?') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
