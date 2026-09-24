<?php
/**
 * @var array $project @var array|null $featured @var array|null $before @var array|null $after
 * @var list<array> $gallery @var list<array> $related @var array $relatedImages
 */
$location = $project['area_name'] ?: ($project['location_text'] ?? '');
?>
<article>
    <section class="hero hero--compact">
        <div class="container">
            <h1><?= e($project['title']) ?></h1>
            <?php if ($project['excerpt'] !== ''): ?><p class="hero__lead"><?= e($project['excerpt']) ?></p><?php endif; ?>
        </div>
    </section>

    <section class="section">
        <div class="container split">
            <div class="prose">
                <dl class="facts">
                    <?php if ($project['service_name']): ?><div><dt>Service</dt><dd><?php if ($project['service_published']): ?><a href="/services/<?= e($project['service_slug']) ?>/"><?= e($project['service_name']) ?></a><?php else: ?><?= e($project['service_name']) ?><?php endif; ?></dd></div><?php endif; ?>
                    <?php if ($location): ?><div><dt>Location</dt><dd><?php if ($project['area_name'] && $project['area_published']): ?><a href="/areas/<?= e($project['area_slug']) ?>/"><?= e($location) ?></a><?php else: ?><?= e($location) ?><?php endif; ?></dd></div><?php endif; ?>
                    <?php if ($project['vehicle_type']): ?><div><dt>Vehicle</dt><dd><?= e($project['vehicle_type']) ?></dd></div><?php endif; ?>
                    <?php if ($project['project_date']): ?><div><dt>Date</dt><dd><time datetime="<?= e($project['project_date']) ?>"><?= e(format_date($project['project_date'])) ?></time></dd></div><?php endif; ?>
                </dl>

                <?php if ($featured): ?>
                    <figure><?= media_img($featured, '(min-width: 1024px) 720px, 100vw', ['loading' => 'eager', 'fetchpriority' => 'high']) ?></figure>
                <?php endif; ?>

                <?= $project['body'] /* sanitised on save */ ?>

                <?php if ($before || $after): ?>
                    <h2>Before and after</h2>
                    <div class="before-after">
                        <?php if ($before): ?><figure><?= media_img($before, '(min-width: 640px) 360px, 100vw') ?><figcaption>Before</figcaption></figure><?php endif; ?>
                        <?php if ($after): ?><figure><?= media_img($after, '(min-width: 640px) 360px, 100vw') ?><figcaption>After</figcaption></figure><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($gallery !== []): ?>
                    <h2>Photos</h2>
                    <div class="gallery">
                        <?php foreach ($gallery as $g): ?>
                            <figure><?= media_img($g, '(min-width: 640px) 240px, 100vw') ?><?php if ($g['caption'] !== ''): ?><figcaption><?= e($g['caption']) ?></figcaption><?php endif; ?></figure>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <aside class="split__aside"><?= partial('contact-box', ['heading' => 'Need similar help?']) ?></aside>
        </div>
    </section>
</article>

<?php if ($related !== []): ?>
<section class="section section--surface" aria-labelledby="rp-h">
    <div class="container">
        <h2 id="rp-h">More recent jobs</h2>
        <div class="grid grid--3">
            <?php foreach ($related as $p): ?>
                <?= partial('project-card', ['project' => $p, 'image' => $relatedImages[(int) $p['featured_image_id']] ?? null]) ?>
            <?php endforeach; ?>
        </div>
        <p class="section__foot"><a href="/projects/">All projects</a></p>
    </div>
</section>
<?php endif; ?>
