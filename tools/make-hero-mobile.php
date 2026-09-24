<?php
/*
 * Builds a portrait version of the hero photo for phones.
 *
 * The source is 1920x800. A phone hero is roughly 390x700, so `cover` throws away most of the
 * width and leaves an unrecognisable close-up of the cab. This crops a region around the truck and
 * extends the sky above it, giving an image whose shape matches the phone hero: the whole truck
 * stays visible, with room for the headline over open sky.
 *
 * Re-run it whenever the hero photo changes:
 *   php tools/make-hero-mobile.php public/assets/img/hero-bg.webp public/assets/img/hero-bg-mobile.webp
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('CLI only.');
}

[$script, $src, $out] = $argv;

$im = imagecreatefromwebp($src);
$sh = imagesy($im);

// Region around the truck, full height of the source.
$cropX = 700;
$cropW = 840;

$targetW = $cropW;
$targetH = (int) round($cropW / 0.5616);   // phone hero shape (about 390x694)
// All of the extra height goes above the photo: the copy sits on the sky and the truck keeps the
// road it was photographed on. Extending the road instead smears the lane markings downwards.
$skyH = $targetH - $sh;

$canvas = imagecreatetruecolor($targetW, $targetH);

// The sky is extended from the single topmost row of pixels. A one-pixel source carries
// no detail to smear, so the extension meets the photo in exactly its own colour and the join is
// invisible — stretching a thicker band instead leaves visible streaks and a hard seam.
imagecopyresampled($canvas, $im, 0, 0, $cropX, 0, $targetW, $skyH, $cropW, 1);
// The photo itself, unscaled.
imagecopy($canvas, $im, 0, $skyH, $cropX, 0, $cropW, $sh);

imagewebp($canvas, $out, 82);
printf("%s -> %dx%d (%d KB): sky %d + photo %d\n", basename($out), $targetW, $targetH, (int) (filesize($out) / 1024), $skyH, $sh);
