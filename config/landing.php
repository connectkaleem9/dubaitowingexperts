<?php

declare(strict_types=1);

/*
 * Google Ads landing pages (/landing/{slug}/). noindex,follow — see decision D-006.
 * Each page mirrors one ad group's promise (docs/seo/google-ads-keywords.md).
 * Never add unconfirmed claims (24/7, arrival times, prices, licences) — CLAUDE.md §2.
 */
return [
    'car-recovery-dubai' => [
        'title' => 'Car Recovery in Dubai – Call Now',
        'h1' => 'Car Recovery in Dubai',
        'lead' => 'Broken down, stuck or unable to drive? Call or WhatsApp your location and we will tell you the price before we send a recovery truck.',
        'service_slug' => 'car-recovery',
        'whatsapp' => 'Hello Dubai Towing Experts, I need car recovery in Dubai. My location is: ',
        'points' => [
            'Available 24 hours a day, seven days a week',
            'Recovery for cars, SUVs and other light vehicles across Dubai',
            'Clear quote agreed with you before dispatch',
            'Share a WhatsApp location pin so the driver finds you quickly',
            'Delivery to your home, a garage or a location you choose',
        ],
        'steps' => ['Call or WhatsApp us with your location', 'Agree the price and destination', 'Your vehicle is loaded and delivered'],
    ],
    'emergency-towing-dubai' => [
        'title' => 'Emergency Towing in Dubai – Call Now',
        'h1' => 'Emergency Towing in Dubai',
        'lead' => 'Stopped on the road after a breakdown or accident? Call us now with your location and we will arrange a tow truck to move your vehicle safely.',
        'service_slug' => 'towing-service',
        'whatsapp' => 'Hello Dubai Towing Experts, I need emergency towing in Dubai. My location is: ',
        'points' => [
            'Emergency towing around the clock, day or night',
            'Towing after breakdowns and accidents',
            'Price confirmed before the truck is sent',
            'We can take your car to a garage, home or a place you choose',
            'Simple guidance on staying safe while you wait',
        ],
        'steps' => ['Move to safety and call us', 'Send your location on WhatsApp', 'We tow your vehicle to the agreed destination'],
    ],
    'roadside-assistance-dubai' => [
        'title' => 'Roadside Assistance in Dubai – Call Now',
        'h1' => 'Roadside Assistance in Dubai',
        'lead' => 'Flat tyre, dead battery or a car that will not start? Tell us what happened and where you are, and we will help you on the spot or recover the vehicle.',
        'service_slug' => 'roadside-assistance',
        'whatsapp' => 'Hello Dubai Towing Experts, I need roadside assistance in Dubai. My location is: ',
        'points' => [
            'Someone answers 24/7 — call or WhatsApp',
            'Help with common roadside problems',
            'If it cannot be fixed on the spot, we can recover the vehicle',
            'Quote agreed before anyone is sent',
            'Call or WhatsApp — whichever is easier for you',
        ],
        'steps' => ['Tell us the problem and your location', 'Agree the price', 'We help on site or recover the vehicle'],
    ],
];
