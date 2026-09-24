<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Review;
use App\Models\Service;
use App\Validation\Validator;

final class ReviewController extends AdminController
{
    private const ACTIONS = [
        'approve'   => ['status' => 'approved'],
        'reject'    => ['status' => 'rejected', 'is_featured' => 0],
        'hide'      => ['status' => 'hidden', 'is_featured' => 0],
        'feature'   => ['is_featured' => 1],
        'unfeature' => ['is_featured' => 0],
    ];

    public function index(Request $request): void
    {
        $page = $this->page($request);
        $status = $request->query('status');
        $q = $request->query('q');
        $rating = $request->queryInt('rating');
        $result = Review::adminList($page, 20, $status, $q, $rating);
        $this->view('reviews/index', [
            'title' => 'Reviews',
            'actions' => '<a class="a-btn" href="/admin/reviews/new/">Add a review received offline</a>',
            'items' => $result['items'],
            'images' => Review::imagesFor(array_column($result['items'], 'id')),
            'pager' => ['page' => $page, 'pages' => max(1, (int) ceil($result['total'] / 20))],
            'status' => $status,
            'q' => $q,
            'rating' => $rating,
            'counts' => Review::countByStatus(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('reviews/form', ['title' => 'Add review', 'review' => null, 'services' => array_column(Service::all(), 'name', 'id')]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, '/admin/reviews/new/', true);
        $data['consent_at'] = date('Y-m-d H:i:s');
        $data['approved_at'] = $data['status'] === 'approved' ? date('Y-m-d H:i:s') : null;
        $id = Review::create($data);
        $this->log($request, 'review_created', 'review', $id, 'Offline review from ' . $data['name']);
        $this->success('Review added.', '/admin/reviews/');
    }

    public function edit(Request $request): void
    {
        $review = Review::find($this->id($request)) ?? abort(404);
        $this->view('reviews/form', [
            'title' => 'Edit review',
            'review' => $review,
            'images' => Review::images((int) $review['id']),
            'services' => array_column(Service::all(), 'name', 'id'),
        ]);
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $review = Review::find($id) ?? abort(404);
        $data = $this->validated($request, '/admin/reviews/' . $id . '/', false);
        if ($data['status'] === 'approved' && $review['approved_at'] === null) {
            $data['approved_at'] = date('Y-m-d H:i:s');
        }
        Review::update($id, $data);
        $this->log($request, 'review_updated', 'review', $id);
        $this->success('Review saved.', '/admin/reviews/' . $id . '/');
    }

    public function moderate(Request $request): void
    {
        $id = $this->id($request);
        $review = Review::find($id) ?? abort(404);
        $action = $request->input('action');
        $changes = self::ACTIONS[$action] ?? abort(400);
        if ($action === 'feature' && $review['status'] !== 'approved') {
            $this->error('Only approved reviews can be featured.', '/admin/reviews/');
        }
        if (($changes['status'] ?? null) === 'approved' && $review['approved_at'] === null) {
            $changes['approved_at'] = date('Y-m-d H:i:s');
        }
        Review::update($id, $changes);
        $this->log($request, 'review_' . $action, 'review', $id);
        $back = $request->input('back');
        $this->success('Review updated.', str_starts_with($back, '/admin/reviews/') ? $back : '/admin/reviews/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        Review::find($id) ?? abort(404);
        Review::delete($id);
        $this->log($request, 'review_deleted', 'review', $id);
        $this->success('Review deleted.', '/admin/reviews/');
    }

    private function validated(Request $request, string $back, bool $isNew): array
    {
        $rules = [
            'name' => 'required|max:100',
            'service_id' => 'nullable|int',
            'area_text' => 'nullable|max:120',
            'rating' => 'required|int|min_value:1|max_value:5',
            'body' => 'required|min:10|max:2000',
            'status' => 'required|in:pending,approved,rejected,hidden',
            'admin_note' => 'nullable|max:500',
        ];
        if ($isNew) {
            $rules['genuine'] = 'accepted';
        }
        $v = Validator::make($request->all(), $rules, ['genuine' => 'this is a genuine review the customer agreed to publish']);
        $errors = $v->errors();
        $serviceId = $this->optionalId($request, 'service_id');
        if ($serviceId !== null && Service::find($serviceId) === null) {
            $errors['service_id'] = 'Choose a valid service.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $request->all(), $back);
        }
        $status = $request->input('status');
        return [
            'name' => $request->input('name'),
            'service_id' => $serviceId,
            'area_text' => $request->input('area_text') ?: null,
            'rating' => (int) $request->input('rating'),
            'body' => $request->input('body'),
            'status' => $status,
            'is_featured' => $status === 'approved' && $request->input('is_featured') === '1' ? 1 : 0,
            'admin_note' => $request->input('admin_note') ?: null,
        ];
    }
}
