<?php

declare(strict_types=1);

/*
 * Area pages. Only areas with genuinely local, unique content are published (CLAUDE.md §6).
 * The rest are seeded unpublished as placeholders for the Local SEO agent / owner to complete.
 */
$published = [
    [
        'slug' => 'downtown-dubai',
        'name' => 'Downtown Dubai',
        'h1' => 'Car Recovery in Downtown Dubai',
        'sort_order' => 1,
        'excerpt' => 'Car recovery and towing in Downtown Dubai — from tower basements and mall car parks to Sheikh Zayed Road and Financial Centre Road.',
        'intro' => 'Stuck in a Downtown car park or broken down near the Boulevard? Send us your location and parking level and we\'ll agree the price before we come to you.',
        'body' => <<<'HTML'
<h2>Recovery in Downtown Dubai</h2>
<p>Downtown Dubai packs residential towers, hotels, offices and some of the city's busiest car parks into a small area around Burj Khalifa and Dubai Mall. That makes recovery here less about distance and more about <strong>access</strong>: getting to the right level of the right car park, or reaching a car stopped on a road with no easy place to pull over.</p>

<h2>Typical situations we're called for</h2>
<ul>
<li><strong>Cars that won't start in tower basements</strong> — often after being parked for a while in the heat</li>
<li><strong>Breakdowns in mall car parks</strong>, where you'll need to give the zone, level and bay number</li>
<li><strong>Stops on Financial Centre Road, Sheikh Mohammed bin Rashid Boulevard or Sheikh Zayed Road</strong>, where traffic is heavy and stopping space is limited</li>
<li><strong>Accident damage</strong> after a low-speed collision in slow Downtown traffic</li>
</ul>

<h2>Helping us find you in Downtown</h2>
<ul>
<li>Send a WhatsApp location pin — then add the building name, car park entrance, level and bay number</li>
<li>Tell us about height barriers or ramps if you're in a basement</li>
<li>Let building security know a recovery vehicle is coming; many towers need to approve access</li>
<li>During major events (such as New Year's Eve) some Downtown roads close — tell us if you know about closures near you</li>
</ul>

<h2>Where we can take your car</h2>
<p>Downtown sits right next to Sheikh Zayed Road and Al Khail Road, giving quick routes to workshops in Al Quoz and dealer service centres across the city. Tell us the destination when you call and we'll quote for it.</p>
HTML,
    ],
    [
        'slug' => 'dubai-marina',
        'name' => 'Dubai Marina',
        'h1' => 'Car Recovery in Dubai Marina',
        'sort_order' => 2,
        'excerpt' => 'Car recovery and towing in Dubai Marina and JBR — tower car parks, busy Marina roads and Sheikh Zayed Road breakdowns.',
        'intro' => 'Broken down in a Marina tower car park or on the way out to Sheikh Zayed Road? WhatsApp your location, building and parking level and we\'ll agree a price before we set off.',
        'body' => <<<'HTML'
<h2>Recovery in Dubai Marina</h2>
<p>Dubai Marina is one of the densest residential areas in the city — dozens of high-rise towers around the canal, with Jumeirah Beach Residence (JBR) on the seafront and Jumeirah Lakes Towers (JLT) just across Sheikh Zayed Road. Most cars here live in multi-level tower car parks, and the roads around the Marina loop are busy at almost any time of day.</p>

<h2>Typical situations we're called for</h2>
<ul>
<li><strong>Flat batteries in tower car parks</strong>, especially on cars that are used only occasionally</li>
<li><strong>Breakdowns on the Marina loop roads</strong> and King Salman bin Abdulaziz Al Saud Street, where there's little room to stop</li>
<li><strong>Sheikh Zayed Road breakdowns</strong> near the Marina and JLT interchanges</li>
<li><strong>Flat tyres</strong> discovered in the car park or at JBR drop-off areas</li>
</ul>

<h2>Helping us find you in the Marina</h2>
<ul>
<li>Share a location pin, the tower name and the car park entrance you use — many towers have entrances on different streets</li>
<li>Tell us the parking level and whether the car park has a low height limit</li>
<li>Ask your building security or concierge to expect a recovery vehicle</li>
<li>If you're on the road, tell us which direction you're facing and the nearest tram stop or landmark — the Marina tram line runs through the area and makes a good reference point</li>
</ul>

<h2>Nearby</h2>
<p>Close to the Marina are JBR, JLT, Dubai Internet City, Al Sufouh and Palm Jumeirah — if you're in one of these, just send your location pin. Wherever your car needs to go — a nearby garage, a dealer on Sheikh Zayed Road, or further — tell us when you call.</p>
HTML,
    ],
    [
        'slug' => 'business-bay',
        'name' => 'Business Bay',
        'h1' => 'Car Recovery in Business Bay',
        'sort_order' => 3,
        'excerpt' => 'Car recovery and towing in Business Bay — office and residential tower car parks, the canal-side roads, Al Khail Road and Business Bay Crossing.',
        'intro' => 'Car won\'t start in a Business Bay car park, or stopped on Al Khail Road? Send your location and we\'ll agree the price before we come to you.',
        'body' => <<<'HTML'
<h2>Recovery in Business Bay</h2>
<p>Business Bay combines office blocks, hotels and residential towers along the Dubai Water Canal, next to Downtown Dubai. Traffic is heavy at commuter times, construction is ongoing in parts of the district, and most cars are parked in basements or multi-level car parks shared by several buildings.</p>

<h2>Typical situations we're called for</h2>
<ul>
<li><strong>Cars that won't start at the end of the working day</strong> in office car parks</li>
<li><strong>Breakdowns on Al Khail Road</strong> (E44) and at the Business Bay Crossing approaches, where traffic is fast and stopping space is tight</li>
<li><strong>Flat tyres</strong> — construction debris on some internal roads causes punctures</li>
<li><strong>Minor collisions</strong> in congested junctions around Al Asayel Street and Marasi Drive</li>
</ul>

<h2>Helping us find you in Business Bay</h2>
<ul>
<li>Several towers can share one car park, so give us the building name <em>and</em> the car park entrance street</li>
<li>Note the level, bay number and any height barrier</li>
<li>On Al Khail Road, tell us your direction of travel and the last exit you passed</li>
<li>Canal-side roads loop and change names — a location pin avoids confusion</li>
</ul>

<h2>Where we can take your car</h2>
<p>Business Bay has quick links to Sheikh Zayed Road and Al Khail Road, so workshops in Al Quoz and dealer service centres across Dubai are within easy reach. Tell us the destination when you call.</p>
HTML,
    ],
    [
        'slug' => 'deira',
        'name' => 'Deira',
        'h1' => 'Car Recovery in Deira',
        'sort_order' => 4,
        'excerpt' => 'Car recovery and towing in Deira — busy older streets, street parking, the Creek crossings and roads near Dubai International Airport.',
        'intro' => 'Broken down on a busy Deira street, at a Creek crossing or near the airport? Call or WhatsApp your location and we\'ll agree the price before we set off.',
        'body' => <<<'HTML'
<h2>Recovery in Deira</h2>
<p>Deira is one of Dubai's oldest districts, on the northern side of Dubai Creek. Its streets are busy, many are narrow, and a lot of parking is on-street or in open lots rather than in towers. It's also where the main Creek crossings and the roads around Dubai International Airport meet, so traffic can back up quickly when a car stops.</p>

<h2>Typical situations we're called for</h2>
<ul>
<li><strong>Breakdowns on the Creek crossings</strong> — Al Maktoum Bridge, Al Garhoud Bridge, Infinity Bridge and Al Shindagha Tunnel approaches</li>
<li><strong>Cars stuck in street parking</strong> in areas like Al Rigga, Al Muraqqabat and Port Saeed</li>
<li><strong>Airport Road and Al Garhoud</strong> breakdowns on the way to or from DXB</li>
<li><strong>Overheating in stop-start traffic</strong> on Baniyas Road and Salahuddin Road</li>
</ul>

<h2>Helping us find you in Deira</h2>
<ul>
<li>Send a location pin and the nearest landmark — a hotel, mall, metro station or mosque</li>
<li>Tell us which side of the road you're on; many Deira roads have service roads running alongside them</li>
<li>If you're parked on the street, let us know if the car is boxed in by other vehicles</li>
<li>On a bridge or tunnel approach, tell us your direction of travel</li>
</ul>

<h2>Where we can take your car</h2>
<p>From Deira we can take your car to garages in Deira itself, across the Creek in Bur Dubai and Al Quoz, or to dealer service centres elsewhere in Dubai. Tell us where it needs to go when you call.</p>
HTML,
    ],
    [
        'slug' => 'bur-dubai',
        'name' => 'Bur Dubai',
        'h1' => 'Car Recovery in Bur Dubai',
        'sort_order' => 5,
        'excerpt' => 'Car recovery and towing in Bur Dubai, Karama and Mankhool — narrow streets, building car parks and Khalid Bin Al Waleed Road.',
        'intro' => 'Car broken down in Bur Dubai, Karama or Mankhool? WhatsApp your location and nearest landmark and we\'ll agree the price before we come to you.',
        'body' => <<<'HTML'
<h2>Recovery in Bur Dubai</h2>
<p>Bur Dubai sits on the southern side of Dubai Creek and takes in historic Al Fahidi, busy residential neighbourhoods like Al Mankhool, Al Karama and Oud Metha, and major routes such as Khalid Bin Al Waleed Road and Sheikh Khalifa Bin Zayed Road. Many buildings are mid-rise with small ground-level or basement car parks, and street parking is in high demand.</p>

<h2>Typical situations we're called for</h2>
<ul>
<li><strong>Cars that won't start</strong> in tight building car parks or residential streets</li>
<li><strong>Breakdowns on Khalid Bin Al Waleed Road</strong> and around BurJuman during busy periods</li>
<li><strong>Stops near the Creek crossings</strong> — Al Maktoum Bridge, Floating Bridge and Al Shindagha</li>
<li><strong>Accident damage</strong> after low-speed bumps in congested traffic</li>
</ul>

<h2>Helping us find you in Bur Dubai</h2>
<ul>
<li>Send a location pin and a nearby landmark — a metro station, mall, school or well-known shop</li>
<li>For building car parks, tell us the entrance, level and whether space to manoeuvre is tight</li>
<li>If the car is parked on a narrow street, let us know how close other cars are parked</li>
</ul>

<h2>Where we can take your car</h2>
<p>From Bur Dubai we can take your car to local workshops, to Al Quoz or Deira, or to a dealer service centre anywhere in Dubai. Tell us the destination when you call and we'll quote for it.</p>
HTML,
    ],
];

// Master Plan §7 areas not yet written — seeded unpublished (no content, not indexable).
$pending = [
    'Jumeirah', 'Al Barsha', 'Al Quoz', 'Jumeirah Village Circle', 'Dubai Silicon Oasis', 'Dubai Hills',
    'Arabian Ranches', 'International City', 'Mirdif', 'Al Nahda', 'Al Qusais', 'Rashidiya', 'Umm Suqeim',
    'Al Satwa', 'DIFC', 'Palm Jumeirah', 'Discovery Gardens', 'Motor City', 'Dubai Sports City',
    'Dubai Investment Park', 'Dubai Production City', 'Nad Al Sheba', 'Al Warqa', 'Dubai South', 'Al Garhoud',
];

$rows = [];
foreach ($published as $a) {
    $a['is_published'] = 1;
    $a['whatsapp_message'] = 'Hello Dubai Towing Experts, I need recovery in ' . $a['name'] . '. My location is: ';
    $rows[] = $a;
}
foreach ($pending as $i => $name) {
    $rows[] = [
        'slug' => slugify($name),
        'name' => $name,
        'h1' => 'Car Recovery in ' . $name,
        'sort_order' => 100 + $i,
        'excerpt' => '',
        'intro' => '',
        'body' => '',
        'whatsapp_message' => null,
        'is_published' => 0,
    ];
}
return $rows;
