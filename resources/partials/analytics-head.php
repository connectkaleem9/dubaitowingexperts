<?php
/**
 * Google Tag Manager + Consent Mode v2. Configure the container ID in Admin → Settings (gtm_id).
 * Event names pushed by app.js are documented in docs/analytics-tracking.md.
 * @var App\Services\Seo $seo
 */
$gtm = (string) setting('gtm_id', '');
if (!preg_match('/^GTM-[A-Z0-9]{4,12}$/', $gtm)) {
    return;
}
$consentDefault = setting('consent_default', 'denied') === 'granted' ? 'granted' : 'denied';
?>
<script nonce="<?= e(csp_nonce()) ?>">
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'<?= $consentDefault ?>',ad_user_data:'<?= $consentDefault ?>',ad_personalization:'<?= $consentDefault ?>',analytics_storage:'<?= $consentDefault ?>',wait_for_update:500});
try{var c=localStorage.getItem('dre_consent');if(c==='granted'||c==='denied'){gtag('consent','update',{ad_storage:c,ad_user_data:c,ad_personalization:c,analytics_storage:c});}}catch(e){}
dataLayer.push({page_type:<?= json_encode($seo->pageType, JSON_HEX_TAG) ?>});
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',<?= json_encode($gtm) ?>);
</script>
