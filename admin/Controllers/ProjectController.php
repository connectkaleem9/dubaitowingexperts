<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Area;
use App\Models\Media;
use App\Models\Project;
use App\Models\Service;
use App\Validation\Validator;

final class ProjectController extends AdminController
{
    public function index(Request $request): void
    {
        $page = $this->page($request);
        $status = $request->query('status');
        $q = $request->query('q');
        $result = Project::adminList($page, 20, $status, $q);
        $this->view('projects/index', [
            'title' => 'Projects',
            'actions' => '<a class="a-btn a-btn--primary" href="/admin/projects/new/">Add project</a>',
            'items' => $result['items'],
            'pager' => ['page' => $page, 'pages' => max(1, (int) ceil($result['total'] / 20))],
            'status' => $status,
            'q' => $q,
            'counts' => Project::countByStatus(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->form(null);
    }

    public function edit(Request $request): void
    {
        $project = Project::find($this->id($request)) ?? abort(404);
        $this->form($project);
    }

    private function form(?array $project): void
    {
        $this->view('projects/form', [
            'title' => $project ? 'Edit project' : 'Add project',
            'actions' => $project && $project['status'] === 'published'
                ? '<a class="a-btn" href="/projects/' . e($project['slug']) . '/" target="_blank" rel="noopener">View on site ↗</a>' : '',
            'project' => $project,
            'services' => array_column(Service::all(), 'name', 'id'),
            'areas' => array_column(Area::all(), 'name', 'id'),
            'before' => $project ? Media::find($project['before_image_id']) : null,
            'after' => $project ? Media::find($project['after_image_id']) : null,
        ]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, null, '/admin/projects/new/');
        $data['created_by'] = Auth::id();
        $id = Project::create($data);
        $this->saveImages($request, $id, null, '/admin/projects/' . $id . '/');
        $this->log($request, 'project_created', 'project', $id, $data['title']);
        $this->success('Project created.', '/admin/projects/' . $id . '/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $project = Project::find($id) ?? abort(404);
        $data = $this->validated($request, $project, '/admin/projects/' . $id . '/');
        Project::update($id, $data);
        $this->saveImages($request, $id, $project, '/admin/projects/' . $id . '/');
        $this->log($request, 'project_updated', 'project', $id, $data['title']);
        $this->success('Project saved.', '/admin/projects/' . $id . '/');
    }

    public function toggleStatus(Request $request): void
    {
        $id = $this->id($request);
        $project = Project::find($id) ?? abort(404);
        $publish = $project['status'] !== 'published';
        Project::update($id, [
            'status' => $publish ? 'published' : 'draft',
            'published_at' => $publish ? ($project['published_at'] ?? date('Y-m-d H:i:s')) : $project['published_at'],
        ]);
        $this->log($request, $publish ? 'project_published' : 'project_unpublished', 'project', $id, $project['title']);
        $this->success($publish ? 'Project published.' : 'Project moved to draft.', '/admin/projects/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $project = Project::find($id) ?? abort(404);
        Project::delete($id);
        $this->log($request, 'project_deleted', 'project', $id, $project['title']);
        $this->success('Project deleted. Its images remain in the media library.', '/admin/projects/');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?array $project, string $back): array
    {
        $input = $request->all();
        $slug = $request->input('slug') !== '' ? $request->input('slug') : slugify($request->input('title'));
        $input['slug'] = $slug;
        $v = Validator::make($input, [
            'title' => 'required|max:160',
            'slug' => 'required|slug|max:160',
            'service_id' => 'nullable|int',
            'area_id' => 'nullable|int',
            'project_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
        ]);
        $errors = $v->errors();
        if (!isset($errors['slug']) && Project::slugExists($slug, (int) ($project['id'] ?? 0))) {
            $errors['slug'] = 'Another project already uses this URL slug.';
        }
        $serviceId = $this->optionalId($request, 'service_id');
        $areaId = $this->optionalId($request, 'area_id');
        if ($serviceId !== null && Service::find($serviceId) === null) {
            $errors['service_id'] = 'Choose a valid service.';
        }
        if ($areaId !== null && Area::find($areaId) === null) {
            $errors['area_id'] = 'Choose a valid area.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $input, $back);
        }
        $status = $request->input('status');
        return [
            'title' => $request->input('title'),
            'slug' => $slug,
            'service_id' => $serviceId,
            'area_id' => $areaId,
            'project_date' => $request->input('project_date') ?: null,
            'status' => $status,
            'published_at' => $status === 'published' ? ($project['published_at'] ?? date('Y-m-d H:i:s')) : ($project['published_at'] ?? null),
        ];
    }

    private function saveImages(Request $request, int $id, ?array $project, string $back): void
    {
        $title = $request->input('title');
        $errors = [];
        $updates = [];
        foreach (['before_image_id' => ['before', $title . ' – before'], 'after_image_id' => ['after', $title . ' – after']] as $col => [$field, $alt]) {
            [$mediaId, $error] = $this->imageField($request, $field, $project ? ($project[$col] ? (int) $project[$col] : null) : null, $alt);
            if ($error !== null) {
                $errors[] = ucfirst($field) . ' photo: ' . $error;
            }
            $updates[$col] = $mediaId !== null && Media::find($mediaId) !== null ? $mediaId : null;
        }
        // Cards and listings show the featured image; there is no field for it any more, so it
        // follows the "after" shot — the finished job — and falls back to "before".
        $updates['featured_image_id'] = $updates['after_image_id'] ?? $updates['before_image_id'] ?? null;
        Project::update($id, $updates);
        if ($errors !== []) {
            $this->error('Saved, but: ' . implode(' ', $errors), $back);
        }
    }
}
