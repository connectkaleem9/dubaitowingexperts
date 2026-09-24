<?php
$services = nav_services();
$areas = nav_areas();
$waMessage = $waMessage ?? null;
$nav = [
    ['/', 'Home', [], null],
    ['/services/', 'Services', $services, '/services/'],
    ['/areas/', 'Areas', array_slice($areas, 0, 10), '/areas/'],
    ['/projects/', 'Projects', [], null],
    ['/reviews/', 'Reviews', [], null],
    ['/about/', 'About', [], null],
    ['/contact/', 'Contact', [], null],
];
// FAQ and Blog are reachable from the footer (and from in-page links), keeping the top nav short.
?>
<header class="site-header">
    <div class="container site-header__bar">
        <?= partial('logo', ['href' => '/']) ?>
        <nav id="site-nav" class="site-nav" aria-label="Main">
            <ul>
                <?php foreach ($nav as [$href, $label, $children, $base]): ?>
                    <?php if ($href === '/areas/' && $areas === []) { continue; } ?>
                    <li<?= $children !== [] ? ' class="has-sub"' : '' ?>>
                        <a href="<?= e($href) ?>"<?= is_active($href) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                        <?php if ($children !== []): ?>
                            <ul class="sub">
                                <?php foreach ($children as $child): ?>
                                    <li><a href="<?= e($base . $child['slug'] . '/') ?>"><?= e($child['name']) ?></a></li>
                                <?php endforeach; ?>
                                <li><a href="<?= e($base) ?>">View all <?= e(mb_strtolower($label)) ?></a></li>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="header-actions">
            <a class="btn btn--call" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="header">
                <?= icon('phone') ?><span class="btn__label"><?= e(business('phone_display')) ?></span><span class="sr-only"> – call now</span>
            </a>
            <button class="nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false">
                <?= icon('menu') ?><span class="sr-only">Menu</span>
            </button>
        </div>
    </div>
</header>
