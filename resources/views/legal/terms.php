<?php /** @var string $title */ $updated = setting('legal_updated', '21 September 2026'); ?>
<section class="section">
    <div class="container prose">
        <h1><?= e($title) ?></h1>
        <p class="muted">Last updated: <?= e($updated) ?></p>

        <p>These terms apply to your use of <?= e(business('domain')) ?> and to requests you make to <?= e(business('name')) ?> for recovery, towing and roadside assistance in Dubai. By using the website or requesting a service, you agree to them.</p>

        <h2>1. Requests and quotes</h2>
        <p>Contacting us by phone, WhatsApp or the website form is a request, not a booking. A job is confirmed only when we have agreed the service, price, pickup location and destination with you. Quotes are based on the information you give us; if the situation on arrival is materially different (for example the vehicle is in a different place, cannot be accessed as described, or needs additional work), we will explain and agree any change with you before continuing.</p>

        <h2>2. Your responsibilities</h2>
        <ul>
            <li>Give accurate information about your location, the vehicle and its condition.</li>
            <li>Make sure you are entitled to have the vehicle moved, and that it is legal to do so (for example, an accident has been reported to the police where required).</li>
            <li>Remove valuables from the vehicle where possible. We are not responsible for loose items left inside.</li>
            <li>Stay in a safe place while waiting for assistance.</li>
        </ul>

        <h2>3. Payment</h2>
        <p>Payment terms and accepted payment methods are confirmed when the job is agreed.</p>

        <h2>4. Cancellations</h2>
        <p>Please tell us as soon as possible if you no longer need assistance. If a vehicle has already been dispatched, we will tell you whether any charge applies before you cancel.</p>

        <h2>5. Liability</h2>
        <p>We carry out every job with reasonable care and skill. Nothing in these terms limits any rights you have under UAE consumer protection law. We are not responsible for pre-existing damage or mechanical faults, or for delays caused by traffic, road closures, weather, police instructions or other events outside our control.</p>

        <h2>6. Website content</h2>
        <p>Information on this website, including guides and FAQs, is general guidance and not professional, legal or insurance advice. See our <a href="/disclaimer/">disclaimer</a>. Customer reviews are the opinions of the people who wrote them.</p>

        <h2>7. Reviews you submit</h2>
        <p>By submitting a review you confirm it is your genuine experience and you allow us to publish it, together with your name and any photo you upload. We may decline or remove reviews that are abusive, unlawful, off-topic or contain personal information.</p>

        <h2>8. Governing law</h2>
        <p>These terms are governed by the laws of the Emirate of Dubai and the federal laws of the United Arab Emirates. The courts of Dubai have jurisdiction over any dispute.</p>

        <h2>9. Contact</h2>
        <p>Questions about these terms: call or WhatsApp <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a>.</p>
    </div>
</section>
