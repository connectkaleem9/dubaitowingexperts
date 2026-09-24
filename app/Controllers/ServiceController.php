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
use App\Services\Schema;
use App\Services\Seo;

final class ServiceController extends Controller
{
    public function index(Request $request): void
    {
        $seo = Seo::page(
            '/services/',
            'Recovery & Towing Services in Dubai | ' . business('name'),
            'Car recovery, towing, breakdown, accident and roadside assistance services in Dubai. See how each service works and call or WhatsApp for a quote.'
        )->type('services')->crumbs('Services', '/services/');

        $this->view('services/index', ['seo' => $seo, 'services' => Service::published()]);
    }

    public function show(Request $request): void
    {
        $service = Service::findPublishedBySlug($request->param('slug')) ?? abort(404);
        $path = '/services/' . $service['slug'] . '/';
        $areas = Area::forService((int) $service['id']);

        $seo = Seo::page(
            $path,
            $service['name'] . ' in Dubai | ' . business('name'),
            $service['excerpt']
        )->type('service')->crumbs('Services', '/services/')->crumbs($service['name'], $path);
        $seo->addSchema(Schema::service($service, $path, $areas));
        $image = Media::find($service['image_id'] ? (int) $service['image_id'] : null);
        if ($image && $seo->ogImage === null) {
            $seo->ogImage = media_url($image, 1600);
        }

        $related = array_values(array_filter(Service::published(), static fn (array $s): bool => $s['id'] !== $service['id']));
        $projects = Project::latestPublished(3, (int) $service['id']);

        $this->view('services/show', [
            'seo' => $seo,
            'waMessage' => $service['whatsapp_message'] ?: null,
            'service' => $service,
            'image' => $image,
            'areas' => $areas,
            'related' => array_slice($related, 0, 4),
            'faqs' => Faq::forService((int) $service['id']),
            'reviews' => Review::highlights(3, (int) $service['id']),
            'projects' => $projects,
            'projectImages' => Media::findMany(array_column($projects, 'featured_image_id')),
        ]);
    }
}
