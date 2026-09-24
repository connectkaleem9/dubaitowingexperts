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
use App\Services\Seo;

final class AreaController extends Controller
{
    public function index(Request $request): void
    {
        $areas = Area::published();
        $seo = Seo::page(
            '/areas/',
            'Areas We Cover in Dubai | ' . business('name'),
            'Car recovery and towing across Dubai communities. Find your area, see local recovery notes, and call or WhatsApp us with your location.',
            $areas === [] ? 'noindex,follow' : 'index,follow'
        )->type('areas')->crumbs('Areas', '/areas/');

        // The full coverage list is shown as names; only areas with real local content have a page.
        $others = array_values(array_filter(Area::all(), static fn (array $a): bool => !$a['is_published']));

        $this->view('areas/index', ['seo' => $seo, 'areas' => $areas, 'others' => $others]);
    }

    public function show(Request $request): void
    {
        $area = Area::findPublishedBySlug($request->param('slug')) ?? abort(404);
        $path = '/areas/' . $area['slug'] . '/';

        $seo = Seo::page(
            $path,
            'Car Recovery ' . $area['name'] . ' | ' . business('name'),
            $area['excerpt']
        )->type('area')->crumbs('Areas', '/areas/')->crumbs($area['name'], $path);
        $image = Media::find($area['image_id'] ? (int) $area['image_id'] : null);
        if ($image && $seo->ogImage === null) {
            $seo->ogImage = media_url($image, 1600);
        }

        $services = Service::forArea((int) $area['id']) ?: Service::published();
        $projects = Project::latestPublished(3, null, (int) $area['id']);
        $nearby = array_values(array_filter(Area::published(), static fn (array $a): bool => $a['id'] !== $area['id']));

        $this->view('areas/show', [
            'seo' => $seo,
            'waMessage' => $area['whatsapp_message'] ?: 'Hello Dubai Towing Experts, I need recovery in ' . $area['name'] . '. My location is: ',
            'area' => $area,
            'image' => $image,
            'services' => $services,
            'faqs' => Faq::forArea((int) $area['id']),
            'projects' => $projects,
            'projectImages' => Media::findMany(array_column($projects, 'featured_image_id')),
            'reviews' => Review::highlights(3),
            'nearby' => array_slice($nearby, 0, 6),
        ]);
    }
}
