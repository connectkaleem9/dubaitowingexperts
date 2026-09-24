<?php

declare(strict_types=1);

use Admin\Controllers\ActivityController;
use Admin\Controllers\AdminUserController;
use Admin\Controllers\AreaController;
use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\FaqController;
use Admin\Controllers\LeadController;
use Admin\Controllers\MediaController;
use Admin\Controllers\PostController;
use Admin\Controllers\ProjectController;
use Admin\Controllers\ReviewController;
use Admin\Controllers\SecurityController;
use Admin\Controllers\SeoController;
use Admin\Controllers\ServiceController;
use Admin\Controllers\SettingsController;
use App\Middleware\Authenticate;
use App\Middleware\OwnerOnly;

/** @var App\Core\Router $router */

$router->get('/admin/login/', [AuthController::class, 'showLogin']);
$router->post('/admin/login/', [AuthController::class, 'login']);
$router->post('/admin/logout/', [AuthController::class, 'logout']);

$router->group([Authenticate::class], static function ($r): void {
    $r->get('/admin/', [DashboardController::class, 'index']);

    // Projects
    $r->get('/admin/projects/', [ProjectController::class, 'index']);
    $r->get('/admin/projects/new/', [ProjectController::class, 'create']);
    $r->post('/admin/projects/new/', [ProjectController::class, 'store']);
    $r->get('/admin/projects/{id}/', [ProjectController::class, 'edit']);
    $r->post('/admin/projects/{id}/', [ProjectController::class, 'update']);
    $r->post('/admin/projects/{id}/status/', [ProjectController::class, 'toggleStatus']);
    $r->post('/admin/projects/{id}/delete/', [ProjectController::class, 'destroy']);
    $r->post('/admin/projects/{id}/gallery/', [ProjectController::class, 'addGallery']);
    $r->post('/admin/projects/{id}/gallery/{linkid}/delete/', [ProjectController::class, 'removeGallery']);

    // Reviews
    $r->get('/admin/reviews/', [ReviewController::class, 'index']);
    $r->get('/admin/reviews/new/', [ReviewController::class, 'create']);
    $r->post('/admin/reviews/new/', [ReviewController::class, 'store']);
    $r->get('/admin/reviews/{id}/', [ReviewController::class, 'edit']);
    $r->post('/admin/reviews/{id}/', [ReviewController::class, 'update']);
    $r->post('/admin/reviews/{id}/moderate/', [ReviewController::class, 'moderate']);
    $r->post('/admin/reviews/{id}/delete/', [ReviewController::class, 'destroy']);

    // Leads
    $r->get('/admin/leads/', [LeadController::class, 'index']);
    $r->get('/admin/leads/export/', [LeadController::class, 'export']);
    $r->get('/admin/leads/{id}/', [LeadController::class, 'show']);
    $r->post('/admin/leads/{id}/', [LeadController::class, 'update']);
    $r->post('/admin/leads/{id}/delete/', [LeadController::class, 'destroy'], [OwnerOnly::class]);

    // Services & Areas
    $r->get('/admin/services/', [ServiceController::class, 'index']);
    $r->get('/admin/services/new/', [ServiceController::class, 'create']);
    $r->post('/admin/services/new/', [ServiceController::class, 'store']);
    $r->get('/admin/services/{id}/', [ServiceController::class, 'edit']);
    $r->post('/admin/services/{id}/', [ServiceController::class, 'update']);
    $r->post('/admin/services/{id}/delete/', [ServiceController::class, 'destroy'], [OwnerOnly::class]);

    $r->get('/admin/areas/', [AreaController::class, 'index']);
    $r->get('/admin/areas/new/', [AreaController::class, 'create']);
    $r->post('/admin/areas/new/', [AreaController::class, 'store']);
    $r->get('/admin/areas/{id}/', [AreaController::class, 'edit']);
    $r->post('/admin/areas/{id}/', [AreaController::class, 'update']);
    $r->post('/admin/areas/{id}/delete/', [AreaController::class, 'destroy'], [OwnerOnly::class]);

    // FAQs
    $r->get('/admin/faqs/', [FaqController::class, 'index']);
    $r->get('/admin/faqs/new/', [FaqController::class, 'create']);
    $r->post('/admin/faqs/new/', [FaqController::class, 'store']);
    $r->get('/admin/faqs/{id}/', [FaqController::class, 'edit']);
    $r->post('/admin/faqs/{id}/', [FaqController::class, 'update']);
    $r->post('/admin/faqs/{id}/delete/', [FaqController::class, 'destroy']);

    // Blog
    $r->get('/admin/posts/', [PostController::class, 'index']);
    $r->get('/admin/posts/new/', [PostController::class, 'create']);
    $r->post('/admin/posts/new/', [PostController::class, 'store']);
    $r->get('/admin/posts/{id}/', [PostController::class, 'edit']);
    $r->post('/admin/posts/{id}/', [PostController::class, 'update']);
    $r->post('/admin/posts/{id}/delete/', [PostController::class, 'destroy']);

    // Media
    $r->get('/admin/media/', [MediaController::class, 'index']);
    $r->post('/admin/media/upload/', [MediaController::class, 'upload']);
    $r->post('/admin/media/{id}/', [MediaController::class, 'update']);
    $r->post('/admin/media/{id}/delete/', [MediaController::class, 'destroy']);

    // SEO
    $r->get('/admin/seo/', [SeoController::class, 'index']);
    $r->post('/admin/seo/', [SeoController::class, 'save']);
    $r->post('/admin/seo/{id}/delete/', [SeoController::class, 'destroy']);

    // Owner-only
    $r->group([OwnerOnly::class], static function ($r): void {
        $r->get('/admin/settings/', [SettingsController::class, 'index']);
        $r->post('/admin/settings/', [SettingsController::class, 'save']);
        $r->get('/admin/users/', [AdminUserController::class, 'index']);
        $r->post('/admin/users/', [AdminUserController::class, 'store']);
        $r->post('/admin/users/{id}/', [AdminUserController::class, 'update']);
        $r->post('/admin/users/{id}/delete/', [AdminUserController::class, 'destroy']);
        $r->get('/admin/activity/', [ActivityController::class, 'index']);
    });

    // Security (own account)
    $r->get('/admin/security/', [SecurityController::class, 'index']);
    $r->post('/admin/security/password/', [SecurityController::class, 'changePassword']);
});
