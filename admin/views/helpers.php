<?php

declare(strict_types=1);

/*
 * Small form helpers for admin views (loaded by AdminController::view()).
 * Values are escaped here, so pass raw values.
 */

if (!function_exists('af_error')) {
    function af_error(string $name): string
    {
        $errors = errors();
        return isset($errors[$name]) ? '<p class="a-error" id="err-' . e($name) . '">' . e($errors[$name]) . '</p>' : '';
    }

    function af_val(string $name, mixed $current): string
    {
        $old = old($name, "\0");
        return $old !== "\0" ? $old : (string) ($current ?? '');
    }

    function af_input(string $name, string $label, mixed $value, array $attrs = [], string $hint = ''): string
    {
        $type = $attrs['type'] ?? 'text';
        unset($attrs['type']);
        $extra = '';
        foreach ($attrs as $k => $v) {
            $extra .= $v === true ? ' ' . e($k) : ' ' . e($k) . '="' . e($v) . '"';
        }
        $err = errors()[$name] ?? null;
        return '<div class="a-field' . ($err ? ' has-error' : '') . '"><label for="f-' . e($name) . '">' . e($label) . '</label>'
            . '<input id="f-' . e($name) . '" name="' . e($name) . '" type="' . e($type) . '" value="' . e($type === 'password' ? '' : af_val($name, $value)) . '"' . $extra
            . ($err ? ' aria-invalid="true" aria-describedby="err-' . e($name) . '"' : '') . '>'
            . ($hint !== '' ? '<small>' . e($hint) . '</small>' : '') . af_error($name) . '</div>';
    }

    function af_textarea(string $name, string $label, mixed $value, int $rows = 5, string $hint = '', bool $rich = false): string
    {
        $err = errors()[$name] ?? null;
        return '<div class="a-field' . ($err ? ' has-error' : '') . '"><label for="f-' . e($name) . '">' . e($label) . '</label>'
            . ($rich ? '<div class="a-toolbar" data-editor-for="f-' . e($name) . '">'
                . '<button type="button" data-wrap="h2">H2</button><button type="button" data-wrap="h3">H3</button>'
                . '<button type="button" data-wrap="p">P</button><button type="button" data-wrap="strong"><b>B</b></button>'
                . '<button type="button" data-wrap="em"><i>I</i></button><button type="button" data-list="ul">• List</button>'
                . '<button type="button" data-list="ol">1. List</button><button type="button" data-link>Link</button></div>' : '')
            . '<textarea id="f-' . e($name) . '" name="' . e($name) . '" rows="' . $rows . '"' . ($err ? ' aria-invalid="true"' : '') . '>' . e(af_val($name, $value)) . '</textarea>'
            . ($hint !== '' ? '<small>' . e($hint) . '</small>' : '') . af_error($name) . '</div>';
    }

    /** @param array<string|int, string> $options value => label */
    function af_select(string $name, string $label, mixed $value, array $options, string $empty = ''): string
    {
        $current = af_val($name, $value);
        $html = '<div class="a-field' . (isset(errors()[$name]) ? ' has-error' : '') . '"><label for="f-' . e($name) . '">' . e($label) . '</label><select id="f-' . e($name) . '" name="' . e($name) . '">';
        if ($empty !== '') {
            $html .= '<option value="">' . e($empty) . '</option>';
        }
        foreach ($options as $v => $l) {
            $html .= '<option value="' . e($v) . '"' . ((string) $v === $current ? ' selected' : '') . '>' . e($l) . '</option>';
        }
        return $html . '</select>' . af_error($name) . '</div>';
    }

    function af_checkbox(string $name, string $label, bool $checked, string $hint = ''): string
    {
        $old = old($name, "\0");
        $isChecked = $old !== "\0" ? $old === '1' : $checked;
        return '<div class="a-field a-check"><input type="hidden" name="' . e($name) . '" value="0">'
            . '<label><input type="checkbox" name="' . e($name) . '" value="1"' . ($isChecked ? ' checked' : '') . '> ' . e($label) . '</label>'
            . ($hint !== '' ? '<small>' . e($hint) . '</small>' : '') . '</div>';
    }

    /** Image field: preview + upload + optional pick-from-library + remove. */
    function af_image(string $name, string $label, ?array $media, array $library = [], string $hint = ''): string
    {
        $html = '<div class="a-field a-image' . (isset(errors()[$name]) ? ' has-error' : '') . '"><span class="a-label">' . e($label) . '</span>';
        if ($media) {
            $html .= '<div class="a-thumb">' . media_img($media, '160px') . '<span>' . e($media['alt_text'] ?: '(no alt text)') . '</span></div>'
                . '<label class="a-inline"><input type="checkbox" name="' . e($name) . '_remove" value="1"> Remove image</label>';
        }
        $html .= '<label class="a-inline" for="f-' . e($name) . '">Upload new</label><input id="f-' . e($name) . '" type="file" name="' . e($name) . '" accept="image/jpeg,image/png,image/webp">';
        if ($library !== []) {
            $html .= '<label class="a-inline" for="f-' . e($name) . '-pick">…or choose from media library</label><select id="f-' . e($name) . '-pick" name="' . e($name) . '_media_id"><option value="">—</option>';
            foreach ($library as $m) {
                $html .= '<option value="' . (int) $m['id'] . '">#' . (int) $m['id'] . ' ' . e(str_limit($m['alt_text'] ?: (string) $m['original_name'], 60)) . '</option>';
            }
            $html .= '</select>';
        }
        return $html . ($hint !== '' ? '<small>' . e($hint) . '</small>' : '') . af_error($name) . '</div>';
    }

    function a_status(string $status): string
    {
        return '<span class="a-badge a-badge--' . e($status) . '">' . e(ucwords(str_replace('_', ' ', $status))) . '</span>';
    }

    function a_pager(array $pager, string $base, array $query = []): string
    {
        if ($pager['pages'] <= 1) {
            return '';
        }
        $html = '<nav class="a-pager" aria-label="Pagination">';
        for ($p = 1; $p <= $pager['pages']; $p++) {
            $q = http_build_query(array_filter($query + ['page' => $p > 1 ? $p : null], static fn ($v) => $v !== null && $v !== ''));
            $html .= $p === $pager['page'] ? '<span aria-current="page">' . $p . '</span>' : '<a href="' . e($base . ($q ? '?' . $q : '')) . '">' . $p . '</a>';
        }
        return $html . '</nav>';
    }

    /** POST button form (for delete/status actions) with CSRF. */
    function a_post_button(string $action, string $label, string $class = 'a-btn', string $confirm = '', array $fields = []): string
    {
        $html = '<form method="post" action="' . e($action) . '" class="a-inline-form"' . ($confirm !== '' ? ' data-confirm="' . e($confirm) . '"' : '') . '>' . csrf_field();
        foreach ($fields as $k => $v) {
            $html .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
        }
        return $html . '<button type="submit" class="' . e($class) . '">' . e($label) . '</button></form>';
    }
}
