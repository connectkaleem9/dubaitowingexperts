<?php

declare(strict_types=1);

/*
 * Single source of truth for NAP data (CLAUDE.md §2).
 * Values here are defaults; the admin "Site Settings" screen can override them.
 * Leave unknown facts as null — templates and schema skip null values.
 */
return [
    'name'           => 'Dubai Towing Experts',
    'domain'         => 'dubaitowingexperts.com',
    'phone_display'  => '052 585 1934',
    'phone_e164'     => '+971525851934',
    'whatsapp'       => '971525851934',
    'email'          => null,          // owner to confirm
    'city'           => 'Dubai',
    'region'         => 'Dubai',
    'country'        => 'AE',
    'country_name'   => 'United Arab Emirates',
    'street_address' => null,          // not provided — never invent
    'opening_hours'  => 'Mo-Su 00:00-23:59', // 24/7 — confirmed by the owner 2026-09-22 (decision D-010)
    'geo'            => null,          // ['lat' => ..., 'lng' => ...] once confirmed
    'social'         => [],            // profile URLs for schema sameAs
    'google_maps_url'=> null,          // Google Business Profile URL once verified
];
