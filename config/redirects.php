<?php

declare(strict_types=1);

/*
 * Permanent (301) redirects: old path => new path. Keep docs/seo/redirect-map.md in sync.
 * Paths are exact matches (with or without trailing slash — list both if needed).
 */
return [
    // Battery jump start was removed at the owner's request (2026-09-23); the nearest page is roadside assistance.
    '/services/battery-jump-start/' => '/services/roadside-assistance/',
    '/services/battery-jump-start'  => '/services/roadside-assistance/',
    '/services/vehicle-recovery/' => '/services/car-recovery/',
    '/services/vehicle-recovery'  => '/services/car-recovery/',
    '/home/'                      => '/',
    '/index.html'                 => '/',
];
