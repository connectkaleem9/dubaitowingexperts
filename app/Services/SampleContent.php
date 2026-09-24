<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Request;

/**
 * Stand-in content for checking a design before real content exists.
 *
 * Nothing here is ever stored or shown to the public. It is rendered only when a signed-in admin
 * asks for it with ?preview=sample, and those responses are noindex. Reviews and projects on the
 * live site must be real (CLAUDE.md §11): inventing customer reviews would mislead visitors,
 * breaches Google's review policies and puts the Business Profile at risk.
 */
final class SampleContent
{
    public const FLAG = 'sample';

    /** True when a signed-in admin asked to preview the page filled with sample content. */
    public static function wanted(Request $request): bool
    {
        return $request->query('preview') === self::FLAG && Auth::user() !== null;
    }

    /**
     * Sample reviews shaped exactly like rows from the reviews table, so every template that
     * renders a real review renders these unchanged.
     *
     * @return list<array<string, mixed>>
     */
    public static function reviews(): array
    {
        $bodies = [
            ['Ahmed A.', 5, 'Car Recovery', 'Dubai Marina', "Called them when my car wouldn't start in the basement car park. They talked me through where to wait, arrived with a flatbed and had it loaded without a scratch. The price I was quoted on the phone is the price I paid."],
            ['Priya K.', 5, 'Breakdown Recovery', 'Business Bay', 'Broke down on Sheikh Zayed Road at night and I was genuinely worried. They stayed on WhatsApp with me until the truck arrived and took the car straight to my garage. Very reassuring.'],
            ['James T.', 5, 'Towing Service', 'Downtown Dubai', 'Needed my car moved from the tower car park to a dealer in Al Quoz. Straightforward booking, sensible price agreed up front, no surprises at the end. Would use again.'],
            ['Fatima S.', 4, 'Flat Tyre Assistance', 'Deira', 'Flat tyre on the way to work. They came out, fitted the spare and checked the others before I drove off. Took a little longer than I hoped but the care was worth it.'],
            ['Mohammed R.', 5, 'Accident Recovery', 'Bur Dubai', 'After a small accident they handled everything calmly, loaded the car properly and delivered it to the body shop I chose. Explained each step, which I appreciated.'],
            ['Elena V.', 5, 'Roadside Assistance', 'Dubai Marina', 'Locked out with the engine running. They arrived quickly, sorted it without damaging anything, and were polite the whole time. Excellent value.'],
            ['Rashid H.', 5, 'Car Recovery', 'Al Barsha', 'Low sports car, so I was nervous about how it would be loaded. They used the right ramps and straps and it came off the truck exactly as it went on.'],
            ['Sana M.', 4, 'Breakdown Recovery', 'Jumeirah', 'Good communication from the first message to delivery. Fair pricing and they kept me updated on WhatsApp the whole way.'],
        ];

        $rows = [];
        foreach ($bodies as $i => [$name, $rating, $service, $area, $body]) {
            $date = date('Y-m-d H:i:s', strtotime('-' . ($i * 9 + 4) . ' days'));
            $rows[] = [
                'id' => -($i + 1),        // negative, so it can never collide with a real row
                'name' => $name,
                'rating' => $rating,
                'body' => $body,
                'service_name' => $service,
                'service_slug' => null,
                'area_text' => $area,
                'status' => 'approved',
                'is_featured' => 0,
                'approved_at' => $date,
                'created_at' => $date,
            ];
        }
        return $rows;
    }

    /** @return array{count: int, average: float} */
    public static function reviewStats(): array
    {
        $ratings = array_column(self::reviews(), 'rating');
        return ['count' => count($ratings), 'average' => array_sum($ratings) / max(1, count($ratings))];
    }
}
