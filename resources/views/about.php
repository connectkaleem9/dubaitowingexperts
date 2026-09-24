<?php /** @var list<array> $services */ ?>
<section class="hero hero--compact">
    <div class="container">
        <h1>About Dubai Towing Experts</h1>
        <p class="hero__lead">We help drivers in Dubai when their vehicle can't go any further — with recovery, towing and roadside assistance that starts with a clear conversation and an agreed price.</p>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="prose">
            <h2>What we do</h2>
            <p>A breakdown, a flat tyre or an accident is stressful enough. Our job is to make the next part simple: you tell us where you are and what happened, we explain what we will do and what it will cost, and then we get your vehicle where it needs to go.</p>
            <?php if ($services !== []): ?>
                <p>Our services include:</p>
                <ul>
                    <?php foreach ($services as $s): ?>
                        <li><a href="/services/<?= e($s['slug']) ?>/"><?= e($s['name']) ?></a> — <?= e($s['excerpt']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h2>How we work</h2>
            <ul>
                <li><strong>Clear pricing.</strong> We agree the cost with you before we dispatch.</li>
                <li><strong>Straightforward contact.</strong> Call or WhatsApp the same number: <a href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="about"><?= e(business('phone_display')) ?></a>.</li>
                <li><strong>Your vehicle, your destination.</strong> We deliver to the garage, dealer, home or address you choose.</li>
                <li><strong>Honest feedback.</strong> Every review on our <a href="/reviews/">reviews page</a> is from a real customer, and you can see real jobs on our <a href="/projects/">projects page</a>.</li>
            </ul>

            <h2>Where we work</h2>
            <p>We are based in Dubai and cover the city's communities, business districts and main roads. See the <a href="/areas/">areas we cover</a> or simply send us your location.</p>
        </div>
        <aside class="split__aside"><?= partial('contact-box') ?></aside>
    </div>
</section>
