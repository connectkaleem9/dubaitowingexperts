<?php
/*
 * Shown when analytics is configured and consent defaults to "denied" (Admin → Settings).
 * The test has to cover Analytics on its own as well as Tag Manager: without it, a site running
 * only GA4 would never offer the banner, so analytics_storage would stay denied for every visitor
 * and Google would receive nothing but cookieless pings.
 */
$hasTag = preg_match('/^GTM-[A-Z0-9]{4,12}$/', (string) setting('gtm_id', ''))
    || preg_match('/^G-[A-Z0-9]{4,15}$/', (string) setting('ga4_id', ''));
if (!$hasTag || setting('consent_default', 'denied') === 'granted') {
    return;
}
?>
<div class="consent" id="consent" role="dialog" aria-live="polite" aria-label="Cookie preferences" hidden>
    <p>We use cookies to measure how our website is used and to improve our ads. See our <a href="/cookie-policy/">cookie policy</a>.</p>
    <div class="btn-row">
        <button type="button" class="btn btn--primary" data-consent="granted">Accept</button>
        <button type="button" class="btn btn--outline" data-consent="denied">Decline</button>
    </div>
</div>
