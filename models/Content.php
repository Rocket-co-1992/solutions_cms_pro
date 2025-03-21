<?php

namespace Pandao\Models;

use Pandao\Common\Utils\UrlUtils;
use Pandao\Services\CommentService;

class Content
{
    protected $pms_db;
    protected $commentService;
    protected $siteContext;

    public $id;
    public $comment;
    public $itemComments = [];
    public $num_comments = 0;
    public $images = [];

    /**
     * Constructor to initialize the database and website properties.
     *
     * @param object $db The database connection object.
     * @param object|null $siteContext The website object.
     */
    public function __construct($db, $siteContext)
    {
        $this->pms_db = $db;
        $this->siteContext = $siteContext;
    }

    /**
     * Get the path of the main image for the content.
     *
     * @param string $size The size of the image (e.g., 'big').
     * @param bool $full_url Whether to return the full URL or just the path.
     * @param int $min_w The minimum width required for the image.
     * 
     * @return string The path or URL to the main image, or an empty string if the image width is less than $min_w.
     */
    public function getMainImagePath($size = 'big', $full_url = false, $min_w = 0)
    {
        $imgpath = '';
        $images = $this->images ?? [];

        if (!empty($images)) {
            $row = $images[0];
            $type = $this instanceof Article ? 'article' : 'page';
            $path = 'medias/' . $type . '/' . $size . '/' . $row['id'] . '/' . $row['file'];
            $webp_path = preg_replace('/\.\w+$/', '.webp', $path);
            $full_path = SYSBASE . 'public/' . (file_exists(SYSBASE . 'public/' . $webp_path) ? $webp_path : $path);

            if (file_exists($full_path)) {
                $s = getimagesize($full_path);
                if ($s[0] < $min_w) {
                    return null;
                }
            } else {
                return null;
            }

            $imgpath = DOCBASE . (file_exists(SYSBASE . 'public/' . $webp_path) ? $webp_path : $path);

            if ($full_url) {
                $imgpath = UrlUtils::getUrl(true) . $imgpath;
            }
        }
        return $imgpath;
    }

    /**
     * Get the details of the main image including path, label, width, and height.
     *
     * @param string $size The size of the image (e.g., 'big').
     * @param bool $full_url Whether to return the full URL or just the path.
     * 
     * @return array An associative array containing image details such as path, label, width, and height.
     */
    public function getMainImage($size = 'big', $full_url = false)
    {
        $mainImg = ['path' => '', 'label' => '', 'w' => 0, 'h' => 0];
        $images = $this->images ?? [];

        if (!empty($images)) {
            $imgRow = $images[0];
            $type = $this instanceof Article ? 'article' : 'page';
            $path = 'medias/' . $type . '/' . $size . '/' . $imgRow['id'] . '/' . $imgRow['file'];
            $abspath = SYSBASE . 'public/' . $path;
            if (file_exists($abspath)) {
                $s = getimagesize($abspath);
                $mainImg['w'] = $s[0];
                $mainImg['h'] = $s[1];
                $mainImg['label'] = $imgRow['label'];
                $webp_path = preg_replace('/\.\w+$/', '.webp', $path);
                $mainImg['path'] = file_exists(SYSBASE . 'public/' . $webp_path) ? DOCBASE . $webp_path : DOCBASE . $path;
                
                if ($full_url) {
                    $mainImg['path'] = UrlUtils::getUrl(true) . $mainImg['path'];
                }
            }
        }
        return $mainImg;
    }

    /**
     * Load comments for the current content (e.g., page or article).
     */
    public function loadComments()
    {
        $this->commentService = new CommentService($this->pms_db);
        $itemType = ($this instanceof Article) ? 'article' : 'page';
        $this->itemComments = $this->commentService->getComments($itemType, $this->id);
        $this->num_comments = count($this->itemComments);
    }
}
