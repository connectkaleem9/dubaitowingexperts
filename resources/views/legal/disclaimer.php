<?php /** @var string $title */ $updated = setting('legal_updated', '21 September 2026'); ?>
<section class="section">
    <div class="container prose">
        <h1><?= e($title) ?></h1>
        <p class="muted">Last updated: <?= e($updated) ?></p>

        <h2>General information only</h2>
        <p>Guides, FAQs and other content on <?= e(business('domain')) ?> are general information for drivers in Dubai. They are not legal, insurance, mechanical or safety advice for your specific situation. Road rules and official procedures (for example how to report an accident) can change — always follow instructions from Dubai Police, the RTA and your insurer.</p>

        <h2>Emergencies</h2>
        <p>If anyone is injured or in danger, call 999 (police) or 998 (ambulance) first. We are a vehicle recovery service, not an emergency service.</p>

        <h2>Accuracy</h2>
        <p>We try to keep the website accurate and up to date, but we do not guarantee that all information is complete or current. Prices and availability are confirmed only when you speak to us.</p>

        <h2>External links</h2>
        <p>Links to other websites are provided for convenience. We are not responsible for their content.</p>

        <h2>Reviews</h2>
        <p>Customer reviews reflect the personal experience of the reviewer. We moderate reviews for abuse and relevance but do not edit their opinions.</p>
    </div>
</section>
