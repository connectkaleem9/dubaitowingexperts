<?php /** @var string|null $error @var string $email */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Security-Policy" content="<?= e(implode('; ', App\Middleware\SecurityHeaders::cspDirectives(csp_nonce()))) ?>">
<meta name="robots" content="noindex,nofollow">
<title>Sign in · <?= e(business('name')) ?> Admin</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="alternate icon" href="/favicon.ico" sizes="16x16 32x32 48x48">
<?php // Shown inside the normal site header and footer, so site.css loads before admin.css. ?>
<link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="a-body a-login-body">
<?= partial('icons') ?>
<a class="skip-link" href="#main">Skip to content</a>
<?= partial('topbar') ?>
<?= partial('header') ?>
<main id="main" class="a-login">
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
<?= partial('footer') ?>
<script src="<?= e(asset('js/app.js')) ?>" defer nonce="<?= e(csp_nonce()) ?>"></script>
</body>
</html>
