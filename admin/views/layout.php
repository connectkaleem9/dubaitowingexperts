<?php
/** @var string $content @var array $admin @var string $title */
use App\Core\Session;

$isOwner = ($admin['role'] ?? '') === 'owner';
$menu = [
    ['/admin/', 'Dashboard'],
    ['/admin/leads/', 'Contact Requests'],
    ['/admin/projects/', 'Projects'],
    ['/admin/reviews/', 'Reviews'],
    ['/admin/services/', 'Services'],
    ['/admin/areas/', 'Areas'],
    ['/admin/faqs/', 'FAQs'],
    ['/admin/posts/', 'Blog'],
    ['/admin/media/', 'Media'],
    ['/admin/seo/', 'SEO'],
];
if ($isOwner) {
    $menu[] = ['/admin/settings/', 'Site Settings'];
    $menu[] = ['/admin/users/', 'Admin Users'];
    $menu[] = ['/admin/activity/', 'Activity Logs'];
}
$menu[] = ['/admin/security/', 'Security'];
$path = current_path();
$success = Session::getFlash('success');
$error = Session::getFlash('error');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="<?= e(implode('; ', App\Middleware\SecurityHeaders::cspDirectives(csp_nonce()))) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title ?? 'Admin') ?> · Admin · <?= e(business('name')) ?></title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="a-shell">
    <aside class="a-side">
        <a class="a-brand" href="/admin/"><img src="/assets/img/logo-footer.webp" width="760" height="347" alt=""> <span>Admin</span></a>
        <nav aria-label="Admin">
            <ul>
                <?php foreach ($menu as [$href, $label]): ?>
                    <li><a href="<?= e($href) ?>"<?= ($href === '/admin/' ? $path === '/admin/' : str_starts_with($path, $href)) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="a-side__foot">
            <span><?= e($admin['name'] ?? '') ?> · <?= e($admin['role'] ?? '') ?></span>
            <a href="/" target="_blank" rel="noopener">View website ↗</a>
            <?= a_post_button('/admin/logout/', 'Sign out', 'a-link') ?>
        </div>
    </aside>
    <main id="main" class="a-main">
        <header class="a-head">
            <h1><?= e($title ?? 'Admin') ?></h1>
            <?php if (!empty($actions)): ?><div class="a-actions"><?= $actions ?></div><?php endif; ?>
        </header>
        <?php if ($success): ?><div class="a-alert a-alert--ok" role="status"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="a-alert a-alert--err" role="alert"><?= e($error) ?></div><?php endif; ?>
        <?= $content ?>
    </main>
</div>
<script src="<?= e(asset('js/admin.js')) ?>" defer nonce="<?= e(csp_nonce()) ?>"></script>
</body>
</html>
