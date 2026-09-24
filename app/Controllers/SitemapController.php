<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\Sitemap;

final class SitemapController extends Controller
{
    public function xml(Request $request): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');
        echo Sitemap::xml();
    }
}
