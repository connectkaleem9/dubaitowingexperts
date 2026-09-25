/* Dubai Towing Experts — site behaviour. No dependencies. Events: docs/analytics-tracking.md */
(function () {
    'use strict';

    var dl = (window.dataLayer = window.dataLayer || []);
    var pageType = document.body.getAttribute('data-page-type') || 'page';

    function track(event, params) {
        var payload = { event: event, page_type: pageType };
        for (var k in params) { if (Object.prototype.hasOwnProperty.call(params, k)) { payload[k] = params[k]; } }
        dl.push(payload);
        // A dataLayer push is what Google Tag Manager listens for. Analytics loaded on its own
        // (gtag.js, no container) does not see those, so send it the event directly as well.
        if (typeof window.gtag === 'function') {
            var attrs = { page_type: pageType };
            for (var j in params) { if (Object.prototype.hasOwnProperty.call(params, j)) { attrs[j] = params[j]; } }
            window.gtag('event', event, attrs);
        }
    }

    /* Mobile navigation */
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('site-nav');
    if (toggle && nav) {
        var setOpen = function (open) {
            nav.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.querySelector('use').setAttribute('href', open ? '#i-close' : '#i-menu');
        };
        toggle.addEventListener('click', function () {
            setOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
        });
    }

    /* Click tracking: phone_click, whatsapp_click, etc. */
    document.addEventListener('click', function (e) {
        var el = e.target.closest ? e.target.closest('[data-track]') : null;
        if (!el) { return; }
        track(el.getAttribute('data-track'), {
            link_location: el.getAttribute('data-location') || '',
            link_url: el.getAttribute('href') || ''
        });
    });

    /* form_start: first interaction with a tracked form */
    document.querySelectorAll('form[data-track-form]').forEach(function (form) {
        var started = false;
        form.addEventListener('focusin', function () {
            if (started) { return; }
            started = true;
            track('form_start', { form_name: form.getAttribute('data-track-form') });
        });
        form.addEventListener('submit', function () {
            var btn = form.querySelector('[type="submit"]');
            if (btn) { btn.setAttribute('aria-disabled', 'true'); btn.textContent = 'Sending…'; }
        });
    });

    /* Conversions rendered by the server (thank-you page, review success) */
    var conversion = document.body.getAttribute('data-conversion');
    if (conversion) { track(conversion, {}); }
    document.querySelectorAll('[data-conversion-event]').forEach(function (el) {
        track(el.getAttribute('data-conversion-event'), {});
    });

    /* View events for key page types */
    var views = { service: 'service_view', area: 'area_view', project: 'project_view' };
    if (views[pageType]) { track(views[pageType], { page_path: location.pathname }); }

    /* Lead form: email only relevant when "Email" is the preferred contact */
    document.querySelectorAll('#lead-form').forEach(function (form) {
        var emailField = form.querySelector('[data-email-field]');
        if (!emailField) { return; }
        var sync = function () {
            var checked = form.querySelector('input[name="preferred_contact"]:checked');
            var needsEmail = checked && checked.value === 'email';
            var hasValue = emailField.querySelector('input').value !== '';
            emailField.hidden = !needsEmail && !hasValue && !emailField.classList.contains('field--error');
        };
        form.addEventListener('change', sync);
        sync();
    });

    /* "Share my location" buttons append a map link to the WhatsApp message */
    document.querySelectorAll('[data-share-location]').forEach(function (btn) {
        if (!('geolocation' in navigator)) { btn.hidden = true; return; }
        btn.addEventListener('click', function () {
            var base = btn.getAttribute('data-wa');
            btn.setAttribute('aria-busy', 'true');
            navigator.geolocation.getCurrentPosition(function (pos) {
                var link = 'https://maps.google.com/?q=' + pos.coords.latitude.toFixed(6) + ',' + pos.coords.longitude.toFixed(6);
                track('whatsapp_click', { link_location: 'share_location' });
                window.location.href = base + encodeURIComponent(' ' + link);
            }, function () {
                btn.removeAttribute('aria-busy');
                window.location.href = base;
            }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
        });
    });

    /* Sliders: pause/play control (WCAG 2.2.2 — moving content must be stoppable) */
    document.querySelectorAll('[data-marquee-toggle]').forEach(function (btn) {
        var target = btn.getAttribute('data-marquee-target');
        var marquee = target ? document.getElementById(target) : btn.closest('[data-marquee]');
        if (!marquee) { btn.hidden = true; return; }
        btn.addEventListener('click', function () {
            var paused = marquee.classList.toggle('is-paused');
            btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
            var label = btn.querySelector('[data-marquee-label]');
            if (label) { label.textContent = paused ? 'Play' : 'Pause'; }
            var use = btn.querySelector('use');
            if (use) { use.setAttribute('href', paused ? '#i-play' : '#i-pause'); }
        });
    });

    /* Review carousel: arrows + dots, wrapping, keyboard accessible */
    document.querySelectorAll('[data-carousel]').forEach(function (root) {
        var track = root.querySelector('.carousel-track');
        var slides = track ? Array.prototype.slice.call(track.children) : [];
        var dotsBox = root.querySelector('[data-carousel-dots]');
        if (!track || slides.length < 2) { return; }

        var index = 0;
        var dots = slides.map(function (_, i) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', 'Review ' + (i + 1));
            dot.addEventListener('click', function () { go(i); });
            if (dotsBox) { dotsBox.appendChild(dot); }
            return dot;
        });

        function go(next) {
            index = (next + slides.length) % slides.length;
            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            dots.forEach(function (d, i) { d.setAttribute('aria-selected', i === index ? 'true' : 'false'); });
            slides.forEach(function (s, i) { s.toggleAttribute('inert', i !== index); });
        }

        var prev = root.querySelector('[data-carousel-prev]');
        var next = root.querySelector('[data-carousel-next]');
        if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
        if (next) { next.addEventListener('click', function () { go(index + 1); }); }
        root.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') { go(index - 1); }
            if (e.key === 'ArrowRight') { go(index + 1); }
        });
        go(0);
    });

    /* Consent banner (only rendered when GTM is configured) */
    var banner = document.getElementById('consent');
    if (banner) {
        var stored = null;
        try { stored = localStorage.getItem('dre_consent'); } catch (err) { stored = null; }
        if (!stored) { banner.hidden = false; }
        banner.addEventListener('click', function (e) {
            var choice = e.target.getAttribute && e.target.getAttribute('data-consent');
            if (!choice) { return; }
            try { localStorage.setItem('dre_consent', choice); } catch (err) { /* private mode */ }
            if (typeof window.gtag === 'function') {
                window.gtag('consent', 'update', { ad_storage: choice, ad_user_data: choice, ad_personalization: choice, analytics_storage: choice });
            }
            banner.hidden = true;
        });
    }
})();
