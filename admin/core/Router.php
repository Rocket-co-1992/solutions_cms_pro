<?php

namespace Pandao\Admin\Core;

use Pandao\Admin\Core\Helpers;
use Pandao\Common\Services\AuthHandler;
use Pandao\Common\Utils\UrlUtils;
use Pandao\Admin\Controllers\ListController;
use Pandao\Admin\Controllers\FormController;

class Router
{
    protected $pms_db;

    private $modulePath = __DIR__ . '/../../'. PMS_ADMIN_FOLDER .'/modules/';
    private $controllerPath = __DIR__ . '/../../'. PMS_ADMIN_FOLDER .'/controllers/';
    private $controllerPrefix = "\\Pandao\\Admin\\Controllers\\";
    private $modulePrefix = "\\Pandao\\Admin\\Modules\\";

    /**
     * Initialize the Router with the database connection.
     *
     * @param Database $db The database connection.
     */
    public function __construct($db)
    {
        $this->pms_db = $db;
    }

    /**
     * Route the request based on whether it's XHR or HTTP.
     */
    public function route()
    {
        if (UrlUtils::isXhr())
            $this->handleXhr();
        else
            $this->handleHttp();
    }

    /**
     * Handle XHR (AJAX) requests.
     */
    private function handleXhr()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = substr($url, strpos($url, '/'));
        $path = trim($path, DOCBASE);
        $urlParts = explode('/', $path);

        $isModuleRequest = ($urlParts[2] ?? null) === 'modules';
        $moduleName = $isModuleRequest ? ($urlParts[3] ?? null) : null;
        $ajaxAction = $urlParts[$isModuleRequest ? 4 : 2] ?? null;

        $actionScript = $isModuleRequest && $moduleName
            ? __DIR__ . "/../modules/{$moduleName}/handlers/{$ajaxAction}.php"
            : __DIR__ . "/../handlers/{$ajaxAction}.php";

        if (file_exists($actionScript)) {
            require_once $actionScript;
        } else {
            echo json_encode([
                'error' => $isModuleRequest
                    ? 'Unknown action or file not found in module'
                    : 'Unknown action or file not found'
            ]);
        }
    }

    /**
     * Handle HTTP requests.
     */
    public function handleHttp()
    {
        $url = $this->parseUrl();
        
        if (empty($url['module']) && empty($url['view']) && empty($url['action']) && $this->isMainEntry()) {
            if (AuthHandler::isAuthenticated())
                header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=dashboard');
            else
                header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=login');
            exit;
        }
        
        if($url['module'] === null){
            Helpers::err404("Page or module not found");
        }
        
        $moduleName = ucfirst($url['module'] ?? '');
        $view = $url['view'] ?? 'index';
        $action = $url['action'] ?? '';
        $display = $url['display'] ?? '';

        if($moduleName == 'Setup'){
            $this->controllerPath = __DIR__ . '/../../setup/controllers/';
            $this->controllerPrefix = "\\Pandao\\Setup\\Controllers\\";
        }

        if ($this->isSystemPage($moduleName)) {
            $this->loadSystemPage($moduleName, $view, $action, $display);
        } elseif ($this->moduleExists($moduleName)) {
            $this->loadModuleController($moduleName, $view, $action, $display);
        } else {
            Helpers::err404("Page or module not found");
        }
    }

    /**
     * Check if the request is for the main entry point.
     *
     * @return bool True if it is the main entry point.
     */
    private function isMainEntry()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        return ($requestUri === '/' || basename($scriptName) === 'index.php') && empty($_GET);
    }

    /**
     * Parse the URL to retrieve module, view, and action.
     *
     * @return array Parsed URL components.
     */
    private function parseUrl()
    {
        return [
            'module' => $_GET['module'] ?? null,
            'view' => $_GET['view'] ?? null,
            'action' => $_GET['action'] ?? null,
            'display' => $_GET['display'] ?? null
        ];
    }

    /**
     * Check if a system page exists.
     *
     * @param string $page The page name.
     * 
     * @return bool True if the system page exists.
     */
    private function isSystemPage($page)
    {
        return file_exists($this->controllerPath . $page . 'Controller.php');
    }

    /**
     * Load a system page controller and call the appropriate view method.
     *
     * @param string $moduleName The name of the system module.
     * @param string $view The view method.
     * @param string $action The action to pass to the view.
     */
    private function loadSystemPage($moduleName, $view, $action, $display)
    {
        $controllerClass = $this->controllerPrefix . "{$moduleName}Controller";
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass($this->pms_db);
            if (method_exists($controller, $view)) {
                $controller->$view($action, $display);
            } else {
                Helpers::err404("Action {$action} not found for the system page {$moduleName}");
            }
        } else {
            Helpers::err404("Page system {$moduleName} not found");
        }
    }

    /**
     * Check if a module exists in the modules directory.
     *
     * @param string $moduleName The module name.
     * 
     * @return bool True if the module exists.
     */
    private function moduleExists($moduleName)
    {
        return is_dir($this->modulePath . strtolower($moduleName));
    }

    /**
     * Load the controller for a module or fall back to the default controllers.
     *
     * @param string $moduleName The module name.
     * @param string $view The view method.
     * @param string $action The action to pass to the view.
     */
    private function loadModuleController($moduleName, $view, $action, $display)
    {
        $moduleControllerClass = $this->modulePrefix . ucfirst($moduleName) . "\\Controllers\\" . ucfirst($view) . "Controller";

        if (class_exists($moduleControllerClass)) {
            $controller = new $moduleControllerClass($this->pms_db, $this->modulePath);
            if (method_exists($controller, $view)) {
                $controller->$view($action, $display);
            } else {
                $this->loadDefaultController($view, $action, $moduleName);
            }
        } else {
            $this->loadDefaultController($view, $action, $moduleName);
        }
    }

    /**
     * Load the default ListController or FormController.
     *
     * @param string $view The view method (list or form).
     * @param string $action The action to pass to the controller.
     * @param string $moduleName The module name.
     */
    private function loadDefaultController($view, $action, $moduleName)
    {
        switch ($view) {
            case 'list':
                $moduleListControllerClass = $this->modulePrefix . ucfirst($moduleName) . "\\Controllers\\ListController";
                if (class_exists($moduleListControllerClass)) {
                    $controller = new $moduleListControllerClass($this->pms_db, $this->modulePath);
                } else {
                    $controller = new ListController($this->pms_db, $this->modulePath);
                }
                $controller->list($action);
                break;

            case 'form':
                $moduleFormControllerClass = $this->modulePrefix . ucfirst($moduleName) . "\\Controllers\\FormController";
                if (class_exists($moduleFormControllerClass)) {
                    $controller = new $moduleFormControllerClass($this->pms_db, $this->modulePath);
                } else {
                    $controller = new FormController($this->pms_db, $this->modulePath);
                }
                $controller->form($action);
                break;

            default:
                Helpers::err404("Action {$view} not found for the module {$moduleName}");
                break;
        }
    }
}
