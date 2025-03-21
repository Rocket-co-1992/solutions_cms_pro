<?php

namespace Pandao\Admin\Models;

use Pandao\Common\Utils\FileUtils;
use Pandao\Common\Utils\StrUtils;

class MediaModel
{
    protected $pms_db;
    protected $languages;
    
    /**
     * MediaModel constructor. Initializes a MediaModel object with the provided parameters.
     *
     * @param object $db Database connection object.
     * @param array $languages An array of language objects.
     *
     */
    public function __construct($db, $languages = [])
    {
        $this->pms_db = $db;
        $this->languages = $languages;
    }

    /**
     * Get the file path of a media file based on its ID and type.
     *
     * @param int $id_file The ID of the media file.
     * @param string $type The type of the media file (image or other).
     *
     * @return string|bool The file path if found, otherwise false.
     */
    public function getFilePath($id_file, $type)
    {
        $query_file = 'SELECT file FROM solutionsCMS_' . MODULE . '_file WHERE id = ' . $id_file;
        if (MULTILINGUAL) {
            $query_file .= ' AND lang = ' . PMS_DEFAULT_LANG;
        }
        $result_file = $this->pms_db->query($query_file);
        if ($result_file !== false && $this->pms_db->last_row_count() > 0) {
            $file = $result_file->fetchColumn(0);
            $filepath = null;

            if ($type == 'image') {
                if (is_file(SYSBASE . 'public/medias/' . MODULE . '/big/' . $id_file . '/' . $file))
                    $filepath = SYSBASE . 'public/medias/' . MODULE . '/big/' . $id_file . '/' . $file;
                elseif (is_file(SYSBASE . 'public/medias/' . MODULE . '/medium/' . $id_file . '/' . $file))
                    $filepath = SYSBASE . 'public/medias/' . MODULE . '/medium/' . $id_file . '/' . $file;
                elseif (is_file(SYSBASE . 'public/medias/' . MODULE . '/small/' . $id_file . '/' . $file))
                    $filepath = SYSBASE . 'public/medias/' . MODULE . '/small/' . $id_file . '/' . $file;
            } elseif ($type == 'other' && is_file(SYSBASE . 'public/medias/' . MODULE . '/other/' . $id_file . '/' . $file)) {
                $filepath = SYSBASE . 'public/medias/' . MODULE . '/other/' . $id_file . '/' . $file;
            }

            return $filepath;
        }

        return false;
    }

    /**
     * Get all media files from the database for a specific item.
     *
     * @param int $id_item The ID of the item associated with the files.
     *
     * @return array An array of media files.
     */
    public function getFilesFromDb($id_item)
    {
        $query_file = 'SELECT * FROM solutionsCMS_' . MODULE . '_file WHERE id_item = :id_item AND file != \'\' ORDER BY `rank`';
        $result_file = $this->pms_db->prepare($query_file);
        $result_file->execute([':id_item' => $id_item]);
        return $result_file->fetchAll();
    }

    /**
     * Get formatted media file data for a specific item.
     *
     * @param int $id_item The ID of the item associated with the files.
     *
     * @return array Formatted file data including paths, sizes, and previews.
     */
    public function getFilesData($id_item)
    {
        $fileData = $this->getFilesFromDb($id_item);
        $formattedFileData = [];

        foreach ($fileData as $file) {
            
            $filename = $file['file'];
            $id_file = $file['id'];
            $type = $file['type'];
            $ext = strtolower(ltrim(strrchr($file['file'], '.'), '.'));

            $paths = [];
            $filesize = '';

            if ($type == 'other') {
                $paths['file_path'] = 'medias/' . MODULE . '/other/' . $id_file . '/' . $filename;
                $weight = filesize(SYSBASE . 'public/' . $paths['file_path']);
                $preview_html = (isset($pms_allowable_file_exts[$ext])) ? '<i class="fa-regular fa-'.$pms_allowable_file_exts[$ext].'"></i>' : '';
            } elseif ($type == 'image') {
                $big_path = 'medias/' . MODULE . '/big/' . $id_file . '/' . $filename;
                $medium_path = 'medias/' . MODULE . '/medium/' . $id_file . '/' . $filename;
                $small_path = 'medias/' . MODULE . '/small/' . $id_file . '/' . $filename;

                // Define paths based on resizing options
                if (RESIZING == 0) {
                    $paths['preview_path'] = $big_path;
                } elseif (RESIZING == 1) {
                    $paths['preview_path'] = $medium_path;
                } else {
                    $paths['preview_path'] = $small_path;
                }

                $paths['zoom_path'] = is_file(SYSBASE . 'public/' . $big_path) ? $big_path : 
                                    (is_file(SYSBASE . 'public/' . $medium_path) ? $medium_path : 
                                    (is_file(SYSBASE . 'public/' . $small_path) ? $small_path : ''));

                if (!empty($paths['zoom_path'])) {
                    $weight = filesize(SYSBASE . 'public/' . $paths['zoom_path']);
                    $dim = @getimagesize(SYSBASE . 'public/' . $paths['zoom_path']);
                    $filesize = is_array($dim) ? $dim[0] . ' x ' . $dim[1] : '0 x 0';
                    $filesize .= ' | ';
                    $preview_html = '<img src="' . DOCBASE . $paths['preview_path'] . '" class="card-img-top">';
                }
            }

            $id_lang = (MULTILINGUAL) ? $file['lang'] : 0;
            $ext = strtolower(ltrim(strrchr($file['file'], '.'), '.'));

            $filesize .= FileUtils::fileSizeConvert($weight);

            $formattedFileData[$id_lang][] = [
                'id' => $file['id'],
                'filename' => StrUtils::strtrunc(substr($file['file'], 0, strrpos($file['file'], '.')), 24, false, '..', true).'.'.$ext,
                'label' => !empty($file['label']) ? htmlentities($file['label'], ENT_QUOTES, 'UTF-8') : '',
                'checked' => $file['checked'],
                'home' => $file['home'],
                'type' => $file['type'],
                'fieldname' => 'file_'.$file['id'] . '_' . $file['lang'],
                'paths' => $paths,
                'preview_html' => $preview_html,
                'filesize' => $filesize,
                'dimensions' => $filePaths['dimensions'] ?? null
            ];
        }
        return $formattedFileData;
    }
}
