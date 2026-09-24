<?php /** @var list<array> $posts @var array $images @var array $pager */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Guides for Dubai Drivers</h1>
        <p class="hero__lead">Practical advice for breakdowns, accidents, flat tyres and keeping your car going in the Dubai heat.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($posts === []): ?>
            <p>Our first guides are on the way. Meanwhile, our <a href="/faq/">FAQ</a> answers the most common questions.</p>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($posts as $p): ?>
                    <article class="card">
                        <?php if ($img = $images[(int) $p['featured_image_id']] ?? null): ?>
                            <div class="card__media"><?= media_img($img, '(min-width: 1024px) 380px, (min-width: 640px) 50vw, 100vw') ?></div>
                        <?php endif; ?>
                        <div class="card__meta"><time datetime="<?= e(substr((string) $p['published_at'], 0, 10)) ?>"><?= e(format_date($p['published_at'])) ?></time></div>
                        <h2 class="h3"><?= e($p['title']) ?></h2>
                        <p><?= e($p['excerpt']) ?></p>
                        <a class="card__link" href="/blog/<?= e($p['slug']) ?>/">Read guide <?= icon('arrow') ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
            <?= partial('pagination', ['pager' => $pager, 'base' => '/blog/']) ?>
        <?php endif; ?>
    </div>
</section>
