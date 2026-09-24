<section class="section error-page">
    <div class="container">
        <h1>Sorry, something went wrong</h1>
        <p>Our website had a problem loading this page. You can still reach us directly.</p>
        <div class="btn-row">
            <a class="btn btn--call btn--lg" href="<?= e(tel_href()) ?>"><?= icon('phone') ?>Call <?= e(business('phone_display')) ?></a>
            <a class="btn btn--wa btn--lg" href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?>WhatsApp us</a>
        </div>
    </div>
</section>
