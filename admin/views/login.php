<?php /** @var string|null $error @var string $email */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Sign in · <?= e(business('name')) ?> Admin</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="a-login-body">
<main class="a-login">
    <img class="a-login__logo" src="/assets/img/logo-header.webp" width="760" height="343" alt="<?= e(business('name')) ?>">
    <h1>Admin sign in</h1>
    <?php if ($error): ?><div class="a-alert a-alert--err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/admin/login/" class="a-form">
        <?= csrf_field() ?>
        <div class="a-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="username" required value="<?= e($email) ?>" autofocus>
        </div>
        <div class="a-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="a-btn a-btn--primary a-btn--block" type="submit">Sign in</button>
    </form>
    <p class="a-muted"><a href="/">← Back to website</a></p>
</main>
</body>
</html>
