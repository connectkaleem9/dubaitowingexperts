<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Session;
use App\Models\Area;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Service;
use App\Services\FormGuard;
use App\Services\Mailer;
use App\Services\Seo;
use App\Validation\Validator;

final class ContactController extends Controller
{
    public const VEHICLE_TYPES = ['Sedan / hatchback', 'SUV / 4x4', 'Sports / luxury car', 'Motorbike', 'Van / pickup', 'Other'];

    public function show(Request $request): void
    {
        $seo = Seo::page(
            '/contact/',
            'Contact Us – 24/7 Call or WhatsApp | ' . business('name'),
            'Need car recovery or towing in Dubai? Call 052 585 1934 any time, WhatsApp your location, or send the form and we will call you back with a quote.'
        )->type('contact')->crumbs('Contact', '/contact/');

        $this->view('contact', [
            'seo' => $seo,
            'services' => Service::published(),
            'areas' => Area::published(),
            'faqs' => array_slice(Faq::forHome(4), 0, 4),
        ]);
    }

    public function store(Request $request): void
    {
        $formType = in_array($request->input('form_type'), ['contact', 'quote', 'landing'], true) ? $request->input('form_type') : 'contact';
        $back = $this->safeReturnPath($request->input('source_path'));

        $guard = FormGuard::check($request, 'lead', 5, 600);
        if ($guard === FormGuard::SPAM) {
            redirect('/thank-you/');
        }

        $data = $request->all();
        $v = Validator::make($data, [
            'name' => 'required|max:100|no_links',
            'phone' => 'required|phone',
            'email' => ($request->input('preferred_contact') === 'email' ? 'required' : 'nullable') . '|email|max:191',
            'service_id' => 'nullable|int',
            'location' => 'required|max:200',
            'vehicle_type' => 'nullable|in:' . implode(',', self::VEHICLE_TYPES),
            'message' => 'nullable|max:2000',
            'preferred_contact' => 'required|in:phone,whatsapp,email',
        ], [
            'location' => 'Your location',
            'preferred_contact' => 'contact method',
            'vehicle_type' => 'vehicle type',
        ]);

        $errors = $v->errors();
        if ($guard !== null) {
            $errors['_form'] = $guard;
        }
        $serviceId = (int) $request->input('service_id');
        $service = $serviceId > 0 ? Service::find($serviceId) : null;
        if ($serviceId > 0 && $service === null) {
            $errors['service_id'] = 'Choose a valid service.';
        }
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', array_diff_key($data, ['_token' => 1, '_ts' => 1]));
            redirect($back . '#lead-form');
        }

        $attribution = (array) Session::get('attribution', []);
        $lead = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email') ?: null,
            'service_id' => $service ? (int) $service['id'] : null,
            'service_text' => $service['name'] ?? null,
            'location' => $request->input('location'),
            'vehicle_type' => $request->input('vehicle_type') ?: null,
            'message' => $request->input('message') ?: null,
            'preferred_contact' => $request->input('preferred_contact'),
            'form_type' => $formType,
            'source_path' => mb_substr($back, 0, 255),
            'utm_source' => $attribution['utm_source'] ?? null,
            'utm_medium' => $attribution['utm_medium'] ?? null,
            'utm_campaign' => $attribution['utm_campaign'] ?? null,
            'utm_term' => $attribution['utm_term'] ?? null,
            'gclid' => $attribution['gclid'] ?? null,
            'ip_hash' => ip_hash($request->ip()),
            'user_agent' => $request->userAgent(),
        ];
        $id = Lead::create($lead);

        try {
            Mailer::notifyLead($id, $lead + ['service_label' => $service['name'] ?? 'Not specified']);
        } catch (\Throwable $e) {
            ErrorHandler::report($e);
        }

        Session::flash('lead_conversion', $formType === 'quote' ? 'quote_request' : 'form_submit');
        Session::flash('lead_name', $lead['name']);
        redirect('/thank-you/');
    }

    public function thankYou(Request $request): void
    {
        $seo = Seo::page('/thank-you/', 'Thank You | ' . business('name'), 'Your request has been received.', 'noindex,follow')->type('thank_you');
        $this->view('thank-you', [
            'seo' => $seo,
            'conversion' => Session::getFlash('lead_conversion'),
            'name' => Session::getFlash('lead_name'),
        ]);
    }

    /** Only allow returning to an internal path. */
    private function safeReturnPath(string $path): string
    {
        return preg_match('#^/[a-z0-9\-/]*$#', $path) ? $path : '/contact/';
    }
}
