<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\Admin;
use App\Models\ActivityLog;
use App\Validation\Validator;

final class SecurityController extends AdminController
{
    public function index(Request $request): void
    {
        $user = Auth::user() ?? abort(403);
        $this->view('security/index', [
            'title' => 'Security',
            'user' => $user,
            'logins' => ActivityLog::paginate(1, 15, 'login')['items'],
            'failed' => ActivityLog::paginate(1, 15, 'login_failed')['items'],
            'checks' => $this->checks($request),
        ]);
    }

    public function changePassword(Request $request): void
    {
        $user = Auth::user() ?? abort(403);
        $data = $request->all();
        $v = Validator::make($data, ['password' => 'required|min:12|max:200']);
        $errors = $v->errors();
        if (!password_verify((string) ($data['current_password'] ?? ''), (string) $user['password_hash'])) {
            $errors['current_password'] = 'Your current password is not correct.';
        }
        if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            $errors['password_confirm'] = 'The new passwords do not match.';
        }
        if ($errors !== []) {
            $this->invalid($errors, [], '/admin/security/');
        }
        Admin::setPassword((int) $user['id'], (string) $data['password']);
        Session::regenerate();
        $this->log($request, 'password_changed', 'admin', (int) $user['id']);
        $this->success('Password changed.', '/admin/security/');
    }

    /** @return list<array{0: string, 1: bool, 2: string}> label, pass, advice */
    private function checks(Request $request): array
    {
        return [
            ['HTTPS in use', $request->isSecure(), 'Serve the site over HTTPS only. Check your hosting SSL settings.'],
            ['Debug mode off', !config('app.debug'), 'Set APP_DEBUG=false in .env on the live site.'],
            ['APP_KEY set', strlen((string) config('app.key')) >= 32, 'Set a random 64-character APP_KEY in .env.'],
            ['Environment is production', config('app.env') === 'production', 'Set APP_ENV=production in .env on the live site.'],
            ['Uploads directory protected', is_file(BASE_PATH . '/public/uploads/.htaccess'), 'public/uploads/.htaccess must exist to stop scripts running there.'],
            ['.env outside public folder', !is_file(BASE_PATH . '/public/.env'), 'Never place .env inside the public folder.'],
        ];
    }
}
