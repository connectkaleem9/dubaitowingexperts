<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\ActivityLog;
use App\Services\Uploader;

abstract class AdminController
{
    protected function view(string $view, array $data = []): void
    {
        require_once BASE_PATH . '/admin/views/helpers.php';
        header('Content-Type: text/html; charset=UTF-8');
        echo View::render('admin:' . $view, $data + ['admin' => Auth::user()], 'admin:layout');
    }

    protected function success(string $message, string $to): never
    {
        Session::flash('success', $message);
        redirect($to);
    }

    protected function error(string $message, string $to): never
    {
        Session::flash('error', $message);
        redirect($to);
    }

    /** @param array<string, string> $errors */
    protected function invalid(array $errors, array $old, string $to): never
    {
        Session::flash('errors', $errors);
        Session::flash('old', array_diff_key($old, ['_token' => 1, 'password' => 1, 'password_confirm' => 1, 'current_password' => 1]));
        Session::flash('error', 'Please fix the highlighted fields.');
        redirect($to);
    }

    protected function log(Request $request, string $action, ?string $entity = null, ?int $id = null, ?string $details = null): void
    {
        ActivityLog::record(Auth::id(), $action, $entity, $id, $details, $request->ip());
    }

    protected function id(Request $request, string $key = 'id'): int
    {
        return (int) $request->param($key);
    }

    /** Nullable positive int from a select/hidden input. */
    protected function optionalId(Request $request, string $key): ?int
    {
        $v = (int) $request->input($key);
        return $v > 0 ? $v : null;
    }

    /**
     * Handle an image field: upload new file if provided, clear if "remove" checked, else keep.
     * @return array{0: ?int, 1: ?string} [media id, error]
     */
    protected function imageField(Request $request, string $field, ?int $current, string $alt): array
    {
        $file = $request->file($field);
        if ($file !== null) {
            try {
                return [Uploader::image($file, $alt, Auth::id()), null];
            } catch (\RuntimeException $e) {
                return [$current, $e->getMessage()];
            }
        }
        $picked = (int) $request->input($field . '_media_id');
        if ($picked > 0) {
            return [$picked, null];
        }
        if ($request->input($field . '_remove') === '1') {
            return [null, null];
        }
        return [$current, null];
    }

    protected function page(Request $request): int
    {
        return max(1, $request->queryInt('page', 1));
    }
}
