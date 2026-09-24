<?php /** @var string $title */ $updated = setting('legal_updated', '21 September 2026'); ?>
<section class="section">
    <div class="container prose">
        <h1><?= e($title) ?></h1>
        <p class="muted">Last updated: <?= e($updated) ?></p>

        <p>This policy explains how <?= e(business('name')) ?> ("we", "us") collects and uses personal information when you visit <?= e(business('domain')) ?>, contact us, request a service or leave a review. We process personal data in line with the UAE Personal Data Protection Law (Federal Decree-Law No. 45 of 2021) and other applicable UAE law.</p>

        <h2>Information we collect</h2>
        <ul>
            <li><strong>Enquiry and quote forms:</strong> your name, mobile number, optional email address, the location of your vehicle, vehicle type, the service you need, your message and your preferred contact method.</li>
            <li><strong>Reviews:</strong> your name, rating, review text, optional area, optional email or phone (never published) and any photo you choose to upload.</li>
            <li><strong>Calls and WhatsApp:</strong> when you call or message us, we receive your phone number and anything you share, such as a location pin or photos.</li>
            <li><strong>Technical data:</strong> basic information your browser sends (such as device type and pages visited). We store a one-way scrambled version of your IP address to prevent spam; we do not store the IP address itself with your enquiry.</li>
            <li><strong>Advertising data:</strong> if you arrive from an online advert, the campaign details in the link (for example a Google click ID) are saved with your enquiry so we can understand which adverts work.</li>
        </ul>

        <h2>How we use it</h2>
        <ul>
            <li>To respond to your enquiry, give you a quote and provide the service you request.</li>
            <li>To contact you by your chosen method about your request.</li>
            <li>To publish reviews you have agreed we may publish.</li>
            <li>To keep the website secure and prevent spam or abuse.</li>
            <li>To measure and improve our website and advertising, where you have allowed this (see our <a href="/cookie-policy/">cookie policy</a>).</li>
        </ul>
        <p>We do not sell your personal information.</p>

        <h2>Who we share it with</h2>
        <p>We share information only where needed to deliver the service or run the website: for example our web hosting provider, email provider, and — if analytics or advertising cookies are enabled — Google. If you ask us to deliver your vehicle to a garage, dealer or insurer, we may share the details needed to do that. We may disclose information where the law requires it, for example to the police.</p>

        <h2>How long we keep it</h2>
        <p>We keep enquiry records for as long as needed to provide the service and for our business records, and then delete them. Published reviews remain until you ask us to remove them or we stop displaying them.</p>

        <h2>Your rights</h2>
        <p>You can ask us to tell you what personal information we hold about you, correct it, delete it, or stop using it for a particular purpose, subject to UAE law. To make a request, call or WhatsApp us on <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a><?php if (business('email')): ?> or email <a href="mailto:<?= e(business('email')) ?>"><?= e(business('email')) ?></a><?php endif; ?>.</p>

        <h2>Security</h2>
        <p>We use HTTPS encryption, access-controlled systems and other reasonable measures to protect your information. No website is completely secure, so please do not send sensitive information (such as payment card details) through our forms.</p>

        <h2>Changes</h2>
        <p>We may update this policy. The date at the top shows when it last changed.</p>
    </div>
</section>
