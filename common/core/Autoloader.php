<?php

namespace Pandao;

/**
 * Autoloader class for automatically including PHP files based on class names and namespaces.
 */
class Autoloader
{
    /**
     * Array of class prefixes mapped to base directories.
     * 
     * @var array
     */
    protected static $prefixes = [];

    /**
     * Registers the autoloader function with spl_autoload_register.
     * This method sets the namespace prefixes and their corresponding directory paths.
     *
     * @return void
     */
    public static function register(): void
    {
        self::$prefixes = [
            'Common\\Core\\' => __DIR__ . '/../../common/core/',
            'Common\\Utils\\' => __DIR__ . '/../../common/utils/',
            'Common\\Models\\' => __DIR__ . '/../../common/models/',
            'Common\\Services\\' => __DIR__ . '/../../common/services/',
            'Core\\' => __DIR__ . '/../../core/',
            'Core\\Services\\' => __DIR__ . '/../../core/services/',
            'Services\\' => __DIR__ . '/../../services/',
            'Controllers\\' => __DIR__ . '/../../controllers/',
            'Models\\' => __DIR__ . '/../../models/',
            'Models\\DTO\\' => __DIR__ . '/../../models/dto/',
            'Admin\\Core\\' => __DIR__ . '/../../' . PMS_ADMIN_FOLDER . '/core/',
            'Admin\\Controllers\\' => __DIR__ . '/../../' . PMS_ADMIN_FOLDER . '/controllers/',
            'Admin\\Models\\' => __DIR__ . '/../../' . PMS_ADMIN_FOLDER . '/models/',
            'Setup\\Controllers\\' => __DIR__ . '/../../setup/controllers/'
        ];

        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Autoload function that loads the appropriate PHP file based on the class name and namespace.
     * Removes the "Pandao" namespace from the class name and looks for the file in the corresponding directory.
     *
     * @param string $class The fully qualified class name.
     * @return void
     */
    protected static function autoload(string $class): void
    {
        if (strpos($class, __NAMESPACE__ . '\\') !== 0) {
            return;
        }

        $class = str_replace(__NAMESPACE__ . '\\', '', $class);

        foreach (self::$prefixes as $prefix => $base_dir) {
            if (strpos($class, $prefix) === 0) {
                $relative_class = substr($class, strlen($prefix));
                $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }

        self::loadAdminModuleClass($class);
    }

    /**
     * Loads admin module class files dynamically from the admin modules directory.
     * This method handles classes under "Admin\\Modules" and tries to resolve the file path based on
     * the module and its type (e.g., Controllers, Models, etc.), allowing modular loading.
     *
     * @param string $class The fully qualified class name.
     * @return void
     */
    protected static function loadAdminModuleClass(string $class): void
    {
        if (strpos($class, 'Admin\\Modules\\') === 0) {
            $parts = explode('\\', $class);
            $module = $parts[2] ?? null; // Get the module name
            $type = $parts[3] ?? null;   // Get the type (e.g., Controllers, Models)

            if ($module && $type) {
                $relative_class = substr($class, strlen("Admin\\Modules\\$module\\$type\\"));
                $path = strtolower($module) . '/' . strtolower($type) . '/' . str_replace('\\', '/', $relative_class) . '.php';
                $file = __DIR__ . '/../../' . PMS_ADMIN_FOLDER . '/modules/' . $path;

                // Check if the file exists
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
                
                // Try loading from a fallback (e.g., a "default" controller/model)
                self::loadFromFallback($module, $type, $relative_class);
            }
        }
    }

    /**
     * Attempts to load a class from a fallback directory in case it does not exist in the specific module.
     *
     * @param string $module The module name.
     * @param string $type The class type (e.g., Controllers, Models).
     * @param string $relative_class The relative class name to be loaded.
     * @return void
     */
    protected static function loadFromFallback(string $module, string $type, string $relative_class): void
    {
        // Define fallback directories (e.g., for shared controllers/models between modules)
        $fallbackPath = __DIR__ . '/../../' . PMS_ADMIN_FOLDER . '/modules/default/' . strtolower($type) . '/' . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($fallbackPath)) {
            require_once $fallbackPath;
        }
    }
}

Autoloader::register();
