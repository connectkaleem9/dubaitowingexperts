<?php
/** @var list<array> $items */
use App\Core\Auth;
use App\Models\Admin;
?>
<div class="a-table-wrap">
    <table class="a-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last sign-in</th><th><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($items as $u): ?>
            <tr>
                <td colspan="6">
                    <form class="a-form a-form--row" method="post" action="/admin/users/<?= (int) $u['id'] ?>/">
                        <?= csrf_field() ?>
                        <label>Name <input name="name" type="text" value="<?= e($u['name']) ?>" maxlength="100" required></label>
                        <span class="a-muted"><?= e($u['email']) ?></span>
                        <label>Role
                            <select name="role">
                                <?php foreach (Admin::ROLES as $value => $label): ?>
                                    <option value="<?= e($value) ?>"<?= $u['role'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1"<?= $u['is_active'] ? ' checked' : '' ?>> Active</label>
                        <label>New password <input name="password" type="password" autocomplete="new-password" minlength="12" placeholder="leave blank to keep"></label>
                        <label>Confirm <input name="password_confirm" type="password" autocomplete="new-password" minlength="12"></label>
                        <span class="a-muted"><?= e(format_date($u['last_login_at'], 'j M Y, H:i')) ?: 'never' ?></span>
                        <button class="a-btn a-btn--sm a-btn--primary" type="submit">Save</button>
                    </form>
                    <?php if ((int) $u['id'] !== Auth::id()): ?>
                        <?= a_post_button('/admin/users/' . (int) $u['id'] . '/delete/', 'Delete user', 'a-btn a-btn--sm a-btn--danger', 'Delete this admin user?') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<section class="a-card">
    <h2>Add an admin user</h2>
    <form class="a-form" method="post" action="/admin/users/">
        <?= csrf_field() ?>
        <div class="a-grid-2">
            <?= af_input('name', 'Name *', '', ['required' => true, 'maxlength' => 100]) ?>
            <?= af_input('email', 'Email *', '', ['type' => 'email', 'required' => true, 'maxlength' => 191]) ?>
        </div>
        <div class="a-grid-3">
            <?= af_input('password', 'Password *', '', ['type' => 'password', 'required' => true, 'minlength' => 12, 'autocomplete' => 'new-password'], 'At least 12 characters. Use a password manager.') ?>
            <?= af_input('password_confirm', 'Confirm password *', '', ['type' => 'password', 'required' => true, 'minlength' => 12, 'autocomplete' => 'new-password']) ?>
            <?= af_select('role', 'Role', 'editor', Admin::ROLES) ?>
        </div>
        <button class="a-btn a-btn--primary" type="submit">Create user</button>
    </form>
</section>
