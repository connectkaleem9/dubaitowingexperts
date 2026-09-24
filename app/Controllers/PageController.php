<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Faq;
use App\Models\Service;
use App\Services\Schema;
use App\Services\Seo;

final class PageController extends Controller
{
    private const LEGAL = [
        '/privacy-policy/' => ['legal/privacy', 'Privacy Policy', 'How Dubai Towing Experts collects, uses and protects the personal information you share through this website, calls and WhatsApp.'],
        '/terms-and-conditions/' => ['legal/terms', 'Terms and Conditions', 'Terms for using the Dubai Towing Experts website and requesting recovery, towing and roadside assistance services.'],
        '/cookie-policy/' => ['legal/cookies', 'Cookie Policy', 'Which cookies and similar technologies the Dubai Towing Experts website uses, why, and how to control them.'],
        '/disclaimer/' => ['legal/disclaimer', 'Disclaimer', 'Important information about the general guidance published on the Dubai Towing Experts website.'],
    ];

    public function about(Request $request): void
    {
        $seo = Seo::page(
            '/about/',
            'About Us | ' . business('name'),
            'Dubai Towing Experts helps drivers across Dubai with car recovery, towing and roadside assistance. Learn how we work and how to reach us.'
        )->type('about')->crumbs('About', '/about/');
        $this->view('about', ['seo' => $seo, 'services' => Service::published()]);
    }

    public function faq(Request $request): void
    {
        $faqs = Faq::published();
        $seo = Seo::page(
            '/faq/',
            'Car Recovery FAQs – Dubai | ' . business('name'),
            'Answers to common questions about car recovery, towing and roadside assistance in Dubai: quotes, what to do while you wait, accidents and more.'
        )->type('faq')->crumbs('FAQ', '/faq/');
        if ($faqs !== []) {
            $seo->addSchema(['@id' => $seo->canonical() . '#webpage', 'mainEntity' => Schema::faqEntities($faqs)]);
        }

        $grouped = [];
        foreach ($faqs as $f) {
            $grouped[$f['category']][] = $f;
        }
        $this->view('faq', ['seo' => $seo, 'grouped' => $grouped]);
    }

    public function legal(Request $request): void
    {
        [$view, $title, $description] = self::LEGAL[$request->path()] ?? abort(404);
        $seo = Seo::page($request->path(), $title . ' | ' . business('name'), $description)
            ->type('legal')->crumbs($title, $request->path());
        $this->view($view, ['seo' => $seo, 'title' => $title]);
    }
}
