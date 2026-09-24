<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Area;
use App\Models\Faq;
use App\Models\Media;
use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use App\Services\SampleContent;
use App\Services\Seo;

final class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $seo = Seo::page(
            '/',
            '24/7 Car Recovery & Towing in Dubai | ' . business('name'),
            'Car recovery, towing and roadside assistance across Dubai, 24 hours a day. Call or WhatsApp 052 585 1934 — we agree the price before we dispatch.'
        )->type('home');

        $services = Service::published();
        // Every area we cover is listed (owner's instruction 2026-09-23). Areas with a published
        // page link to it; the rest are shown as plain tiles rather than links to thin pages.
        $areas = Area::all();
        $projects = Project::latestPublished(8); // the home slider loops through these
        // ?preview=sample lets a signed-in admin see the page filled out before real content exists.
        $preview = SampleContent::wanted($request);
        if ($preview) {
            $seo->robots = 'noindex,nofollow';
        }
        $reviews = $preview
            ? array_slice(SampleContent::reviews(), 0, 5)
            : Review::highlights(5);             // shown one at a time in the review carousel

        $this->view('home', [
            'seo' => $seo,
            'heroImage' => Media::find((int) setting('hero_image_id', '0')),
            'services' => $services,
            'serviceImages' => Media::findMany(array_column($services, 'image_id')),
            'areas' => $areas, // name-only tiles on the home page — no images by design (2026-09-23)
            'projects' => $projects,
            'projectImages' => Media::findMany(array_column($projects, 'featured_image_id')),
            'reviews' => $reviews,
            'reviewStats' => $preview ? SampleContent::reviewStats() : Review::approvedStats(),
            'faqs' => Faq::forHome(5),
        ]);
    }
}
