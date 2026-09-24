<?php /** @var array<string, list<array>> $grouped */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>Car Recovery Questions &amp; Answers</h1>
        <p class="hero__lead">Straight answers to the questions drivers ask us most. Can't find yours? Call or WhatsApp us.</p>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="prose">
            <?php if ($grouped === []): ?>
                <p>Our FAQ is being updated. Please call <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a> with any question.</p>
            <?php endif; ?>
            <?php foreach ($grouped as $category => $faqs): ?>
                <h2><?= e($category) ?></h2>
                <?= partial('faq-list', ['faqs' => $faqs]) ?>
            <?php endforeach; ?>
        </div>
        <aside class="split__aside"><?= partial('contact-box', ['heading' => 'Still have a question?']) ?></aside>
    </div>
</section>
