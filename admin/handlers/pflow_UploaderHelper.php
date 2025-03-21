<?php

namespace PflowUploader;

class UploaderHelper
{
    public static function cleanAccent($str)
    {
        $unwanted_array = [
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 's', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ğ' => 'G', 'İ' => 'I', 'Ş' => 'S', 'ğ' => 'g', 'ı' => 'i', 'ş' => 's', 'ü' => 'u'
        ];
        return strtr($str, $unwanted_array);
    }

    public static function textFormat($str, $tolower = true, $sep = '-')
    {
        $str = self::cleanAccent($str);
        $str = preg_replace('/([^a-z0-9]+)/i', $sep, $str);
        $str = preg_replace('/' . $sep . '[' . $sep . ']+/', $sep, $str);
        $str = trim($str, $sep);
        if ($tolower) {
            $str = strtolower($str);
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return $str;
    }

    public static function imgResize($source_file, $dest_dir, $max_w, $max_h, $stamp_file = null, $convertToWebp = true)
    {
        $return = false;

        // Assurer que le répertoire de destination se termine par '/'
        if (substr($dest_dir, -1) != '/') {
            $dest_dir .= '/';
        }

        // Vérifier que le fichier source existe et que le répertoire de destination est valide
        if (is_file($source_file) && is_dir($dest_dir)) {

            // Extraire le nom du fichier sans extension
            $pos = strrpos($source_file, '/');
            $filename = $pos !== false ? substr($source_file, $pos + 1) : $source_file;
            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME); // Nom sans extension
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // Extension d'origine en minuscule

            // Obtenir les informations de l'image
            $im_size = getimagesize($source_file);
            if ($im_size === false) {
                return false; // Fichier non valide
            }
            $w = $im_size[0];
            $h = $im_size[1];
            $im_type = $im_size[2];

            // Calcul de la nouvelle taille tout en conservant le ratio
            if ($w > $max_w || $h > $max_h) {
                $ratio = min($max_w / $w, $max_h / $h);
                $new_w = (int)($w * $ratio);
                $new_h = (int)($h * $ratio);
            } else {
                $new_w = $w;
                $new_h = $h;
            }

            // Gestion du watermark si fourni
            if (!is_null($stamp_file) && is_file($stamp_file)) {
                $margin_right = 10;
                $margin_bottom = 10;

                $stamp_size = getimagesize($stamp_file);
                if ($stamp_size === false) {
                    // Si le watermark n'est pas une image valide, ignorer
                    $stamp_file = null;
                } else {
                    $sw = $stamp_size[0];
                    $sh = $stamp_size[1];
                    $s_type = $stamp_size[2];

                    // Calcul des nouvelles dimensions du watermark
                    $new_sw = round($sw * $new_w / $max_w);
                    $new_sh = (int)round($new_sw * $sh / $sw);

                    // Créer l'image du watermark en fonction de son type
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
                        default:
                            $tmp_stamp = null;
                            break;
                    }

                    if (isset($tmp_stamp)) {
                        $new_stamp = imagecreatetruecolor($new_sw, $new_sh);

                        // Gérer la transparence pour les PNG
                        if ($s_type == IMAGETYPE_PNG) {
                            imagesavealpha($new_stamp, true);
                            $trans_colour = imagecolorallocatealpha($new_stamp, 0, 0, 0, 127);
                            imagefill($new_stamp, 0, 0, $trans_colour);
                        }

                        // Redimensionner le watermark
                        imagecopyresampled($new_stamp, $tmp_stamp, 0, 0, 0, 0, $new_sw, $new_sh, $sw, $sh);
                        imagedestroy($tmp_stamp);
                    }
                }
            }

            // Créer l'image source en fonction de son type
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
                default:
                    $tmp_image = null;
                    break;
            }

            if (!isset($tmp_image)) {
                return false; // Type d'image non supporté
            }

            // Créer une nouvelle image avec les dimensions calculées
            $new_image = imagecreatetruecolor($new_w, $new_h);

            // Gérer la transparence pour les PNG
            if ($im_type == IMAGETYPE_PNG) {
                imagesavealpha($new_image, true);
                $trans_colour = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $trans_colour);
            }

            // Redimensionner l'image source dans la nouvelle image
            if (imagecopyresampled($new_image, $tmp_image, 0, 0, 0, 0, $new_w, $new_h, $w, $h)) {

                // Ajouter le watermark si disponible
                if (isset($new_stamp)) {
                    imagecopy($new_image, $new_stamp, $new_w - $new_sw - $margin_right, $new_h - $new_sh - $margin_bottom, 0, 0, $new_sw, $new_sh);
                    imagedestroy($new_stamp);
                }

                // Sauvegarde de l'image : soit en WebP, soit dans son format d'origine
                if ($convertToWebp && function_exists('imagewebp')) {
                    $filename = $filename_no_ext . '.webp';
                    $save_success = imagewebp($new_image, $dest_dir . $filename, 80);

                    // Si le fichier WebP a été généré avec succès, supprimer le fichier source
                    if ($save_success) {
                        unlink($source_file); // Supprime le fichier source
                    }

                } else {
                    // Sauvegarde dans son format original
                    $filename = $filename_no_ext . '.' . $ext;
                    switch ($im_type) {
                        case IMAGETYPE_JPEG:
                            $save_success = imagejpeg($new_image, $dest_dir . $filename, 80);
                            break;
                        case IMAGETYPE_PNG:
                            $save_success = imagepng($new_image, $dest_dir . $filename, 8);
                            break;
                        case IMAGETYPE_GIF:
                            $save_success = imagegif($new_image, $dest_dir . $filename);
                            break;
                        default:
                            $save_success = false;
                            break;
                    }
                }

                if ($save_success) {
                    chmod($dest_dir . $filename, 0664);
                    $return = $dest_dir . $filename;
                }
            }

            // Libérer la mémoire
            if (isset($new_image)) imagedestroy($new_image);
            if (isset($tmp_image)) imagedestroy($tmp_image);
        }

        return $return;
    }
}
