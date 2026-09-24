<?php

declare(strict_types=1);

use App\Controllers\AreaController;
use App\Controllers\BlogController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\LandingController;
use App\Controllers\PageController;
use App\Controllers\ProjectController;
use App\Controllers\ReviewController;
use App\Controllers\ServiceController;
use App\Controllers\SitemapController;

/** @var App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/services/', [ServiceController::class, 'index']);
$router->get('/services/{slug}/', [ServiceController::class, 'show']);

$router->get('/areas/', [AreaController::class, 'index']);
$router->get('/areas/{slug}/', [AreaController::class, 'show']);

$router->get('/projects/', [ProjectController::class, 'index']);
$router->get('/projects/{slug}/', [ProjectController::class, 'show']);

$router->get('/reviews/', [ReviewController::class, 'index']);
$router->post('/reviews/', [ReviewController::class, 'store']);

$router->get('/contact/', [ContactController::class, 'show']);
$router->post('/contact/', [ContactController::class, 'store']);
$router->get('/thank-you/', [ContactController::class, 'thankYou']);

$router->get('/blog/', [BlogController::class, 'index']);
$router->get('/blog/{slug}/', [BlogController::class, 'show']);

$router->get('/landing/{slug}/', [LandingController::class, 'show']);

$router->get('/about/', [PageController::class, 'about']);
$router->get('/faq/', [PageController::class, 'faq']);
$router->get('/privacy-policy/', [PageController::class, 'legal']);
$router->get('/terms-and-conditions/', [PageController::class, 'legal']);
$router->get('/cookie-policy/', [PageController::class, 'legal']);
$router->get('/disclaimer/', [PageController::class, 'legal']);

$router->get('/sitemap.xml', [SitemapController::class, 'xml']);
