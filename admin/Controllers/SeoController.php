<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Area;
use App\Models\Media;
use App\Models\Post;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Services\Sitemap;
use App\Validation\Validator;

final class SeoController extends AdminController
{
    public function index(Request $request): void
    {
        $paths = array_keys(Sitemap::STATIC_PAGES);
        foreach (Service::all() as $s) {
            $paths[] = '/services/' . $s['slug'] . '/';
        }
        foreach (Area::all() as $a) {
            $paths[] = '/areas/' . $a['slug'] . '/';
        }
        foreach (Post::adminList() as $p) {
            $paths[] = '/blog/' . $p['slug'] . '/';
        }
        foreach (array_keys((array) config('landing')) as $slug) {
            $paths[] = '/landing/' . $slug . '/';
        }

        $this->view('seo/index', [
            'title' => 'SEO',
            'overrides' => SeoMeta::all(),
            'paths' => array_values(array_unique($paths)),
            // Still used here: the social sharing image is picked from the library, not uploaded.
            'library' => Media::paginate(1, 200)['items'],
            'sitemapCount' => count(Sitemap::entries()),
        ]);
    }

    public function save(Request $request): void
    {
        $v = Validator::make($request->all(), [
            'path' => 'required|max:255',
            'title' => 'nullable|max:70',
            'meta_description' => 'nullable|max:170',
        ]);
        $path = $request->input('path');
        $errors = $v->errors();
        // "in:" splits on commas, so robots values (which contain commas) are checked here.
        if (!in_array($request->input('robots'), ['', 'index,follow', 'noindex,follow', 'noindex,nofollow'], true)) {
            $errors['robots'] = 'Choose a valid robots setting.';
        }
        if (!preg_match('#^/[a-z0-9\-/]*/$#', $path) && $path !== '/') {
            $errors['path'] = 'Enter a site path with a trailing slash, e.g. /services/car-recovery/';
        }
        if ($errors !== []) {
            $this->invalid($errors, $request->all(), '/admin/seo/');
        }
        $ogId = $this->optionalId($request, 'og_image_id');
        SeoMeta::upsert(
            $path,
            $request->input('title') ?: null,
            $request->input('meta_description') ?: null,
            $request->input('robots') ?: null,
            $ogId !== null && Media::find($ogId) !== null ? $ogId : null
        );
        $this->log($request, 'seo_saved', 'seo', null, $path);
        $this->success('SEO settings saved for ' . $path, '/admin/seo/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $row = SeoMeta::find($id) ?? abort(404);
        SeoMeta::delete($id);
        $this->log($request, 'seo_deleted', 'seo', $id, (string) $row['path']);
        $this->success('Override removed — the page now uses its default title and description.', '/admin/seo/');
    }
}
