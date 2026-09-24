<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Media;
use App\Models\Post;
use App\Models\Service;
use App\Services\Schema;
use App\Services\Seo;

final class BlogController extends Controller
{
    public function index(Request $request): void
    {
        $perPage = (int) config('app.per_page');
        $page = $request->queryInt('page', 1);
        $result = Post::paginatePublished($page, $perPage);
        $pager = $this->pagination($page, $result['total'], $perPage);

        $seo = Seo::page(
            '/blog/',
            'Driving & Breakdown Guides for Dubai | ' . business('name'),
            'Practical guides for Dubai drivers: what to do after a breakdown or accident, flat tyres on the highway, summer car care and how recovery works.',
            $result['total'] === 0 ? 'noindex,follow' : 'index,follow'
        )->type('blog')->crumbs('Blog', '/blog/');
        if ($page > 1) {
            $seo->path = '/blog/?page=' . $page;
            $seo->title = 'Page ' . $page . ' – ' . $seo->title;
        }

        $this->view('blog/index', [
            'seo' => $seo,
            'posts' => $result['items'],
            'images' => Media::findMany(array_column($result['items'], 'featured_image_id')),
            'pager' => $pager,
        ]);
    }

    public function show(Request $request): void
    {
        $post = Post::findPublishedBySlug($request->param('slug')) ?? abort(404);
        $path = '/blog/' . $post['slug'] . '/';
        $image = Media::find($post['featured_image_id'] ? (int) $post['featured_image_id'] : null);

        $seo = Seo::page($path, Seo::withBrand((string) $post['title']), $post['excerpt'])
            ->type('article')->crumbs('Blog', '/blog/')->crumbs($post['title'], $path);
        $seo->ogType = 'article';
        $seo->publishedTime = $post['published_at'];
        $seo->modifiedTime = $post['updated_at'];
        if ($image && $seo->ogImage === null) {
            $seo->ogImage = media_url($image, 1600);
        }
        $seo->addSchema(Schema::article($post, $path, $image ? media_url($image, 1600) : null));

        $this->view('blog/show', [
            'seo' => $seo,
            'post' => $post,
            'image' => $image,
            'latest' => Post::latest(3, (int) $post['id']),
            'services' => array_slice(Service::published(), 0, 4),
        ]);
    }
}
