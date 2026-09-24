<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Media;
use App\Models\Project;
use App\Services\Seo;

final class ProjectController extends Controller
{
    public function index(Request $request): void
    {
        $perPage = (int) config('app.per_page');
        $page = $request->queryInt('page', 1);
        $result = Project::paginatePublished($page, $perPage);
        $pager = $this->pagination($page, $result['total'], $perPage);

        $path = '/projects/' . ($page > 1 ? '?page=' . $page : '');
        $seo = Seo::page(
            '/projects/',
            'Recent Car Recovery Jobs in Dubai | ' . business('name'),
            'Real recovery and towing jobs completed by Dubai Towing Experts across Dubai — the situation, the location and how the vehicle was moved.',
            $result['total'] === 0 ? 'noindex,follow' : 'index,follow'
        )->type('projects')->crumbs('Projects', '/projects/');
        $seo->path = $path;
        if ($page > 1) {
            $seo->title = 'Page ' . $page . ' – ' . $seo->title;
        }

        $this->view('projects/index', [
            'seo' => $seo,
            'projects' => $result['items'],
            'images' => Media::findMany(array_column($result['items'], 'featured_image_id')),
            'pager' => $pager,
        ]);
    }

    public function show(Request $request): void
    {
        $project = Project::findPublishedBySlug($request->param('slug')) ?? abort(404);
        $path = '/projects/' . $project['slug'] . '/';
        $featured = Media::find($project['featured_image_id'] ? (int) $project['featured_image_id'] : null);

        $seo = Seo::page(
            $path,
            Seo::withBrand((string) $project['title']),
            $project['excerpt'] !== '' ? $project['excerpt'] : str_limit((string) $project['body'], 155)
        )->type('project')->crumbs('Projects', '/projects/')->crumbs($project['title'], $path);
        $seo->ogType = 'article';
        if ($featured && $seo->ogImage === null) {
            $seo->ogImage = media_url($featured, 1600);
        }

        $related = Project::latestPublished(3, $project['service_id'] ? (int) $project['service_id'] : null, null, (int) $project['id']);

        $this->view('projects/show', [
            'seo' => $seo,
            'project' => $project,
            'featured' => $featured,
            'before' => Media::find($project['before_image_id'] ? (int) $project['before_image_id'] : null),
            'after' => Media::find($project['after_image_id'] ? (int) $project['after_image_id'] : null),
            'gallery' => Project::gallery((int) $project['id']),
            'related' => $related,
            'relatedImages' => Media::findMany(array_column($related, 'featured_image_id')),
        ]);
    }
}
