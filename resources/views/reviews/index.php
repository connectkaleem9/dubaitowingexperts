<?php
/** @var list<array> $reviews @var array $reviewImages @var array $stats @var list<array> $services @var array $pager @var bool $submitted */
?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Customer Reviews</h1>
        <p class="hero__lead">What drivers across Dubai say about our recovery and towing service. Every review comes from a real customer and is checked before it is published.</p>
        <?php if ($stats['count'] > 0): ?>
            <p class="hero__phone"><?= partial('stars', ['rating' => (int) round($stats['average'])]) ?> <?= e(number_format($stats['average'], 1)) ?> out of 5 from <?= (int) $stats['count'] ?> review<?= $stats['count'] === 1 ? '' : 's' ?> on this website</p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div>
            <?php if ($reviews === []): ?>
                <p>No reviews have been published yet. If we have helped you, we would be grateful if you could be the first to leave one.</p>
            <?php else: ?>
                <div class="grid grid--2">
                    <?php foreach ($reviews as $r): ?>
                        <?= partial('review-card', ['review' => $r, 'photos' => $reviewImages[(int) $r['id']] ?? []]) ?>
                    <?php endforeach; ?>
                </div>
                <?= partial('pagination', ['pager' => $pager, 'base' => '/reviews/']) ?>
            <?php endif; ?>
        </div>
        <div class="split__aside">
            <?= partial('review-form', ['services' => $services, 'submitted' => $submitted]) ?>
        </div>
    </div>
</section>
