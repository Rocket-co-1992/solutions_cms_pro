<?php

namespace Pandao\Admin\Core;

use Pandao\Admin\Models\AdminContext;
use Pandao\Common\Services\AuthHandler;
use Pandao\Common\Core\Bootstrap;

class AdminBootstrap extends Bootstrap
{
    /**
     * Initialize the admin bootstrap process.
     */
    public function init()
    {
        parent::init();
        $this->checkAuthentication();
        $installed = $this->checkSetup();
        AdminContext::get($this->getDb(), $this->langManager, $installed);
        $this->defineLocaleConstants();
    }

    /**
     * Check if the user is authenticated and handle redirection or error accordingly.
     */
    private function checkAuthentication()
    {
        if ($this->getCurrentModule() === 'login' || $this->getCurrentModule() === 'setup')
            return;

        /*if (AuthHandler::isAuthenticated() && !$this->getCurrentModule() && !Utils::isXhr()) {
            header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=dashboard');
            exit;
        }*/

        if (!AuthHandler::isAuthenticated()) {
            if ($this->isAjaxRequest()) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Authentication required'
                ]);
                exit;
            } else {
                header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=login');
                exit;
            }
        }
    }

    /**
     * Retrieve the current module from the URL parameters.
     *
     * @return string|null The current module name, or null if not set.
     */
    private function getCurrentModule()
    {
        return isset($_GET['module']) ? $_GET['module'] : null;
    }

    /**
     * Check if the current request is an AJAX request.
     *
     * @return bool True if the request is AJAX, false otherwise.
     */
    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
