<?php
/**
 * Google Analytics 4 and/or Google Tag Manager, both behind Consent Mode v2.
 *
 * Either can be used on its own. GA4 alone (a G- measurement ID) is the simple route and needs no
 * container work; GTM is there for Google Ads conversions and anything more involved. Configure
 * both in Admin → Settings. Event names pushed by app.js are in docs/analytics-tracking.md.
 *
 * @var App\Services\Seo $seo
 */
$gtm = (string) setting('gtm_id', '');
$ga4 = (string) setting('ga4_id', '');
$hasGtm = (bool) preg_match('/^GTM-[A-Z0-9]{4,12}$/', $gtm);
$hasGa4 = (bool) preg_match('/^G-[A-Z0-9]{4,15}$/', $ga4);
if (!$hasGtm && !$hasGa4) {
    return;
}
$consentDefault = setting('consent_default', 'denied') === 'granted' ? 'granted' : 'denied';
?>
<?php if ($hasGa4): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4) ?>" nonce="<?= e(csp_nonce()) ?>"></script>
<?php endif; ?>
<script nonce="<?= e(csp_nonce()) ?>">
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'<?= $consentDefault ?>',ad_user_data:'<?= $consentDefault ?>',ad_personalization:'<?= $consentDefault ?>',analytics_storage:'<?= $consentDefault ?>',wait_for_update:500});
try{var c=localStorage.getItem('dre_consent');if(c==='granted'||c==='denied'){gtag('consent','update',{ad_storage:c,ad_user_data:c,ad_personalization:c,analytics_storage:c});}}catch(e){}
dataLayer.push({page_type:<?= json_encode($seo->pageType, JSON_HEX_TAG) ?>});
<?php if ($hasGa4): ?>
gtag('js',new Date());gtag('config',<?= json_encode($ga4, JSON_HEX_TAG) ?>,{page_type:<?= json_encode($seo->pageType, JSON_HEX_TAG) ?>});
<?php endif; ?>
<?php if ($hasGtm): ?>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',<?= json_encode($gtm) ?>);
<?php endif; ?>
</script>
