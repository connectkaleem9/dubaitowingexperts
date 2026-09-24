<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Area;
use App\Models\Media;
use App\Models\Service;
use App\Services\HtmlSanitizer;
use App\Validation\Validator;

final class ServiceController extends AdminController
{
    private const ICONS = ['truck' => 'Recovery truck', 'route' => 'Towing / route', 'wrench' => 'Roadside / tools', 'alert' => 'Breakdown / warning', 'car' => 'Car', 'tyre' => 'Tyre', 'battery' => 'Battery', 'pin' => 'Location', 'shield' => 'Shield'];

    public function index(Request $request): void
    {
        $this->view('services/index', [
            'title' => 'Services',
            'actions' => '<a class="a-btn a-btn--primary" href="/admin/services/new/">Add service</a>',
            'items' => Service::all(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->form(null);
    }

    public function edit(Request $request): void
    {
        $this->form(Service::find($this->id($request)) ?? abort(404));
    }

    private function form(?array $service): void
    {
        $this->view('services/form', [
            'title' => $service ? 'Edit service' : 'Add service',
            'actions' => $service && $service['is_published'] ? '<a class="a-btn" href="/services/' . e($service['slug']) . '/" target="_blank" rel="noopener">View on site ↗</a>' : '',
            'service' => $service,
            'icons' => self::ICONS,
            'image' => $service ? Media::find($service['image_id']) : null,
            'areas' => Area::all(),
            'linkedAreas' => $service ? Service::areaIds((int) $service['id']) : [],
        ]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, null, '/admin/services/new/');
        $id = Service::create($data);
        $this->afterSave($request, $id, null);
        $this->log($request, 'service_created', 'service', $id, $data['name']);
        $this->success('Service created.', '/admin/services/' . $id . '/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $service = Service::find($id) ?? abort(404);
        $data = $this->validated($request, $service, '/admin/services/' . $id . '/');
        Service::update($id, $data);
        $this->afterSave($request, $id, $service);
        $this->log($request, 'service_updated', 'service', $id, $data['name']);
        $this->success('Service saved.', '/admin/services/' . $id . '/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $service = Service::find($id) ?? abort(404);
        Service::delete($id);
        $this->log($request, 'service_deleted', 'service', $id, $service['name']);
        $this->error('Service deleted. Add a redirect for /services/' . $service['slug'] . '/ in config/redirects.php so the old URL does not 404.', '/admin/services/');
    }

    private function validated(Request $request, ?array $service, string $back): array
    {
        $input = $request->all();
        $input['slug'] = $request->input('slug') !== '' ? $request->input('slug') : slugify($request->input('name'));
        $v = Validator::make($input, [
            'name' => 'required|max:120',
            'slug' => 'required|slug|max:120',
            'h1' => 'required|max:160',
            'excerpt' => 'required|max:300',
            'intro' => 'nullable|max:1000',
            'body' => 'nullable|max:80000',
            'icon' => 'required|in:' . implode(',', array_keys(self::ICONS)),
            'whatsapp_message' => 'nullable|max:300',
            'sort_order' => 'nullable|int',
        ]);
        $errors = $v->errors();
        if (!isset($errors['slug']) && Service::slugExists($input['slug'], (int) ($service['id'] ?? 0))) {
            $errors['slug'] = 'Another service already uses this URL slug.';
        }
        if ($request->input('is_published') === '1' && trim(strip_tags($request->input('body'))) === '') {
            $errors['body'] = 'Write the page content before publishing.';
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
            'icon' => $request->input('icon'),
            'whatsapp_message' => $request->input('whatsapp_message') ?: null,
            'sort_order' => (int) $request->input('sort_order'),
            'is_published' => $request->input('is_published') === '1' ? 1 : 0,
        ];
    }

    private function afterSave(Request $request, int $id, ?array $service): void
    {
        [$mediaId, $error] = $this->imageField($request, 'image', $service && $service['image_id'] ? (int) $service['image_id'] : null, $request->input('name') . ' in Dubai');
        Service::update($id, ['image_id' => $mediaId !== null && Media::find($mediaId) !== null ? $mediaId : null]);

        $areaIds = array_map('intval', $request->inputArray('areas'));
        $valid = array_column(Area::all(), 'id');
        Service::syncAreas($id, array_values(array_intersect($areaIds, array_map('intval', $valid))));

        if ($error !== null) {
            $this->error('Saved, but the image failed: ' . $error, '/admin/services/' . $id . '/');
        }
    }
}
