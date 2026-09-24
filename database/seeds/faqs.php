<?php

declare(strict_types=1);

/*
 * FAQs. Answers only state facts supported by business information (CLAUDE.md §2).
 * No answers on hours, payment methods, prices or response times until the owner provides them.
 * 'service' / 'area' are slugs resolved by the seeder.
 */
return [
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 1,
        'question' => 'How much does car recovery cost in Dubai?',
        'answer' => '<p>It depends on the job: how far the vehicle needs to go, the type of vehicle, where it is (a street, a basement car park or a highway) and anything extra needed to load it safely. Call or WhatsApp us with your location and destination and we will tell you the price before we send anyone.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 2,
        'question' => 'How do I send you my location?',
        'answer' => '<p>The easiest way is WhatsApp: open a chat with us, tap the attachment (paperclip or +) icon, choose <strong>Location</strong> and send your current location. On our website you can also tap <strong>Send my location</strong> to open WhatsApp with a map link already added. If you are in a car park, add the building name, level and bay number.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 0,
        'question' => 'Are you available 24 hours?',
        'answer' => '<p>Yes. We answer calls and WhatsApp messages 24 hours a day, seven days a week, including weekends and public holidays.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 3,
        'question' => 'Which areas do you cover?',
        'answer' => '<p>We cover Dubai — residential communities, business districts, car parks and main roads. See our <a href="/areas/">areas page</a> for local details. We do not currently work in the other emirates.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 6,
        'question' => 'How quickly can you reach me?',
        'answer' => '<p>It depends on where you are, the traffic at that moment and the type of vehicle needed, so we do not promise a fixed time. When you call we will tell you an honest arrival estimate for your situation before you decide — and we will keep you updated if anything changes.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 1, 'sort_order' => 4,
        'question' => 'Can you take my car to my own garage or the dealer?',
        'answer' => '<p>Yes. Tell us the destination when you call — your preferred garage, an agency service centre, an insurer-approved workshop or your home — and we will include it in the quote.</p>',
    ],
    [
        'category' => 'Getting help', 'show_on_home' => 0, 'sort_order' => 5,
        'question' => 'What information should I have ready when I call?',
        'answer' => '<ul><li>Your exact location (a WhatsApp location pin is best)</li><li>The make, model, colour and plate number of the vehicle</li><li>What happened and whether the car can roll and steer</li><li>Where you would like the vehicle taken</li><li>For car parks: the building, level and bay number</li></ul>',
    ],
    [
        'category' => 'Safety', 'show_on_home' => 1, 'sort_order' => 1,
        'question' => 'What should I do while I wait for recovery?',
        'answer' => '<p>Turn on your hazard lights, move away from traffic and wait somewhere safe — on a highway, behind the barrier if there is one, on the side away from the traffic. Place a warning triangle behind the car only if it is safe to do so. In hot weather keep water with you and avoid waiting in a closed car with the engine off. If anyone is injured or in danger, call 999.</p>',
    ],
    [
        'category' => 'Accidents', 'show_on_home' => 1, 'sort_order' => 1, 'service' => 'accident-recovery',
        'question' => 'Do I need a police report before my car is moved after an accident?',
        'answer' => '<p>In the UAE every accident must be reported to the police. For minor accidents with no injuries, Dubai Police allows reports through its app or DubaiNow; for anything more serious, wait for the police and follow their instructions about moving vehicles. Garages and insurers normally need the police report before they start repairs, so keep the reference number.</p>',
    ],
    [
        'category' => 'Accidents', 'show_on_home' => 0, 'sort_order' => 2, 'service' => 'accident-recovery',
        'question' => 'Should I take photos before my damaged car is recovered?',
        'answer' => '<p>Yes. Photograph the damage, the positions of the vehicles, number plates and the surrounding scene before anything is moved (as long as it is safe). These photos can help with your police report and insurance claim.</p>',
    ],
    [
        'category' => 'Services', 'show_on_home' => 0, 'sort_order' => 1, 'service' => 'towing-service',
        'question' => 'What is the difference between towing and car recovery?',
        'answer' => '<p>People use the words interchangeably. In general, <strong>recovery</strong> means getting a vehicle that cannot be driven out of wherever it has stopped and transporting it, while <strong>towing</strong> is often used for moving a vehicle from one place to another. Either way, tell us the situation and where the car needs to go and we will plan the right way to move it.</p>',
    ],
    [
        'category' => 'Services', 'show_on_home' => 0, 'sort_order' => 2, 'service' => 'roadside-assistance',
        'question' => 'Can my car be fixed at the roadside?',
        'answer' => '<p>Sometimes. A flat tyre with a usable spare, for example, can often be dealt with on the spot. Many faults cannot be fixed safely at the roadside. Describe the problem when you call and we will tell you honestly whether roadside help or recovery to a garage is the better option.</p>',
    ],
    [
        'category' => 'Services', 'show_on_home' => 0, 'sort_order' => 3, 'service' => 'flat-tyre-assistance',
        'question' => 'What if I don\'t have a spare tyre?',
        'answer' => '<p>Many newer cars come with a tyre repair kit instead of a spare, and repair kits cannot fix sidewall damage or a blowout. If there is no usable spare, the safest option is to recover the car to a tyre shop or garage. Tell us when you call and we will quote for that.</p>',
    ],
    [
        'category' => 'Services', 'show_on_home' => 0, 'sort_order' => 4, 'service' => 'breakdown-recovery',
        'question' => 'What should I do if I break down on Sheikh Zayed Road?',
        'answer' => '<p>Get onto the hard shoulder if you can, switch on your hazard lights and get everyone out on the side away from the traffic, behind the barrier if there is one. If the car is stuck in a live lane or anyone is at risk, call 999 first. Then call or WhatsApp us with your location pin and your direction of travel.</p>',
    ],
    [
        'category' => 'Services', 'show_on_home' => 0, 'sort_order' => 5, 'service' => 'car-recovery',
        'question' => 'Why do you ask about my car before you come?',
        'answer' => '<p>Different vehicles need to be moved in different ways. Automatics, all-wheel-drive cars, 4x4s and low sports cars usually need to be carried with no wheels turning on the road. Knowing the make and model, and whether the car can roll and steer, lets us plan how to move it safely.</p>',
    ],
];
