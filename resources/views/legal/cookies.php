<?php /** @var string $title */ $updated = setting('legal_updated', '21 September 2026'); ?>
<section class="section">
    <div class="container prose">
        <h1><?= e($title) ?></h1>
        <p class="muted">Last updated: <?= e($updated) ?></p>

        <p>Cookies are small files a website stores in your browser. This page explains which ones <?= e(business('domain')) ?> uses.</p>

        <h2>Essential cookies</h2>
        <p>These are needed for the website to work and cannot be switched off.</p>
        <table>
            <thead><tr><th scope="col">Name</th><th scope="col">Purpose</th><th scope="col">Duration</th></tr></thead>
            <tbody>
                <tr><td><?= e(config('app.session_name')) ?></td><td>Keeps forms secure (prevents forged submissions) and remembers form errors between pages.</td><td>Until you close your browser</td></tr>
                <tr><td>dre_consent (local storage)</td><td>Remembers whether you accepted or declined analytics and advertising cookies.</td><td>Until you clear it</td></tr>
            </tbody>
        </table>

        <h2>Analytics and advertising cookies</h2>
        <p>If you click "Accept" on our cookie notice, we use Google Analytics and Google Ads (through Google Tag Manager) to understand how visitors use the site and which adverts lead to enquiries. These services may set cookies such as <code>_ga</code>, <code>_gid</code>, <code>_gcl_au</code> and similar. If you click "Decline", these cookies are not set; Google may still receive basic, cookieless signals as described in Google's documentation.</p>
        <p>Learn more about how Google uses information at <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener noreferrer">policies.google.com/technologies/partner-sites</a>.</p>

        <h2>Changing your choice</h2>
        <p>You can clear cookies and site data in your browser settings at any time. The next time you visit, we will ask again.</p>

        <h2>More information</h2>
        <p>See our <a href="/privacy-policy/">privacy policy</a>, or call <a href="<?= e(tel_href()) ?>"><?= e(business('phone_display')) ?></a>.</p>
    </div>
</section>
