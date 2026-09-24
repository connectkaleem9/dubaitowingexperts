<?php
$services = nav_services();
$socials = array_values(array_filter(array_map('trim', explode("\n", (string) setting('business.social', '')))));
$socialIcon = static function (string $url): string {
    return match (true) {
        str_contains($url, 'facebook') => 'facebook',
        str_contains($url, 'instagram') => 'instagram',
        str_contains($url, 'youtube') || str_contains($url, 'youtu.be') => 'youtube',
        str_contains($url, 'linkedin') => 'linkedin',
        default => 'arrow',
    };
};
$socialName = static function (string $url): string {
    return match (true) {
        str_contains($url, 'facebook') => 'Facebook',
        str_contains($url, 'instagram') => 'Instagram',
        str_contains($url, 'youtube') || str_contains($url, 'youtu.be') => 'YouTube',
        str_contains($url, 'linkedin') => 'LinkedIn',
        default => 'Social profile',
    };
};
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <?= partial('logo', ['href' => '/', 'variant' => 'footer']) ?>
                <p class="footer-about">Car recovery, towing and roadside assistance across Dubai, 24 hours a day. Tell us where you are and we agree the price before we set off.</p>
            </div>

            <nav aria-labelledby="f-quick">
                <h2 id="f-quick">Quick Links</h2>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/about/">About Us</a></li>
                    <li><a href="/services/">Our Services</a></li>
                    <li><a href="/areas/">Service Areas</a></li>
                    <li><a href="/projects/">Projects</a></li>
                    <li><a href="/reviews/">Reviews</a></li>
                    <li><a href="/faq/">FAQ</a></li>
                    <li><a href="/blog/">Blog &amp; Guides</a></li>
                    <li><a href="/contact/">Contact</a></li>
                </ul>
            </nav>

            <?php if ($services !== []): ?>
            <nav aria-labelledby="f-services">
                <h2 id="f-services">Our Services</h2>
                <div class="footer-cols-2">
                    <ul>
                        <?php foreach (array_slice($services, 0, (int) ceil(count($services) / 2)) as $s): ?>
                            <li><a href="/services/<?= e($s['slug']) ?>/"><?= e($s['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul>
                        <?php foreach (array_slice($services, (int) ceil(count($services) / 2)) as $s): ?>
                            <li><a href="/services/<?= e($s['slug']) ?>/"><?= e($s['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>

            <div>
                <h2 id="f-contact">Contact Us</h2>
                <address class="footer-nap" aria-labelledby="f-contact">
                    <a href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="footer"><?= icon('phone') ?><span><?= e(business('phone_display')) ?></span></a>
                    <a href="<?= e(whatsapp_href()) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-location="footer"><?= icon('whatsapp') ?><span>WhatsApp us</span></a>
                    <span><?= icon('pin') ?><span><?= e(business('street_address') ? business('street_address') . ', ' : '') ?><?= e(business('city')) ?> – <?= e(business('country_name')) ?></span></span>
                    <?php if (business('email')): ?>
                        <a href="mailto:<?= e(business('email')) ?>"><?= icon('mail') ?><span><?= e(business('email')) ?></span></a>
                    <?php endif; ?>
                    <?php if (business('opening_hours')): ?>
                        <span><?= icon('clock') ?><span>24/7 emergency service</span></span>
                    <?php endif; ?>
                </address>
            </div>

            <?php if ($socials !== []): ?>
            <div>
                <h2 id="f-social">Follow Us</h2>
                <div class="social-row" aria-labelledby="f-social">
                    <?php foreach ($socials as $url): ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener me" aria-label="<?= e($socialName($url)) ?>"><?= icon($socialIcon($url)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= e(business('name')) ?>. All rights reserved.</span>
            <nav aria-label="Legal">
                <a href="/privacy-policy/">Privacy Policy</a>
                <a href="/terms-and-conditions/">Terms &amp; Conditions</a>
                <a href="/disclaimer/">Disclaimer</a>
                <a href="/cookie-policy/">Cookie Policy</a>
            </nav>
            <span class="footer-sign">Designed for a safer Dubai <?= icon('heart', 'icon icon--heart') ?></span>
        </div>
    </div>
</footer>
