<?php

namespace Pandao\Core\Services;

class WidgetData
{
    private static $data = [];

    /**
     * Set the value for a specific key in the widget data.
     *
     * @param string $key The key to identify the data.
     * @param mixed $value The value to be stored.
     */
    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    /**
     * Get the value associated with the specified key.
     *
     * @param string $key The key used to retrieve the value.
     * 
     * @return mixed|null The value if found, null otherwise.
     */
    public static function get($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }
}
