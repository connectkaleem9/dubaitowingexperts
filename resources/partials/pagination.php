<?php
/** @var array{page:int,pages:int} $pager @var string $base */
if ($pager['pages'] <= 1) {
    return;
}
$link = static fn (int $p): string => $p === 1 ? $base : $base . '?page=' . $p;
?>
<nav aria-label="Pagination">
    <ul class="pagination">
        <?php if ($pager['page'] > 1): ?><li><a href="<?= e($link($pager['page'] - 1)) ?>" rel="prev">Previous</a></li><?php endif; ?>
        <?php for ($p = 1; $p <= $pager['pages']; $p++): ?>
            <li><?php if ($p === $pager['page']): ?><span aria-current="page"><?= $p ?></span><?php else: ?><a href="<?= e($link($p)) ?>"><?= $p ?></a><?php endif; ?></li>
        <?php endfor; ?>
        <?php if ($pager['page'] < $pager['pages']): ?><li><a href="<?= e($link($pager['page'] + 1)) ?>" rel="next">Next</a></li><?php endif; ?>
    </ul>
</nav>
