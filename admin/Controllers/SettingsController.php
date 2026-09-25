<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Setting;
use App\Validation\Validator;

final class SettingsController extends AdminController
{
    /** key => [label, type, hint, rule] */
    public const FIELDS = [
        'business.email' => ['Business email', 'text', 'Shown on the contact page and in schema. Leave empty if you do not want to publish an email address.', 'nullable|email|max:191'],
        'business.street_address' => ['Street address', 'text', 'Only add a real address you are happy to publish. Leave empty if you have no public address.', 'nullable|max:200'],
        'business.opening_hours' => ['Opening hours', 'text', 'e.g. "Mo-Su 00:00-23:59" for 24/7, or "Mo-Sa 08:00-20:00". Leave empty until you are sure — do not publish hours you cannot keep.', 'nullable|max:120'],
        'business.google_maps_url' => ['Google Business Profile / Maps link', 'text', 'Paste the link to your Google Maps listing once it is verified.', 'nullable|url|max:255'],
        'business.social' => ['Social profile links', 'textarea', 'One URL per line (Instagram, Facebook, TikTok). Used in schema as sameAs.', 'nullable|max:1000'],
        'hero_image_id' => ['Homepage hero photo (optional override)', 'media', 'Leave empty to keep the main hero background photo. Choosing an image here shows it as a picture beside the headline instead — handy for a seasonal or promotional visual.', 'nullable|int'],
        'whatsapp_default_message' => ['Default WhatsApp message', 'textarea', 'Pre-filled text when someone taps a WhatsApp button.', 'nullable|max:300'],
        'fleet_description' => ['Recovery vehicles and equipment', 'textarea', 'Optional home-page section. Describe only equipment you really have. Leave empty to hide the section.', 'nullable|max:1000'],
        'notify_email' => ['Lead notification email', 'text', 'New enquiries are emailed here. They are always saved in Contact Requests as well.', 'nullable|email|max:191'],
        'ga4_id' => ['Google Analytics measurement ID', 'text', 'e.g. G-AB12CD34EF. Found in Analytics under Admin → Data streams. This alone is enough for Analytics; no Tag Manager needed.', 'nullable|max:20'],
        'search_console_token' => ['Google Search Console verification', 'text', 'From Search Console → HTML tag, paste only the content value (a long string of letters and numbers), not the whole tag. Keep it here permanently — Google re-checks it.', 'nullable|max:200'],
        'gtm_id' => ['Google Tag Manager ID', 'text', 'e.g. GTM-ABC1234. Only needed for Google Ads conversion tracking or more involved setups.', 'nullable|max:20'],
        'consent_default' => ['Cookie consent default', 'select', 'Denied shows a cookie banner and only enables analytics/ads cookies after the visitor accepts.', 'required|in:denied,granted'],
        'default_og_image' => ['Default social sharing image path', 'text', 'e.g. /assets/img/og-default.jpg', 'nullable|max:255'],
        'legal_updated' => ['Legal pages "last updated" date', 'text', 'Shown on the privacy, terms, cookie and disclaimer pages.', 'nullable|max:40'],
    ];

    public function index(Request $request): void
    {
        $this->view('settings/index', [
            'title' => 'Site Settings',
            'fields' => self::FIELDS,
            'values' => Setting::all(),
            'library' => \App\Models\Media::paginate(1, 200)['items'],
        ]);
    }

    public function save(Request $request): void
    {
        $rules = [];
        foreach (self::FIELDS as $key => [, , , $rule]) {
            $rules[$this->inputName($key)] = $rule;
        }
        $data = $request->all();
        $v = Validator::make($data, $rules);
        $errors = $v->errors();
        $gtm = $request->input($this->inputName('gtm_id'));
        if ($gtm !== '' && !preg_match('/^GTM-[A-Z0-9]{4,12}$/', $gtm)) {
            $errors[$this->inputName('gtm_id')] = 'Enter a valid container ID such as GTM-ABC1234.';
        }
        $ga4 = $request->input($this->inputName('ga4_id'));
        if ($ga4 !== '' && !preg_match('/^G-[A-Z0-9]{4,15}$/', $ga4)) {
            $errors[$this->inputName('ga4_id')] = 'Enter a valid measurement ID such as G-AB12CD34EF.';
        }
        // Google's own value is base64url; anything else is a whole tag pasted in by mistake.
        $token = $request->input($this->inputName('search_console_token'));
        if ($token !== '' && !preg_match('/^[A-Za-z0-9_-]{20,100}$/', $token)) {
            $errors[$this->inputName('search_console_token')] =
                'Paste only the content value from the meta tag Google gives you, not the whole tag.';
        }
        $heroId = (int) $request->input($this->inputName('hero_image_id'));
        if ($heroId > 0 && \App\Models\Media::find($heroId) === null) {
            $errors[$this->inputName('hero_image_id')] = 'Choose an image from the media library.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $data, '/admin/settings/');
        }
        foreach (array_keys(self::FIELDS) as $key) {
            Setting::set($key, $request->input($this->inputName($key)));
        }
        $this->log($request, 'settings_updated', 'settings');
        $this->success('Settings saved.', '/admin/settings/');
    }

    /** Setting keys contain dots, which PHP would turn into underscores in POST data. */
    public function inputName(string $key): string
    {
        return str_replace('.', '__', $key);
    }
}
