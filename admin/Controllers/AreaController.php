<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Area;
use App\Models\Media;
use App\Services\HtmlSanitizer;
use App\Validation\Validator;

final class AreaController extends AdminController
{
    /** Minimum body words before an area page may be published (no thin location pages — CLAUDE.md §6). */
    private const MIN_WORDS = 150;

    public function index(Request $request): void
    {
        $this->view('areas/index', [
            'title' => 'Areas',
            'actions' => '<a class="a-btn a-btn--primary" href="/admin/areas/new/">Add area</a>',
            'items' => Area::all(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->form(null);
    }

    public function edit(Request $request): void
    {
        $this->form(Area::find($this->id($request)) ?? abort(404));
    }

    private function form(?array $area): void
    {
        $this->view('areas/form', [
            'title' => $area ? 'Edit area' : 'Add area',
            'actions' => $area && $area['is_published'] ? '<a class="a-btn" href="/areas/' . e($area['slug']) . '/" target="_blank" rel="noopener">View on site ↗</a>' : '',
            'area' => $area,
            'image' => $area ? Media::find($area['image_id']) : null,
            'minWords' => self::MIN_WORDS,
        ]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, null, '/admin/areas/new/');
        $id = Area::create($data);
        $this->saveImage($request, $id, null);
        $this->log($request, 'area_created', 'area', $id, $data['name']);
        $this->success('Area created.', '/admin/areas/' . $id . '/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $area = Area::find($id) ?? abort(404);
        $data = $this->validated($request, $area, '/admin/areas/' . $id . '/');
        Area::update($id, $data);
        $this->saveImage($request, $id, $area);
        $this->log($request, 'area_updated', 'area', $id, $data['name']);
        $this->success('Area saved.', '/admin/areas/' . $id . '/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $area = Area::find($id) ?? abort(404);
        Area::delete($id);
        $this->log($request, 'area_deleted', 'area', $id, $area['name']);
        $this->error('Area deleted. Add a redirect for /areas/' . $area['slug'] . '/ in config/redirects.php if it was published.', '/admin/areas/');
    }

    private function validated(Request $request, ?array $area, string $back): array
    {
        $input = $request->all();
        $input['slug'] = $request->input('slug') !== '' ? $request->input('slug') : slugify($request->input('name'));
        $v = Validator::make($input, [
            'name' => 'required|max:120',
            'slug' => 'required|slug|max:120',
            'h1' => 'required|max:160',
            'excerpt' => 'nullable|max:300',
            'intro' => 'nullable|max:1000',
            'body' => 'nullable|max:80000',
            'whatsapp_message' => 'nullable|max:300',
            'sort_order' => 'nullable|int',
        ]);
        $errors = $v->errors();
        if (!isset($errors['slug']) && Area::slugExists($input['slug'], (int) ($area['id'] ?? 0))) {
            $errors['slug'] = 'Another area already uses this URL slug.';
        }
        $words = str_word_count(strip_tags($request->input('body')));
        if ($request->input('is_published') === '1') {
            if ($words < self::MIN_WORDS) {
                $errors['body'] = "Area pages need genuinely local content before they go live ({$words} words — at least " . self::MIN_WORDS . '). Describe local roads, parking, access and typical situations.';
            }
            if ($request->input('excerpt') === '') {
                $errors['excerpt'] = 'A summary is required for published area pages (it becomes the meta description).';
            }
        }
        if ($errors !== []) {
            $this->invalid($errors, $input, $back);
        }
        return [
            'name' => $request->input('name'),
            'slug' => $input['slug'],
            'h1' => $request->input('h1'),
            'excerpt' => $request->input('excerpt'),
            'intro' => $request->input('intro') ?: null,
            'body' => HtmlSanitizer::clean($request->input('body')),
            'whatsapp_message' => $request->input('whatsapp_message') ?: null,
            'sort_order' => (int) $request->input('sort_order'),
            'is_published' => $request->input('is_published') === '1' ? 1 : 0,
        ];
    }

    private function saveImage(Request $request, int $id, ?array $area): void
    {
        [$mediaId, $error] = $this->imageField($request, 'image', $area && $area['image_id'] ? (int) $area['image_id'] : null, 'Car recovery in ' . $request->input('name'));
        Area::update($id, ['image_id' => $mediaId !== null && Media::find($mediaId) !== null ? $mediaId : null]);
        if ($error !== null) {
            $this->error('Saved, but the image failed: ' . $error, '/admin/areas/' . $id . '/');
        }
    }
}
