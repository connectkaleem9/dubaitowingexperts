<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\ActivityLog;

final class AuthController
{
    public function showLogin(Request $request): void
    {
        if (Auth::user() !== null) {
            redirect('/admin/');
        }
        header('X-Robots-Tag: noindex, nofollow');
        header('Cache-Control: no-store');
        echo View::render('admin:login', ['error' => Session::getFlash('error'), 'email' => Session::getFlash('email', '')], null);
    }

    public function login(Request $request): void
    {
        $email = mb_strtolower($request->input('email'));
        $password = (string) ($request->all()['password'] ?? '');
        if ($email === '' || $password === '' || strlen($password) > 1024) {
            Session::flash('error', 'Enter your email and password.');
            Session::flash('email', $email);
            redirect('/admin/login/');
        }
        $error = Auth::attempt($email, $password, $request->ip());
        if ($error !== null) {
            Session::flash('error', $error);
            Session::flash('email', $email);
            redirect('/admin/login/');
        }
        $intended = (string) Session::get('intended', '/admin/');
        Session::forget('intended');
        redirect(str_starts_with($intended, '/admin/') ? $intended : '/admin/');
    }

    public function logout(Request $request): void
    {
        $id = Auth::id();
        if ($id !== null) {
            ActivityLog::record($id, 'logout', 'admin', $id, null, $request->ip());
        }
        Auth::logout();
        redirect('/admin/login/');
    }
}
