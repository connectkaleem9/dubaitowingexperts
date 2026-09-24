/* Admin helpers: confirm destructive actions, slug preview, simple HTML formatting toolbar. */
(function () {
    'use strict';

    /* Confirm before destructive POSTs */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        var message = form.getAttribute && form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            e.preventDefault();
        }
    });

    /* Suggest a slug from the title while the slug field is untouched */
    var title = document.getElementById('f-title') || document.getElementById('f-name');
    var slug = document.getElementById('f-slug');
    if (title && slug && slug.value === '') {
        var touched = false;
        slug.addEventListener('input', function () { touched = true; });
        title.addEventListener('input', function () {
            if (touched) { return; }
            slug.value = title.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80);
        });
    }

    /* Formatting toolbar: wraps the selected text in HTML tags */
    document.querySelectorAll('.a-toolbar').forEach(function (bar) {
        var field = document.getElementById(bar.getAttribute('data-editor-for'));
        if (!field) { return; }
        bar.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn) { return; }
            var start = field.selectionStart, end = field.selectionEnd;
            var selected = field.value.slice(start, end);
            var replacement = null;

            if (btn.hasAttribute('data-wrap')) {
                var tag = btn.getAttribute('data-wrap');
                replacement = '<' + tag + '>' + (selected || tag === 'p' ? selected : 'text') + '</' + tag + '>';
            } else if (btn.hasAttribute('data-list')) {
                var listTag = btn.getAttribute('data-list');
                var lines = (selected || 'First item\nSecond item').split(/\r?\n/).filter(function (l) { return l.trim() !== ''; });
                replacement = '<' + listTag + '>\n' + lines.map(function (l) { return '  <li>' + l.trim() + '</li>'; }).join('\n') + '\n</' + listTag + '>';
            } else if (btn.hasAttribute('data-link')) {
                var url = window.prompt('Link URL (e.g. /services/car-recovery/)', '/');
                if (!url) { return; }
                replacement = '<a href="' + url.replace(/"/g, '&quot;') + '">' + (selected || url) + '</a>';
            }
            if (replacement === null) { return; }
            field.setRangeText(replacement, start, end, 'end');
            field.focus();
        });
    });

    /* Character counters for SEO-sensitive fields */
    [['f-title', 60], ['f-meta_description', 155], ['f-excerpt', 155]].forEach(function (pair) {
        var field = document.getElementById(pair[0]);
        if (!field) { return; }
        var limit = pair[1];
        var note = document.createElement('small');
        field.parentNode.appendChild(note);
        var update = function () {
            var n = field.value.length;
            note.textContent = n + ' characters' + (n > limit ? ' — longer than the ' + limit + ' recommended, Google may cut it off' : '');
            note.className = n > limit ? 'a-warn' : 'a-muted';
        };
        field.addEventListener('input', update);
        update();
    });
})();
