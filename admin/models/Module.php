<?php

namespace Pandao\Admin\Models;

class Module
{
    public $name;
    public $title;
    public $dir;
    public $multi;
    public $ranking;
    public $home;
    public $main;
    public $validation;
    public $dates;
    public $release;
    public $library;
    public $dashboard;
    public $max_medias;
    public $medias_multi;
    public $resizing;
    public $max_w_big;
    public $max_h_big;
    public $max_w_medium;
    public $max_h_medium;
    public $max_w_small;
    public $max_h_small;
    public $icon = 'puzzle-piece';
    public $permissions = [];
    public $count = 0;
    public $last_date;
    public $configDom;
    public $classname;
    public $editorType;

    /**
     * Module constructor. Initializes a Module object with the provided parameters.
     *
     * @param string $name The module name (table and directory name).
     * @param string $title The module title.
     * @param string $dir The module directory path.
     * @param bool $multi Whether the module supports multilingual content.
     * @param bool $ranking Whether the module supports item ranking.
     * @param bool $home Whether the module supports home page content.
     * @param bool $main Whether the module supports an item defined as main.
     * @param bool $validation Whether the module requires item validation.
     * @param bool $dates Whether the module uses date (add, last edit) fields.
     * @param bool $release Whether the module supports release dates (publish, unpublish).
     * @param bool $library Whether the module needs a library of medias.
     * @param bool $dashboard Whether the module is displayed on the dashboard.
     * @param int $max_medias Maximum number of media items allowed.
     * @param bool $medias_multi Whether multilingual media items are supported.
     * @param bool $resizing Type of resizing of the media items.
     * @param int $max_w_big Maximum width for large images.
     * @param int $max_h_big Maximum height for large images.
     * @param int $max_w_medium Maximum width for medium images.
     * @param int $max_h_medium Maximum height for medium images.
     * @param int $max_w_small Maximum width for small images.
     * @param int $max_h_small Maximum height for small images.
     * @param string $icon The module icon (Font Awesome class).
     * @param array $permissions The module permissions.
     * @param DOMDocument $dom The module configuration DOMDocument.
     *
     */
    public function __construct($name, $title, $dir, $multi, $ranking, $home, $main, $validation, $dates, $release, $library, $dashboard, 
                                $max_medias, $medias_multi, $resizing, $max_w_big, $max_h_big, $max_w_medium, $max_h_medium, $max_w_small, 
                                $max_h_small, $icon, $permissions, $dom, $editorType) {
        $this->name = $name;
        $this->title = $title;
        $this->dir = $dir;
        $this->multi = $multi;
        $this->ranking = $ranking;
        $this->home = $home;
        $this->main = $main;
        $this->validation = $validation;
        $this->dates = $dates;
        $this->release = $release;
        $this->library = $library;
        $this->dashboard = $dashboard;
        $this->max_medias = $max_medias;
        $this->medias_multi = $medias_multi;
        $this->resizing = $resizing;
        $this->max_w_big = $max_w_big;
        $this->max_h_big = $max_h_big;
        $this->max_w_medium = $max_w_medium;
        $this->max_h_medium = $max_h_medium;
        $this->max_w_small = $max_w_small;
        $this->max_h_small = $max_h_small;
        $this->icon = $icon;
        $this->permissions = $permissions;
        $this->configDom = $dom;
        $this->editorType = $editorType;
    }
}
