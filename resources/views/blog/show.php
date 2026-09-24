<?php /** @var array $post @var array|null $image @var list<array> $latest @var list<array> $services */ ?>
<article>
    <section class="hero hero--compact">
        <div class="container">
            <h1><?= e($post['title']) ?></h1>
            <p class="hero__phone">Published <time datetime="<?= e(substr((string) $post['published_at'], 0, 10)) ?>"><?= e(format_date($post['published_at'])) ?></time>
                <?php if (substr((string) $post['updated_at'], 0, 10) > substr((string) $post['published_at'], 0, 10)): ?> · Updated <time datetime="<?= e(substr((string) $post['updated_at'], 0, 10)) ?>"><?= e(format_date($post['updated_at'])) ?></time><?php endif; ?>
            </p>
        </div>
    </section>
    <section class="section">
        <div class="container split">
            <div class="prose">
                <?php if ($image): ?><figure><?= media_img($image, '(min-width: 1024px) 720px, 100vw', ['loading' => 'eager', 'fetchpriority' => 'high']) ?></figure><?php endif; ?>
                <?= $post['body'] /* sanitised on save */ ?>
            </div>
            <aside class="split__aside">
                <?= partial('contact-box', ['heading' => 'Need help on the road?']) ?>
            </aside>
        </div>
    </section>
</article>

<?php if ($services !== []): ?>
<section class="section section--surface" aria-labelledby="bs-h">
    <div class="container">
        <h2 id="bs-h">How we can help</h2>
        <div class="grid grid--4">
            <?php foreach ($services as $s): ?><?= partial('service-card', ['service' => $s]) ?><?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($latest !== []): ?>
<section class="section" aria-labelledby="bl-h">
    <div class="container">
        <h2 id="bl-h">More guides</h2>
        <ul>
            <?php foreach ($latest as $l): ?><li><a href="/blog/<?= e($l['slug']) ?>/"><?= e($l['title']) ?></a></li><?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>
