<?php
/**
 * Homepage — built to the owner's design (docs/design/reference/).
 * Claims are limited to confirmed facts: 24/7 (confirmed 2026-09-22), Dubai-wide coverage, and
 * "price agreed before dispatch". No arrival-time, fleet-size or rating claims (CLAUDE.md §2).
 *
 * @var App\Services\Seo $seo
 * @var list<array> $services @var array $serviceImages @var list<array> $areas @var array $areaImages
 * @var list<array> $projects @var array $projectImages @var list<array> $reviews @var array $reviewStats
 * @var list<array> $faqs
 */
$fleet = trim((string) setting('fleet_description', ''));
// Icons are pre-rendered so the slider markup below can be written once and duplicated for the loop.
$icons = [];
foreach (['clock', 'tag', 'shield', 'pin', 'whatsapp', 'route'] as $name) {
    $icons[$name] = icon($name);
}
?>
<?php // Default: the owner's hero photo as a full-bleed background. Setting a hero image in the
      // admin instead shows it as a picture beside the text (useful for a seasonal or offer visual). ?>
<section class="hero <?= $heroImage ? 'hero--plain' : 'hero--photo' ?>">
    <?php // The hero photo is a CSS background on every width: complete and full width on phones,
          // and the right-hand layer on desktop (see .hero--photo in site.css). ?>
    <p class="hero__script" aria-hidden="true">Anytime, anywhere<br>we're here for you</p>
    <div class="container hero__grid<?= $heroImage ? ' hero__grid--media' : '' ?>">
        <div>
            <p class="eyebrow">Fast. Safe. Reliable.</p>
            <h1>Car Recovery &amp; Towing Services <span class="accent">in Dubai</span></h1>
            <p class="hero__lead"><?= e(business('name')) ?> provides 24/7 car recovery, towing and roadside assistance across Dubai. Tell us where you are and we will agree the price before we set off.</p>
            <div class="btn-row">
                <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="hero">
                    <?= icon('phone') ?><span class="btn__stack">Call Now<small><?= e(business('phone_display')) ?></small></span>
                </a>
                <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="hero">
                    <?= icon('whatsapp') ?><span class="btn__stack">WhatsApp Now<small>Get instant help</small></span>
                </a>
            </div>
            <?php
            // On phones these four points slide right → left; from 900px up they are a static row.
            $statItems = <<<HTML
                <li>{$icons['clock']}<span><strong>24/7</strong><span>Always available</span></span></li>
                <li>{$icons['tag']}<span><strong>Clear price</strong><span>Agreed before dispatch</span></span></li>
                <li>{$icons['shield']}<span><strong>Careful handling</strong><span>Right method for your car</span></span></li>
                <li>{$icons['pin']}<span><strong>All over Dubai</strong><span>We cover every area</span></span></li>
                HTML;
            ?>
            <div class="marquee marquee--rtl marquee--mobile marquee--stats" id="hero-stats-marquee" data-marquee>
                <div class="marquee__track">
                    <ul class="marquee__group hero__stats"><?= $statItems ?></ul>
                    <ul class="marquee__group hero__stats" aria-hidden="true" inert><?= $statItems ?></ul>
                </div>
                <button class="marquee-toggle" type="button" data-marquee-toggle aria-pressed="false">
                    <?= icon('pause') ?><span data-marquee-label>Pause</span><span class="sr-only"> the sliding list</span>
                </button>
            </div>
        </div>
        <?php if ($heroImage): ?>
            <div class="hero__media"><?= media_img($heroImage, '(min-width: 1024px) 560px, 100vw', ['loading' => 'eager', 'fetchpriority' => 'high']) ?></div>
        <?php endif; ?>
    </div>
</section>

