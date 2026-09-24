<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Media;
use App\Models\Post;
use App\Services\HtmlSanitizer;
use App\Validation\Validator;

final class PostController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('posts/index', [
            'title' => 'Blog',
            'actions' => '<a class="a-btn a-btn--primary" href="/admin/posts/new/">Write guide</a>',
            'items' => Post::adminList(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->form(null);
    }

    public function edit(Request $request): void
    {
        $this->form(Post::find($this->id($request)) ?? abort(404));
    }

    private function form(?array $post): void
    {
        $this->view('posts/form', [
            'title' => $post ? 'Edit guide' : 'New guide',
            'actions' => $post && $post['status'] === 'published' ? '<a class="a-btn" href="/blog/' . e($post['slug']) . '/" target="_blank" rel="noopener">View on site ↗</a>' : '',
            'post' => $post,
            'image' => $post ? Media::find($post['featured_image_id']) : null,
            'library' => Media::paginate(1, 200)['items'],
        ]);
    }

    public function store(Request $request): void
    {
        $data = $this->validated($request, null, '/admin/posts/new/');
        $data['author_id'] = Auth::id();
        $id = Post::create($data);
        $this->saveImage($request, $id, null, $data['title']);
        $this->log($request, 'post_created', 'post', $id, $data['title']);
        $this->success('Guide saved.', '/admin/posts/' . $id . '/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $post = Post::find($id) ?? abort(404);
        $data = $this->validated($request, $post, '/admin/posts/' . $id . '/');
        Post::update($id, $data);
        $this->saveImage($request, $id, $post, $data['title']);
        $this->log($request, 'post_updated', 'post', $id, $data['title']);
        $this->success('Guide saved.', '/admin/posts/' . $id . '/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $post = Post::find($id) ?? abort(404);
        Post::delete($id);
        $this->log($request, 'post_deleted', 'post', $id, $post['title']);
        $this->success('Guide deleted.', '/admin/posts/');
    }

    private function validated(Request $request, ?array $post, string $back): array
    {
        $input = $request->all();
        $input['slug'] = $request->input('slug') !== '' ? $request->input('slug') : slugify($request->input('title'));
        $v = Validator::make($input, [
            'title' => 'required|max:160',
            'slug' => 'required|slug|max:160',
            'excerpt' => 'required|max:300',
            'body' => 'required|max:200000',
            'status' => 'required|in:draft,published',
        ]);
        $errors = $v->errors();
        if (!isset($errors['slug']) && Post::slugExists($input['slug'], (int) ($post['id'] ?? 0))) {
            $errors['slug'] = 'Another guide already uses this URL slug.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $input, $back);
        }
        $status = $request->input('status');
        $publishedAt = $request->input('published_at');
        return [
            'title' => $request->input('title'),
            'slug' => $input['slug'],
            'excerpt' => $request->input('excerpt'),
            'body' => HtmlSanitizer::clean($request->input('body')),
            'status' => $status,
            'published_at' => $publishedAt !== ''
                ? date('Y-m-d H:i:s', (int) strtotime($publishedAt))
                : ($status === 'published' ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : ($post['published_at'] ?? null)),
        ];
    }

    private function saveImage(Request $request, int $id, ?array $post, string $title): void
    {
        [$mediaId, $error] = $this->imageField($request, 'image', $post && $post['featured_image_id'] ? (int) $post['featured_image_id'] : null, $title);
        Post::update($id, ['featured_image_id' => $mediaId !== null && Media::find($mediaId) !== null ? $mediaId : null]);
        if ($error !== null) {
            $this->error('Saved, but the image failed: ' . $error, '/admin/posts/' . $id . '/');
        }
    }
}
