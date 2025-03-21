<?php

namespace Pandao\Common\Utils;

/**
 * Class FileUtils
 * - imgResize
 * - getFileMimeType
 * - fileSizeConvert
 * - isRwx
 * - getNewSize
 */

class FileUtils
{
    /**
     * Resizes an image and optionally adds a watermark.
     *
     * @param string $source_file The path to the source image file.
     * @param string $dest_dir The destination directory.
     * @param int $max_w The maximum width for the resized image.
     * @param int $max_h The maximum height for the resized image.
     * @param string|null $stamp_file The path to the watermark image (optional).
     * @return string|bool The path to the resized image, or false on failure.
     */
    public static function imgResize($source_file, $dest_dir, $max_w, $max_h, $stamp_file = null)
    {
        $return = false;
        if (substr($dest_dir, 0, -1) != '/') {
            $dest_dir .= '/';
        }

        if (is_file($source_file) && is_dir($dest_dir)) {

            $pos = strrpos($source_file, '/');
            $filename = $pos !== false ? substr($source_file, $pos + 1) : $source_file;
            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME); // Nom sans extension pour WebP

            $im_size = getimagesize($source_file);
            $w = $im_size[0];
            $h = $im_size[1];
            $im_type = $im_size[2];

            if ($h < $max_h) {
                if ($w < $max_w) {
                    $new_w = $w;
                    $new_h = $h;
                } else {
                    $new_w = $max_w;
                    $new_h = round($max_w * $h / $w);
                }
            } else {
                $new_w = $max_w;
                $new_h = round($max_w * $h / $w);

                if ($new_h > $max_h) {
                    $new_h = $max_h;
                    $new_w = round($max_h * $w / $h);
                }
            }

            if (!is_null($stamp_file) && is_file($stamp_file)) {

                $margin_right = 10;
                $margin_bottom = 10;

                $stamp_size = getimagesize($stamp_file);
                $sw = $stamp_size[0];
                $sh = $stamp_size[1];
                $s_type = $stamp_size[2];

                $new_sw = round($sw * $new_w / $max_w);
                $new_sh = $new_sw * $sh / $sw;

                switch ($s_type) {
                    case IMAGETYPE_JPEG:
                        $tmp_stamp = imagecreatefromjpeg($stamp_file);
                        break;
                    case IMAGETYPE_PNG:
                        $tmp_stamp = imagecreatefrompng($stamp_file);
                        break;
                    case IMAGETYPE_GIF:
                        $tmp_stamp = imagecreatefromgif($stamp_file);
                        break;
                }

                $new_stamp = imagecreatetruecolor($new_sw, $new_sh);

                if ($s_type == IMAGETYPE_PNG) {
                    imagesavealpha($new_stamp, true);
                    $trans_colour = imagecolorallocatealpha($new_stamp, 0, 0, 0, 127);
                    imagefill($new_stamp, 0, 0, $trans_colour);

                    $im = imagecreatetruecolor($new_sw, $new_sh);
                    $bg = imagecolorallocate($im, 0, 0, 0);
                    imagecolortransparent($new_stamp, $bg);
                    imagedestroy($im);
                }

                imagecopyresampled($new_stamp, $tmp_stamp, 0, 0, 0, 0, $new_sw, $new_sh, $sw, $sh);
            }

            switch ($im_type) {
                case IMAGETYPE_JPEG:
                    $tmp_image = imagecreatefromjpeg($source_file);
                    break;
                case IMAGETYPE_PNG:
                    $tmp_image = imagecreatefrompng($source_file);
                    break;
                case IMAGETYPE_GIF:
                    $tmp_image = imagecreatefromgif($source_file);
                    break;
            }

            $new_image = imagecreatetruecolor($new_w, $new_h);

            if ($im_type == IMAGETYPE_PNG) {
                imagesavealpha($new_image, true);
                $trans_colour = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $trans_colour);

                $im = imagecreatetruecolor($new_w, $new_h);
                $bg = imagecolorallocate($im, 0, 0, 0);
                imagecolortransparent($new_image, $bg);
                imagedestroy($im);
            }

