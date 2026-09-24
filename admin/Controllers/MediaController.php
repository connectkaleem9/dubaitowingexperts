<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Media;
use App\Services\Uploader;

final class MediaController extends AdminController
{
    public function index(Request $request): void
    {
        $page = $this->page($request);
        $q = $request->query('q');
        $result = Media::paginate($page, 24, $q);
        $this->view('media/index', [
            'title' => 'Media',
            'items' => $result['items'],
            'pager' => ['page' => $page, 'pages' => max(1, (int) ceil($result['total'] / 24))],
            'q' => $q,
        ]);
    }

    public function upload(Request $request): void
    {
        $files = array_slice($request->files('files'), 0, 20);
        $alt = $request->input('alt_text');
        $ok = 0;
        $errors = [];
        foreach ($files as $i => $file) {
            try {
                $id = Uploader::image($file, $alt !== '' ? $alt . (count($files) > 1 ? ' ' . ($i + 1) : '') : pathinfo((string) $file['name'], PATHINFO_FILENAME), Auth::id());
                $this->log($request, 'media_uploaded', 'media', $id, (string) $file['name']);
                $ok++;
            } catch (\RuntimeException $e) {
                $errors[] = $file['name'] . ': ' . $e->getMessage();
            }
        }
        if ($errors !== []) {
            $this->error("Uploaded {$ok} image(s). Problems: " . implode(' ', $errors), '/admin/media/');
        }
        $this->success("Uploaded {$ok} image(s). Add descriptive alt text to each one — it matters for accessibility and image search.", '/admin/media/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        Media::find($id) ?? abort(404);
        Media::updateAlt($id, mb_substr($request->input('alt_text'), 0, 255));
        $this->log($request, 'media_updated', 'media', $id);
        $this->success('Alt text saved.', '/admin/media/?page=' . max(1, (int) $request->input('page')));
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $media = Media::find($id) ?? abort(404);
        $usages = Media::usages($id);
        if ($usages !== []) {
            $this->error('This image is still used by: ' . implode('; ', array_slice($usages, 0, 5)) . '. Remove it there first.', '/admin/media/');
        }
        Uploader::deleteFiles($media);
        Media::delete($id);
        $this->log($request, 'media_deleted', 'media', $id, (string) $media['path']);
        $this->success('Image deleted.', '/admin/media/');
    }
}
