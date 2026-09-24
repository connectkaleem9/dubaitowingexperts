<?php
/** @var list<array> $reviews @var array $reviewImages @var array $stats @var list<array> $services @var bool $submitted */
?>
<section class="hero hero--compact">
    <div class="container">
        <p class="eyebrow">Customer Reviews</p>
        <h1>What Our Customers Say</h1>
        <p class="hero__lead">What drivers across Dubai say about our recovery and towing service. Every review comes from a real customer and is checked before it is published.</p>
        <?php if ($stats['count'] > 0): ?>
            <p class="hero__phone"><?= partial('stars', ['rating' => (int) round($stats['average'])]) ?> <?= e(number_format($stats['average'], 1)) ?> out of 5 from <?= (int) $stats['count'] ?> review<?= $stats['count'] === 1 ? '' : 's' ?> on this website</p>
        <?php endif; ?>
    </div>
</section>

<section class="section" aria-label="Reviews">
    <div class="container">
        <?php if (!empty($preview)): ?>
            <p class="alert alert--info" role="status"><strong>Design preview.</strong> These reviews are samples so you can see the layout filled out. They are not saved anywhere and only you can see them &mdash; the public page shows real reviews only. <a href="/reviews/">Show the real page</a></p>
        <?php endif; ?>
        <?php if ($reviews === []): ?>
            <div class="cta-card cta-card--center">
                <h2>Be the first to review us</h2>
                <p>We publish reviews from real customers only, so this page starts empty. If we have helped you, a few words would mean a lot.</p>
                <a class="btn btn--white btn--lg" href="#review-form">Leave a review</a>
            </div>
        <?php else: ?>
            <?php // Every approved review, all on this one page. ?>
            <div class="review-grid">
                <?php foreach ($reviews as $r): ?>
                    <?= partial('review-card', ['review' => $r, 'photos' => $reviewImages[(int) $r['id']] ?? []]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section--surface" aria-labelledby="leave-review-h">
    <div class="container container--narrow">
        <div class="section-head section-head--center">
            <p class="eyebrow eyebrow--center">Share your experience</p>
            <h2 id="leave-review-h">Leave Us a Review</h2>
            <p class="muted">Did we recover your car recently? We would love to hear how it went &mdash; it only takes a minute.</p>
        </div>
        <?= partial('review-form', ['services' => $services, 'submitted' => $submitted, 'heading' => false]) ?>
    </div>
</section>