            if (imagecopyresampled($new_image, $tmp_image, 0, 0, 0, 0, $new_w, $new_h, $w, $h)) {
                if (isset($tmp_stamp)) {
                    imagecopy($new_image, $new_stamp, $new_w - $new_sw - $margin_right, $new_h - $new_sh - $margin_bottom, 0, 0, $new_sw, $new_sh);
                }

                // Sauvegarde de l'image dans son format original
                switch ($im_type) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($new_image, $dest_dir . $filename, 80);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($new_image, $dest_dir . $filename, 8);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($new_image, $dest_dir . $filename);
                        break;
                }

                // Génération et sauvegarde de la version WebP
                if (function_exists('imagewebp')) {
                    imagewebp($new_image, $dest_dir . $filename_no_ext . '.webp', 80);
                    chmod($dest_dir . $filename_no_ext . '.webp', 0664);
                } else {
                    error_log('WebP support is not available in this PHP installation.');
                }

                chmod($dest_dir . $filename, 0664);
                $return = $dest_dir . $filename;
            }

            if (isset($new_image)) imagedestroy($new_image);
            if (isset($tmp_image)) imagedestroy($tmp_image);
            if (isset($new_stamp)) imagedestroy($new_stamp);
            if (isset($tmp_stamp)) imagedestroy($tmp_stamp);
        }
        return $return;
    }

    /**
     * Gets the MIME type of a file.
     *
     * @param string $file The path to the file.
     * @return string The MIME type of the file.
     */
    public static function getFileMimeType($file)
    {
        $type = 'application/octet-stream';
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $type = mime_content_type($file);
        } else {
            $dim = @getimagesize($file);
            if (is_array($dim) && isset($dim['mime'])) {
                $type = $dim['mime'];
            }
        }

        if (in_array($type, ['application/octet-stream'])) {
            $secondOpinion = @exec('file -b --mime-type ' . escapeshellarg($file), $foo, $returnCode);
            if ($returnCode === 0 && $secondOpinion) {
                $type = $secondOpinion;
            }
        }
        return $type;
    }

    /**
     * Converts a file size in bytes to a human-readable format.
     *
     * @param float $bytes The size in bytes.
     * @return string The formatted size (e.g., "2.5 MB").
     */
    public static function fileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = [
            0 => ['unit' => 'To', 'value' => pow(1024, 4)],
            1 => ['unit' => 'Go', 'value' => pow(1024, 3)],
            2 => ['unit' => 'Mo', 'value' => pow(1024, 2)],
            3 => ['unit' => 'Ko', 'value' => 1024],
            4 => ['unit' => 'octets', 'value' => 1],
        ];
        $result = '';
        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem['value']) {
                $result = $bytes / $arItem['value'];
                $result = str_replace('.', ',', strval(round($result, 2))) . ' ' . $arItem['unit'];
                break;
            }
        }
        return $result;
    }

    /**
     * Checks if a file has read, write, and execute permissions (777).
     *
     * @param string $file The file path.
     * @return bool True if the file has 777 permissions, false otherwise.
     */
    public static function isRwx($file)
    {
        return (file_exists($file) && substr(sprintf('%o', fileperms($file)), -3) === '777');
    }

    /**
     * Returns new width and height values for an image, respecting max limits.
     *
     * @param int $w The current width.
     * @param int $h The current height.
     * @param int $max_w The maximum width.
     * @param int $max_h The maximum height.
     * @return array An array containing the new width and height.
     */
    public static function getNewSize($w, $h, $max_w, $max_h)
    {
        if ($h < $max_h) {
            if ($w < $max_w) {
                $new_w = $w;
                $new_h = $h;
            } else {
                $new_w = $max_w;
                $new_h = round($max_w * $h / $w);
            }
        } else {
            $new_w = $max_w;
            $new_h = round($max_w * $h / $w);
            if ($new_h > $max_h) {
                $new_h = $max_h;
                $new_w = round($max_h * $w / $h);
            }
        }
        return [$new_w, $new_h];
    }
}
