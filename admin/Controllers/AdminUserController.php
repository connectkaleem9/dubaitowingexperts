<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Admin;
use App\Validation\Validator;

final class AdminUserController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('users/index', ['title' => 'Admin Users', 'items' => Admin::all()]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:191',
            'password' => 'required|min:12|max:200',
            'role' => 'required|in:owner,editor',
        ]);
        $errors = $v->errors();
        if (Admin::findByEmail($request->input('email')) !== null) {
            $errors['email'] = 'An admin with this email already exists.';
        }
        if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            $errors['password_confirm'] = 'The passwords do not match.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $data, '/admin/users/');
        }
        $id = Admin::create($request->input('name'), $request->input('email'), (string) $data['password'], $request->input('role'));
        $this->log($request, 'admin_created', 'admin', $id, $request->input('email'));
        $this->success('Admin user created.', '/admin/users/');
    }

    public function update(Request $request): void
    {
        $id = $this->id($request);
        $user = Admin::find($id) ?? abort(404);
        $data = $request->all();
        $v = Validator::make($data, [
            'name' => 'required|max:100',
            'role' => 'required|in:owner,editor',
            'password' => 'nullable|min:12|max:200',
        ]);
        $errors = $v->errors();
        $active = $request->input('is_active') === '1';
        $demoting = $user['role'] === 'owner' && ($request->input('role') !== 'owner' || !$active);
        if ($demoting && Admin::countActiveOwners() <= 1) {
            $errors['role'] = 'You cannot remove the last active owner.';
        }
        if (($data['password'] ?? '') !== '' && ($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            $errors['password_confirm'] = 'The passwords do not match.';
        }
        if ($errors !== []) {
            $this->invalid($errors, $data, '/admin/users/');
        }
        Admin::update($id, [
            'name' => $request->input('name'),
            'role' => $request->input('role'),
            'is_active' => $active ? 1 : 0,
            'locked_until' => null,
            'failed_logins' => 0,
        ]);
        if (($data['password'] ?? '') !== '') {
            Admin::setPassword($id, (string) $data['password']);
            $this->log($request, 'admin_password_reset', 'admin', $id);
        }
        $this->log($request, 'admin_updated', 'admin', $id, $user['email']);
        $this->success('Admin user updated.', '/admin/users/');
    }

    public function destroy(Request $request): void
    {
        $id = $this->id($request);
        $user = Admin::find($id) ?? abort(404);
        if ($id === Auth::id()) {
            $this->error('You cannot delete your own account.', '/admin/users/');
        }
        if ($user['role'] === 'owner' && Admin::countActiveOwners() <= 1) {
            $this->error('You cannot delete the last active owner.', '/admin/users/');
        }
        Admin::delete($id);
        $this->log($request, 'admin_deleted', 'admin', $id, $user['email']);
        $this->success('Admin user deleted.', '/admin/users/');
    }
}
