<?php

declare(strict_types=1);

/*
 * Service pages (Master Plan §8, merged per decision D-003).
 * Copy rules: CLAUDE.md §2 — no 24/7, arrival times, prices, licences, fleet size or years.
 * Owner must confirm each service is offered (PROJECT_STATUS.md → Owner questions).
 */
return [
    [
        'slug' => 'car-recovery',
        'name' => 'Car Recovery',
        'h1' => 'Car Recovery in Dubai',
        'icon' => 'truck',
        'sort_order' => 1,
        'excerpt' => 'Car recovery anywhere in Dubai for vehicles that won\'t start or can\'t be driven safely — with the price agreed before we dispatch.',
        'intro' => 'If your car won\'t start, has broken down or simply shouldn\'t be driven, we\'ll recover it and take it to your garage, the dealer or your home. Call or WhatsApp your location and we\'ll agree the price first.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, I need car recovery. My location is: ',
        'body' => <<<'HTML'
<h2>When you need car recovery</h2>
<p>Car recovery means moving a vehicle that can't be driven — or shouldn't be — from where it is to where it needs to go. Drivers in Dubai usually call us when:</p>
<ul>
<li>The car won't start at home, at work or in a mall car park</li>
<li>A warning light, strange noise or overheating means it isn't safe to keep driving</li>
<li>The car has been damaged in an accident (see <a href="/services/accident-recovery/">accident recovery</a>)</li>
<li>A gearbox, clutch or suspension problem has left it stuck</li>
<li>You've bought a non-running car and need it collected</li>
<li>The car has been parked for a long time and needs to go to a garage before it can be used again</li>
</ul>

<h2>How car recovery works</h2>
<ol>
<li><strong>Call or WhatsApp us.</strong> Tell us what happened and send a location pin.</li>
<li><strong>Tell us about the car.</strong> Make, model, whether it's automatic or 4x4, and anything unusual (lowered suspension, damage, a flat tyre).</li>
<li><strong>Agree the price and destination.</strong> We confirm the cost before anyone is sent.</li>
<li><strong>We load and deliver the vehicle</strong> to the address you gave us and keep you updated on WhatsApp.</li>
</ol>

<h2>Moving your car the right way</h2>
<p>How a car is moved matters. Many modern cars — automatics, all-wheel-drive and 4x4 vehicles, and low sports cars — should be carried with none of their wheels turning on the road. That's why we ask about your vehicle before we set off: so we can plan how to load and secure it without adding to the problem.</p>

<h2>What to have ready</h2>
<ul>
<li>Your exact location (a WhatsApp location pin is best)</li>
<li>The car's make, model, colour and plate number</li>
<li>The keys, and the parking level or bay number if you're in a car park</li>
<li>The destination address — your garage, the dealer or home</li>
<li>After an accident, the police report reference (garages normally need it before repairs)</li>
</ul>

<h2>Car recovery, towing or roadside help?</h2>
<p>If the problem might be fixed where the car is — a flat tyre, for example — <a href="/services/roadside-assistance/">roadside assistance</a> may be all you need. If the car needs to go somewhere, that's recovery or <a href="/services/towing-service/">towing</a>. Not sure? Describe what's happening when you call and we'll tell you honestly which one fits.</p>
HTML,
    ],
    [
        'slug' => 'towing-service',
        'name' => 'Towing Service',
        'h1' => 'Towing Service in Dubai',
        'icon' => 'route',
        'sort_order' => 2,
        'excerpt' => 'Tow truck service in Dubai to move your car to a garage, home or any address you choose — after a breakdown, an accident or when it just won\'t run.',
        'intro' => 'Need a tow truck in Dubai? Whether your car has broken down, been damaged or simply won\'t run, we\'ll tow it to the destination you choose — at a price agreed before we set off.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, I need a tow truck. My location is: ',
        'body' => <<<'HTML'
<h2>Common reasons for a tow</h2>
<ul>
<li><strong>Breakdowns</strong> — engine, electrical or gearbox faults that stop the car</li>
<li><strong>Accident damage</strong> — when the car isn't safe or legal to drive</li>
<li><strong>Garage and dealer visits</strong> — taking a non-running car in for diagnosis or repair</li>
<li><strong>Moving a car you've bought or sold</strong> that can't be driven</li>
<li><strong>Relocating a vehicle</strong> between home, work or storage</li>
</ul>

<h2>Towing to a garage or dealer</h2>
<p>Tell us where the car needs to go when you call. If it's going to an agency service centre or a specific workshop, it helps to call them first so they're expecting the car — some have set drop-off hours or need a job card. For accident repairs, garages in the UAE normally need a copy of the police report before work begins.</p>

<h2>Tell us about the vehicle</h2>
<p>The right way to tow depends on the vehicle. Automatics, all-wheel-drive cars and 4x4s are generally best carried with all four wheels off the ground, and low cars need a gentle loading angle. When you call, we'll ask for the make and model so we can plan how to move it safely.</p>

<h2>What affects the price of a tow?</h2>
<p>Every job is different, so we quote each one individually. The main factors are:</p>
<ul>
<li>The distance from pickup to destination</li>
<li>The type and condition of the vehicle</li>
<li>Access — a street, a tower basement or a highway hard shoulder</li>
<li>Anything extra needed to load the car safely</li>
</ul>
<p>We tell you the price before we dispatch, so there are no surprises.</p>

<h2>Related services</h2>
<p>If your car has simply broken down on the road, see <a href="/services/breakdown-recovery/">breakdown recovery</a>. After a collision, see <a href="/services/accident-recovery/">accident recovery</a>.</p>
HTML,
    ],
    [
        'slug' => 'roadside-assistance',
        'name' => 'Roadside Assistance',
        'h1' => 'Roadside Assistance in Dubai',
        'icon' => 'wrench',
        'sort_order' => 3,
        'excerpt' => 'Roadside help in Dubai for flat tyres and cars that won\'t start — and recovery to a garage if the problem can\'t be sorted on the spot.',
        'intro' => 'Stuck at the side of the road? Tell us what\'s happening and where you are. If it can be sorted on the spot we\'ll tell you, and if it can\'t, we\'ll recover the car to a garage of your choice.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, I need roadside assistance. My location is: ',
        'body' => <<<'HTML'
<h2>Common roadside problems</h2>
<p>Most roadside calls in Dubai come down to a handful of problems:</p>
<ul>
<li><strong>A flat or burst tyre</strong> — see <a href="/services/flat-tyre-assistance/">flat tyre assistance</a></li>
<li><strong>A car that won't start</strong> — often a battery that has given up in the heat</li>
<li><strong>Overheating</strong> — especially in summer traffic</li>
<li><strong>Warning lights</strong> that make you unsure whether to keep driving</li>
</ul>
<p>Describe the problem when you call — what you saw, heard or smelt, and what the dashboard is showing. That helps us work out whether it can be dealt with at the roadside or whether the car needs to go to a garage.</p>

<h2>When roadside help isn't enough</h2>
<p>Some faults can't be fixed safely at the side of the road. If that's the case, we'll say so straight away and quote to <a href="/services/car-recovery/">recover the car</a> to your garage, the dealer or home — so you don't pay for a visit that won't solve the problem.</p>

<h2>Stay safe while you wait</h2>
<ul>
<li>Switch on your hazard lights and pull as far off the road as you safely can.</li>
<li>On highways such as Sheikh Zayed Road or Emirates Road, leave the car on the side away from traffic and wait behind the barrier if there is one.</li>
<li>Don't stand between your car and moving traffic.</li>
<li>Place your warning triangle behind the car if it's safe to walk there.</li>
<li>In summer, keep water with you and avoid sitting in a closed car with the engine off.</li>
</ul>

<h2>Helpful information to share</h2>
<ul>
<li>Your location pin on WhatsApp</li>
<li>The car's make and model</li>
<li>Whether you have a usable spare tyre and the locking wheel-nut key (if fitted)</li>
<li>Where you'd like the car taken if it can't be fixed on the spot</li>
</ul>
HTML,
    ],
    [
        'slug' => 'breakdown-recovery',
        'name' => 'Breakdown Recovery',
        'h1' => 'Car Breakdown Recovery in Dubai',
        'icon' => 'alert',
        'sort_order' => 4,
        'excerpt' => 'Broken down on a Dubai road or highway? Call for breakdown recovery — we\'ll move your car off the road and to the garage you choose.',
        'intro' => 'A breakdown on a busy Dubai road is stressful and can be dangerous. Get yourself somewhere safe, then call or WhatsApp your location — we\'ll agree the price and recover your car to the garage you choose.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, my car has broken down. My location is: ',
        'body' => <<<'HTML'
<h2>Broken down? Do this first</h2>
<ol>
<li><strong>Get out of the traffic.</strong> Steer onto the hard shoulder or into a side road if the car is still rolling.</li>
<li><strong>Hazard lights on.</strong> At night, keep sidelights on too.</li>
<li><strong>Get everyone out on the safe side</strong> — away from the traffic — and wait behind a barrier if there is one.</li>
<li><strong>Warning triangle</strong> behind the car, only if you can place it safely.</li>
<li><strong>Call us</strong> with your location, or send a WhatsApp location pin.</li>
</ol>
<p>If your car has stopped in a live lane and you can't move it, or anyone is at risk, call the police on 999 first.</p>

<h2>Breakdowns we see often in Dubai</h2>
<ul>
<li><strong>Overheating</strong> in slow summer traffic, when cooling systems are working hardest</li>
<li><strong>Battery failure</strong> — heat shortens battery life, and many fail without warning</li>
<li><strong>Tyre blowouts</strong> on highways, often on under-inflated or older tyres</li>
<li><strong>Running out of fuel</strong> on long, quiet stretches between exits</li>
<li><strong>Electrical and gearbox faults</strong> that leave the car stuck in place</li>
</ul>

<h2>Breaking down on a highway</h2>
<p>Sheikh Zayed Road (E11), Al Khail Road (E44), Mohammed Bin Zayed Road (E311) and Emirates Road (E611) carry fast, heavy traffic. Give us the direction you were travelling, the nearest exit or interchange, and any landmark you can see — a location pin is even better. That's how we reach you quickly on roads where it's easy to end up on the wrong side.</p>

<h2>After we recover your car</h2>
<p>We'll take it to your preferred garage, the dealer, or home. If you don't have a garage in mind, tell us what's wrong and where you're based so you can decide where it should go.</p>

<p>Related: <a href="/services/roadside-assistance/">roadside assistance</a> · <a href="/services/towing-service/">towing service</a> · <a href="/blog/car-breakdown-dubai/">what to do when your car breaks down in Dubai</a></p>
HTML,
    ],
    [
        'slug' => 'accident-recovery',
        'name' => 'Accident Recovery',
        'h1' => 'Accident Recovery in Dubai',
        'icon' => 'car',
        'sort_order' => 5,
        'excerpt' => 'Vehicle recovery after an accident in Dubai — we move damaged cars to your garage, dealer or insurer\'s workshop once it\'s safe to do so.',
        'intro' => 'After an accident, your first priorities are safety and reporting it. When it\'s time to move a damaged car, call or WhatsApp us and we\'ll take it to your garage, dealer or insurer\'s workshop.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, I need accident recovery. My location is: ',
        'body' => <<<'HTML'
<h2>1. Make sure everyone is safe</h2>
<p>If anyone is hurt, call 999 for police or 998 for an ambulance straight away. Turn on your hazard lights and move people away from traffic.</p>

<h2>2. Report the accident</h2>
<p>In the UAE, every accident must be reported to the police, however minor. For minor collisions with no injuries, Dubai Police lets drivers report the accident through the <a href="https://www.dubaipolice.gov.ae/" target="_blank" rel="noopener noreferrer">Dubai Police app</a> or DubaiNow. For anything more serious, wait for the police to attend and follow their instructions — including when and whether vehicles can be moved.</p>
<p>Keep the police report reference. Garages and insurers normally need it before they will start repairs.</p>

<h2>3. Take photos before the car is moved</h2>
<p>Photograph the damage, the position of the vehicles, number plates and the scene. It only takes a minute and can make insurance claims much simpler.</p>

<h2>4. Arrange recovery</h2>
<p>Check your insurance first — some policies include recovery. If yours doesn't, or you'd prefer to arrange your own, call us with your location and tell us:</p>
<ul>
<li>What damage the car has (for example a damaged wheel, leaking fluids or airbags deployed)</li>
<li>Whether the car can roll and steer</li>
<li>Where it needs to go — your garage, the dealer or your insurer's approved workshop</li>
</ul>
<p>A car with accident damage should not be driven if it isn't safe or roadworthy. We'll agree the price with you and recover it without driving it.</p>

<h2>Where we can take your car</h2>
<p>Anywhere you choose — your own garage, an agency service centre, an insurer-approved workshop, or home while you sort out your claim.</p>

<p>Read more: <a href="/blog/car-accident-dubai-what-to-do/">what to do after a minor car accident in Dubai</a></p>
HTML,
    ],
    [
        'slug' => 'flat-tyre-assistance',
        'name' => 'Flat Tyre Assistance',
        'h1' => 'Flat Tyre Assistance in Dubai',
        'icon' => 'tyre',
        'sort_order' => 6,
        'excerpt' => 'Flat tyre or blowout in Dubai? We\'ll help fit your spare at the roadside, or recover your car to a tyre shop if the spare can\'t be used.',
        'intro' => 'A flat tyre on a busy road is no place to be struggling with a jack. Call or WhatsApp your location and we\'ll help fit your spare — or recover the car to a tyre shop if that isn\'t possible.',
        'whatsapp_message' => 'Hello Dubai Towing Experts, I have a flat tyre. My location is: ',
        'body' => <<<'HTML'
<h2>Got a flat? Do this first</h2>
<ul>
<li>Slow down gradually and hold the wheel straight — don't brake hard.</li>
<li>Pull over somewhere flat and away from traffic. On a highway, use the hard shoulder or the next exit if you can reach it safely at low speed.</li>
<li>Hazard lights on, everyone out on the side away from traffic.</li>
<li>Don't try to change a tyre on the traffic side of a busy road.</li>
</ul>

<h2>How we help</h2>
<p>If you have a usable spare, we'll help fit it so you can drive to a tyre shop. If there's no spare, the spare is flat too, the wheel is damaged, or the car uses run-flat tyres that have been driven on for too long, the safest option is to <a href="/services/car-recovery/">recover the car</a> to a tyre shop or garage — we'll tell you which applies when you call.</p>

<h2>What to have ready</h2>
<ul>
<li>Your location pin</li>
<li>Whether you have a spare wheel (many newer cars only have a repair kit)</li>
<li>The locking wheel-nut key, if your car has locking nuts — it's often in the glovebox or boot</li>
</ul>

<h2>Why tyres fail in Dubai</h2>
<p>Road surfaces in summer get extremely hot, which increases pressure and stress on tyres. Under-inflation, old rubber and small cracks in the sidewall all raise the risk of a blowout. Check your pressures when the tyres are cold, look for cracks and bulges, and check the four-digit date code on the sidewall (week and year of manufacture) — ageing tyres are more likely to fail even if the tread looks fine.</p>

<p>Related: <a href="/services/roadside-assistance/">roadside assistance</a> · <a href="/blog/flat-tyre-highway-dubai/">what to do with a flat tyre on a Dubai highway</a></p>
HTML,
    ],
];