<?php if ($services !== []): ?>
<section class="section section--surface" aria-labelledby="services-h">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <p class="eyebrow">Our Services</p>
                <h2 id="services-h">Professional Recovery Services in Dubai</h2>
            </div>
            <div class="section__head-actions">
                <button class="marquee-toggle" type="button" data-marquee-toggle data-marquee-target="services-marquee" aria-pressed="false">
                    <?= icon('pause') ?><span data-marquee-label>Pause</span><span class="sr-only"> the services slider</span>
                </button>
                <a class="link-more" href="/services/">View All Services <?= icon('arrow') ?></a>
            </div>
        </div>
        <?php
        // Continuous auto-slide. The track holds the cards twice so it can loop seamlessly;
        // the second set is inert and hidden from assistive tech so links are not duplicated.
        $renderCards = static function (array $services, array $serviceImages): string {
            $html = '';
            foreach ($services as $s) {
                $html .= partial('service-card', ['service' => $s, 'image' => $serviceImages[(int) $s['image_id']] ?? null]);
            }
            return $html;
        };
        ?>
        <div class="marquee" id="services-marquee" data-marquee>
            <div class="marquee__track">
                <div class="marquee__group"><?= $renderCards($services, $serviceImages) ?></div>
                <div class="marquee__group" aria-hidden="true" inert><?= $renderCards($services, $serviceImages) ?></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section--dark section--photo section--tight" aria-labelledby="why-h">
    <div class="container usp-band">
        <div>
            <p class="eyebrow">Why Choose Us</p>
            <h2 id="why-h">Your Trusted Recovery Partner in Dubai</h2>
        </div>
        <?php
        $uspItems = <<<HTML
            <li>{$icons['clock']}<strong>24/7<br>Availability</strong></li>
            <li>{$icons['tag']}<strong>Clear Price<br>Before Dispatch</strong></li>
            <li>{$icons['shield']}<strong>Safe &amp; Secure<br>Handling</strong></li>
            <li>{$icons['whatsapp']}<strong>Call or<br>WhatsApp</strong></li>
            <li>{$icons['route']}<strong>Your Choice<br>of Garage</strong></li>
            <li>{$icons['pin']}<strong>Wide Coverage<br>Across Dubai</strong></li>
            HTML;
        ?>
        <div class="marquee marquee--rtl marquee--mobile marquee--usp" id="usp-marquee" data-marquee>
            <div class="marquee__track">
                <ul class="marquee__group usp-grid"><?= $uspItems ?></ul>
                <ul class="marquee__group usp-grid" aria-hidden="true" inert><?= $uspItems ?></ul>
            </div>
            <button class="marquee-toggle" type="button" data-marquee-toggle aria-pressed="false">
                <?= icon('pause') ?><span data-marquee-label>Pause</span><span class="sr-only"> the sliding list</span>
            </button>
        </div>
    </div>
</section>

<section class="section" aria-labelledby="how-h">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <p class="eyebrow">How It Works</p>
                <h2 id="how-h">Get Help in 3 Simple Steps</h2>
            </div>
            <div class="help-card help-card--inline">
                <?= icon('headset', 'icon icon--big') ?>
                <div>
                    <h3>Need Immediate Help?</h3>
                    <p>We are available 24 hours a day for emergency recovery across Dubai.</p>
                    <a class="btn" href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="help_card"><?= icon('phone') ?><?= e(business('phone_display')) ?></a>
                </div>
            </div>
        </div>
        <ol class="steps steps--row">
                <li>
                    <div>
                        <h3><?= icon('phone') ?>Call or Request</h3>
                        <p>Contact us by call, WhatsApp or the online form — whatever is easiest from where you are.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <h3><?= icon('pin') ?>Share Your Location</h3>
                        <p>Send a WhatsApp location pin and tell us about the vehicle. We confirm the price and an arrival estimate.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <h3><?= icon('truck') ?>We Come to You</h3>
                        <p>Your vehicle is loaded carefully and delivered to the garage, dealer or address you choose.</p>
                    </div>
                </li>
        </ol>
    </div>
</section>

