<?php
// Shown only when analytics is configured and consent defaults to "denied" (Admin → Settings).
if (!preg_match('/^GTM-[A-Z0-9]{4,12}$/', (string) setting('gtm_id', '')) || setting('consent_default', 'denied') === 'granted') {
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
