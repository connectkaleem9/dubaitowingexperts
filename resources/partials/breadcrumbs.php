<?php
/** @var App\Services\Seo $seo */
$items = array_merge([['Home', '/']], $seo->breadcrumbs);
$last = count($items) - 1;
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol class="container">
        <?php foreach ($items as $i => [$name, $path]): ?>
            <li><?php if ($i === $last): ?><span aria-current="page"><?= e($name) ?></span><?php else: ?><a href="<?= e($path) ?>"><?= e($name) ?></a><?php endif; ?></li>
        <?php endforeach; ?>
    </ol>
</nav>