<?php if ($areas !== []): ?>
<section class="section section--surface-2" aria-labelledby="areas-h">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <p class="eyebrow">Areas We Serve</p>
                <h2 id="areas-h">Car Recovery Across All Areas of Dubai</h2>
            </div>
            <a class="link-more" href="/areas/">View All Areas <?= icon('arrow') ?></a>
        </div>
        <div class="area-grid">
            <?php foreach ($areas as $a): ?>
                <?= partial('area-card', ['area' => $a]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section" aria-label="Recent work and customer feedback">
    <div class="container split split--even">
        <div>
            <div class="section__head section__head--row">
                <div>
                    <p class="eyebrow">Recent Projects</p>
                    <h2>Our Latest Recovery Projects</h2>
                </div>
                <?php if ($projects !== []): ?><a class="link-more" href="/projects/">View All Projects <?= icon('arrow') ?></a><?php endif; ?>
            </div>
            <?php if ($projects === []): ?>
                <p class="muted">Recent jobs will appear here as we add them, with photos and what was involved.</p>
            <?php else: ?>
                <?php
                // Continuous slider, same mechanism as the services row.
                $projectTiles = '';
                foreach ($projects as $p) {
                    $projectTiles .= partial('project-tile', ['project' => $p, 'image' => $projectImages[(int) $p['featured_image_id']] ?? null]);
                }
                ?>
                <div class="marquee marquee--projects" id="projects-marquee" data-marquee>
                    <div class="marquee__track">
                        <div class="marquee__group"><?= $projectTiles ?></div>
                        <div class="marquee__group" aria-hidden="true" inert><?= $projectTiles ?></div>
                    </div>
                    <button class="marquee-toggle" type="button" data-marquee-toggle aria-pressed="false">
                        <?= icon('pause') ?><span data-marquee-label>Pause</span><span class="sr-only"> the projects slider</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="section__head section__head--row">
                <div>
                    <p class="eyebrow">Customer Reviews</p>
                    <h2>What Our Customers Say</h2>
                </div>
                <?php if ($reviews !== []): ?><a class="link-more" href="/reviews/">View All Reviews <?= icon('arrow') ?></a><?php endif; ?>
            </div>
            <?php if ($reviews === []): ?>
                <div class="card card--flat">
                    <p>We publish reviews from real customers only, and we are just getting started. If we have helped you, we would be grateful for a few words.</p>
                    <a class="btn btn--outline" href="/reviews/#review-form">Leave a review</a>
                </div>
            <?php else: ?>
                <?php if ($reviewStats['count'] > 0): ?>
                    <p class="muted small"><?= partial('stars', ['rating' => (int) round($reviewStats['average'])]) ?>
                        <?= e(number_format($reviewStats['average'], 1)) ?> out of 5 from <?= (int) $reviewStats['count'] ?> review<?= $reviewStats['count'] === 1 ? '' : 's' ?> left on this website</p>
                <?php endif; ?>
                <div class="review-carousel" data-carousel>
                    <div class="carousel-viewport">
                        <div class="carousel-track">
                            <?php foreach ($reviews as $i => $r): ?>
                                <div role="group" aria-roledescription="slide" aria-label="Review <?= $i + 1 ?> of <?= count($reviews) ?>">
                                    <?= partial('review-card', ['review' => $r]) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (count($reviews) > 1): ?>
                        <div class="carousel-nav">
                            <button class="carousel-arrow carousel-arrow--prev" type="button" data-carousel-prev aria-label="Previous review"><?= icon('arrow') ?></button>
                            <div class="carousel-dots" data-carousel-dots></div>
                            <button class="carousel-arrow" type="button" data-carousel-next aria-label="Next review"><?= icon('arrow') ?></button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($fleet !== ''): ?>
<section class="section section--surface" aria-labelledby="fleet-h">
    <div class="container prose">
        <p class="eyebrow">Our Equipment</p>
        <h2 id="fleet-h">Recovery vehicles and equipment</h2>
        <p><?= nl2br(e($fleet), false) ?></p>
    </div>
</section>
<?php endif; ?>

<section class="section section--surface" aria-label="Questions and contact">
    <div class="container split split--even">
        <div>
            <div class="section__head section__head--row">
                <div>
                    <p class="eyebrow">Frequently Asked Questions</p>
                    <h2>Got Questions? We've Got Answers.</h2>
                </div>
                <a class="link-more" href="/faq/">View All FAQs <?= icon('arrow') ?></a>
            </div>
            <?php if ($faqs !== []): ?>
                <?= partial('faq-list', ['faqs' => $faqs]) ?>
            <?php endif; ?>
        </div>
        <div>
            <?= partial('cta-card') ?>
        </div>
    </div>
</section>

<section class="section" aria-label="Request recovery">
    <div class="container split">
        <?= partial('lead-form', ['services' => $services, 'formType' => 'quote', 'heading' => 'Request recovery or a quote', 'sourcePath' => '/']) ?>
        <div class="split__aside">
            <?= partial('contact-box', ['heading' => 'Prefer to talk?']) ?>
        </div>
    </div>
</section>
