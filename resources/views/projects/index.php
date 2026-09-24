<?php /** @var list<array> $projects @var array $images @var array $pager */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Recent Recovery Jobs in Dubai</h1>
        <p class="hero__lead">Real jobs we have completed — what happened, where it was and how the vehicle was moved.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($projects === []): ?>
            <p>We are adding our latest jobs here soon. In the meantime, see our <a href="/services/">services</a> or <a href="/reviews/">customer reviews</a>.</p>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($projects as $p): ?>
                    <?= partial('project-card', ['project' => $p, 'image' => $images[(int) $p['featured_image_id']] ?? null, 'headingTag' => 'h2']) ?>
                <?php endforeach; ?>
            </div>
            <?= partial('pagination', ['pager' => $pager, 'base' => '/projects/']) ?>
        <?php endif; ?>
    </div>
</section>

<section class="section section--surface">
    <div class="container"><?= partial('cta-band') ?></div>
</section>
