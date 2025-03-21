<?php

namespace Pandao\Core\Services;

class AssetsManager
{
    private static $css = [];
    private static $js = [];

    /**
     * Add a CSS file path to the collection.
     *
     * @param string $path Path to the CSS file.
     */
    public static function addCss($path)
    {
        self::$css[] = $path;
    }

    /**
     * Add a JavaScript file path to the collection.
     *
     * @param string $path Path to the JavaScript file.
     */
    public static function addJs($path)
    {
        self::$js[] = $path;
    }

    /**
     * Get the collection of added CSS file paths.
     *
     * @return array List of CSS file paths.
     */
    public static function getCss()
    {
        return self::$css;
    }

    /**
     * Get the collection of added JavaScript file paths.
     *
     * @return array List of JavaScript file paths.
     */
    public static function getJs()
    {
        return self::$js;
    }
}
