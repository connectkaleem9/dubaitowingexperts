<?php
$gtm = (string) setting('gtm_id', '');
if (!preg_match('/^GTM-[A-Z0-9]{4,12}$/', $gtm)) {
    return;
}
?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtm) ?>" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
