<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\AdminContext;
use Pandao\Admin\Core\Helpers;

class Controller
{
    protected $pms_db;  
    protected $viewData = [];
    protected $modulePath;
    protected $adminContext;
    protected $assets;

    public function __construct($db, $modulePath = null)
    {
        $this->pms_db = $db;
        $this->assets = ['assets_css' => [], 'assets_js' => []];
        $this->adminContext = AdminContext::get();
        $this->modulePath = $modulePath;
    }
    
    /**
     * Load and render a system or module view with partials.
     *
     * @param string $view View file to load.
     * @param string $type 'system' or 'module'. Defaults to 'system'.
     * @param string|null $moduleName Module name if applicable. Defaults to null.
     * 
     */
    public function render($view, $type = 'system', $moduleName = null)
    {
        $this->viewData['action'] = '';
        $this->viewData['adminContext'] = AdminContext::get();
        
        extract($this->viewData);
        extract($this->assets);
        
        if ($type === 'system') {
            
            switch ($view) {
                case 'login' :
                case 'dashboard' : $title = $adminContext->texts['LOGIN'];
                break;
                case 'settings' : $title = $adminContext->texts['SETTINGS'];
                break;
                default : $title = '';
                break;
            }
            define("TITLE_ELEMENT", $title);
            
            $viewPath = ($view == 'setup') ? __DIR__ . '/../../setup/views/' . $view . '.php' : __DIR__ . '/../../'. PMS_ADMIN_FOLDER .'/views/' . $view . '.php';
            
        } elseif ($type === 'module' && $moduleName !== null) {
            $viewPath = $this->modulePath . $moduleName . '/views/' . $view . '.php';
            
            if (!is_file($viewPath))
                $viewPath = __DIR__ . '/../modules/default/views/' . $view . '.php';

            $headPaths = [__DIR__ . '/../modules/default/views/partials/head_' . $view . '.php'];

            $headModulePath = $this->modulePath . $moduleName . '/views/partials/head_' . $view . '.php';
            if (is_file($headModulePath))
                $headPaths[] = $headModulePath;

        } else {
            Helpers::err404('Invalid view or module');
            return;
        }
        
        if (is_file($viewPath)) {
            $partialPath = __DIR__ . '/../../'. PMS_ADMIN_FOLDER .'/views/partials/';
            require_once $partialPath . 'head.php';

            if($type == 'module' && !empty($headPaths)) {
                foreach ($headPaths as $path)
                    if(is_file($path)) require_once $path;
            }

            if($view != 'login' && $view != 'setup') require_once $partialPath . 'header.php';
            require_once $viewPath;
            if($view != 'login' && $view != 'setup') require_once $partialPath . 'footer.php';

        } else {
            Helpers::err404('View ' . $view . ' not found');
        }

        $_SESSION['redirect'] = false;
        $_SESSION['msg_error'] = array();
        $_SESSION['msg_success'] = array();
        $_SESSION['msg_notice'] = array();
    }
}
