<?php

namespace Pandao\Admin\Modules\MODULE_NAME\Controllers;

use Pandao\Admin\Controllers\ModuleController;

class ViewNameController extends ModuleController
{

    public function __construct($db, $modulePath = null)
    {
        parent::__construct($db, $modulePath);
    }

    /**
     * Your custom controller method
     *
     * @param string $action The action to perform.
     * @param string $display Useful to switch between views.
     *
     */
    public function details($action, $display = '')
    {
        $this->viewData = array_merge($this->viewData, [
            // Pass data here and use them in the view as variables
            'permissions' => $this->module->permissions
        ]);

        $_SESSION['module_referer'] = MODULE;

        // Render the view
        $this->render('viewname', 'module', $this->module->name);
    }
}
