<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Review;
use App\Models\Service;
use App\Services\Seo;

/** Distraction-free Google Ads landing pages. Content in config/landing.php. */
final class LandingController extends Controller
{
    public function show(Request $request): void
    {
        $slug = $request->param('slug');
        $page = config('landing.' . $slug) ?? abort(404);
        $path = '/landing/' . $slug . '/';

        $seo = Seo::page($path, $page['title'] . ' | ' . business('name'), $page['lead'], 'noindex,follow')->type('landing');
        $service = Service::findPublishedBySlug($page['service_slug']);

        $this->view('landing/show', [
            'seo' => $seo,
            'waMessage' => $page['whatsapp'],
            'page' => $page,
            'service' => $service,
            'services' => Service::published(),
            'reviews' => Review::highlights(3, $service ? (int) $service['id'] : null) ?: Review::highlights(3),
        ], 'layouts/landing');
    }
}
