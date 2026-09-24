<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use RuntimeException;

/**
 * Secure image upload pipeline:
 * size limit → upload check → finfo MIME allow-list → getimagesize → pixel limit →
 * re-encode with GD to WebP at several widths (strips EXIF/GPS and any embedded payload) →
 * random, descriptive filename → media row.
 */
final class Uploader
{
    private const MIME = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_PIXELS = 40_000_000;

    /** @param array<string, mixed> $file Single $_FILES entry. @return int media id */
    public static function image(array $file, string $alt, ?int $adminId, int $maxBytes = 0): int
    {
        $maxBytes = $maxBytes > 0 ? $maxBytes : (int) config('app.upload_max_bytes');

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::uploadError((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
        }
        $tmp = (string) $file['tmp_name'];
        if (!is_uploaded_file($tmp) && PHP_SAPI !== 'cli') {
            throw new RuntimeException('Invalid upload.');
        }
        $size = (int) filesize($tmp);
        if ($size <= 0 || $size > $maxBytes) {
            throw new RuntimeException('Image must be smaller than ' . round($maxBytes / 1048576) . ' MB.');
        }
        $mime = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!in_array($mime, self::MIME, true)) {
            throw new RuntimeException('Only JPG, PNG or WebP images are allowed.');
        }
        $info = @getimagesize($tmp);
        if ($info === false || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > self::MAX_PIXELS) {
            throw new RuntimeException('The file is not a valid image or is too large in dimensions.');
        }

        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png'  => @imagecreatefrompng($tmp),
            'image/webp' => @imagecreatefromwebp($tmp),
        };
        if ($src === false) {
            throw new RuntimeException('The image could not be processed.');
        }
        if ($mime === 'image/jpeg') {
            $src = self::applyExifOrientation($src, $tmp);
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $dir = date('Y/m');
        $base = slugify($alt !== '' ? $alt : pathinfo((string) $file['name'], PATHINFO_FILENAME), 60) ?: 'image';
        $name = $dir . '/' . $base . '-' . bin2hex(random_bytes(3));
        $absDir = BASE_PATH . '/public/uploads/' . $dir;
        if (!is_dir($absDir) && !mkdir($absDir, 0755, true) && !is_dir($absDir)) {
            throw new RuntimeException('Upload directory is not writable.');
        }

        $made = [];
        foreach ((array) config('app.image_widths') as $target) {
            $target = (int) $target;
            if ($target > $w && $made !== []) {
                continue;
            }
            $tw = min($target, $w);
            $th = (int) round($h * ($tw / $w));
            $dst = imagecreatetruecolor($tw, $th);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
            if (!imagewebp($dst, $absDir . '/' . basename($name) . '-' . $tw . '.webp', 80)) {
                throw new RuntimeException('Could not save the image.');
            }
            imagedestroy($dst);
            $made[] = $tw;
        }
        imagedestroy($src);
        $made = array_values(array_unique($made));

        return Media::create([
            'path' => $name,
            'original_name' => mb_substr((string) $file['name'], 0, 255),
            'mime' => 'image/webp',
            'width' => $w,
            'height' => $h,
            'widths' => implode(',', $made),
            'size_bytes' => $size,
            'alt_text' => mb_substr($alt, 0, 255),
            'uploaded_by' => $adminId,
        ]);
    }

    /** Remove all generated variant files for a media row. */
    public static function deleteFiles(array $media): void
    {
        foreach (explode(',', (string) $media['widths']) as $w) {
            $file = BASE_PATH . '/public/uploads/' . $media['path'] . '-' . (int) $w . '.webp';
            if (is_file($file) && str_starts_with((string) realpath($file), (string) realpath(BASE_PATH . '/public/uploads'))) {
                unlink($file);
            }
        }
    }

    private static function applyExifOrientation(\GdImage $img, string $path): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data($path);
        $o = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
        $rotated = match ($o) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => $img,
        };
        return $rotated === false ? $img : $rotated;
    }

    private static function uploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The image is too large.',
            UPLOAD_ERR_PARTIAL => 'The upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_FILE => 'Please choose an image.',
            default => 'The upload failed. Please try again.',
        };
    }
}
