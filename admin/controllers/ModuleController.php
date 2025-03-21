<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\ModuleModel;

class ModuleController extends Controller
{
    protected $module;
    protected $moduleModel;
    protected $action;

    public function __construct($db, $modulePath = null)
    {
        parent::__construct($db, $modulePath);
        $this->adminContext->setPermissions();
        $this->module = $this->adminContext->currModule;

        define('MODULE', $this->module->name);
        define('MULTILINGUAL', $this->module->multi);
        define('TITLE_ELEMENT', $this->module->title);
        define('RANKING', $this->module->ranking);
        define('HOME', $this->module->home);
        define('MAIN', $this->module->main);
        define('VALIDATION', $this->module->validation);
        define('DATES', $this->module->dates);
        define('RELEASE', $this->module->release);
        define('NB_FILES', $this->module->max_medias);
        define('FILE_MULTI', $this->module->medias_multi);
        define('RESIZING', $this->module->resizing);
        define('MAX_W_BIG', $this->module->max_w_big);
        define('MAX_H_BIG', $this->module->max_h_big);
        define('MAX_W_MEDIUM', $this->module->max_w_medium);
        define('MAX_H_MEDIUM', $this->module->max_h_medium);
        define('MAX_W_SMALL', $this->module->max_w_small);
        define('MAX_H_SMALL', $this->module->max_h_small);
        define('ICON', $this->module->icon);
        define('EDITOR_TYPE', $this->module->editorType);
        define('DIR', $this->module->dir . '/');

        if($this->pms_db !== false) $this->moduleModel = new ModuleModel($db, $this->module);
    }

    /**
     * Method to delete a file or item by its ID.
     *
     * @param int $id_file The ID of the file or item to delete.
     *
     */
    protected function deleteFile($id_file)
    {
        if ($this->adminContext->deleteAllowed && $id_file > 0) {
            if ($this->moduleModel->deleteFile($id_file)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['DELETE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['DELETE_ERROR'];
            }
        }
    }

    /**
     * Method to delete multiple files or items, iterating through the array and calling deleteFile for each.
     *
     * @param array $ids The array of file or item IDs to delete.
     *
     */
    protected function deleteMultiFiles($ids)
    {
        if ($this->adminContext->deleteAllowed && isset($ids)) {
            foreach ($ids as $id_file) {
                $this->deleteFile($id_file);  // Appel de la méthode simple
            }
        }
    }

    /**
     * Method to activate/deactivate/archive a file or item.
     *
     * @param string $table The table where the item is stored.
     * @param int $id The ID of the file or item.
     * @param int $status The new status to set (e.g., 0 for awaiting, 1 for active, 2 for inactive, 3 for archive).
     *
     */
    protected function updateStatus($table, $id, $status)
    {
        if ($this->adminContext->publishAllowed && $id > 0) {
            if ($this->moduleModel->updateStatus($table, $id, $status)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['UPDATE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['UPDATE_ERROR'];
            }
        }
    }

    /**
     * Method to activate/deactivate multiple files or items, iterating through the array and calling updateStatus for each.
     *
     * @param string $table The table where the items are stored.
     * @param int $status The new status to set (e.g., 0 for awaiting, 1 for active, 2 for inactive, 3 for archive).
     * @param array $ids The array of file or item IDs to update.
     *
     */
    protected function updateMultipleStatus($table, $status, $ids)
    {
        if ($this->adminContext->publishAllowed && isset($ids)) {
            foreach ($ids as $id) {
                $this->updateStatus($table, $id, $status);  // Appel de la méthode simple
            }
        }
    }

    /**
     * Method to display or remove a file or item from the homepage.
     *
     * @param string $table The table where the item is stored.
     * @param int $id The ID of the file or item.
     * @param int $display The display status (e.g., 1 for displaying on the homepage, 0 for removing).
     *
     */
    protected function toggleHome($table, $id, $display)
    {
        if ($this->adminContext->publishAllowed && $id > 0) {
            if ($this->moduleModel->displayHome($table, $id, $display)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['UPDATE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['UPDATE_ERROR'];
            }
        }
    }

    /**
     * Method to display or remove multiple files or items from the homepage, iterating through the array and calling toggleHome for each.
     *
     * @param string $table The table where the items are stored.
     * @param int $display The display status (e.g., 1 for displaying on the homepage, 0 for removing).
     * @param array $ids The array of file or item IDs to update.
     *
     */
    protected function toggleMultipleHome($table, $display, $ids)
    {
        if ($this->adminContext->publishAllowed && isset($ids)) {
            foreach ($ids as $id) {
                $this->toggleHome($table, $id, $display);  // Appel de la méthode simple
            }
        }
    }
    
    /**
     * Refresh the current page by redirecting to the specified view.
     * 
     * The method constructs a URL based on the current module and view, 
     * and optionally includes the item ID if available. It then redirects to this URL.
     * 
     * @param int $id_item Identifier of the item if modified.
     *
     */
    protected function refresh($id_item = 0)
    {
        $view = isset($_GET['view']) ? $_GET['view'] : 'list';
        $url = 'module=' . MODULE . '&view=' . $view;
        if($id_item > 0) $url .= '&id=' . $id_item;
        elseif(isset($_POST['id']) && is_numeric($_POST['id'])) $url .= '&id=' . $_POST['id'];
        elseif(isset($_GET['id']) && is_numeric($_GET['id']) && $view == 'form') $url .= '&id=' . $_GET['id'];
        
        header('Location: ' . $url);
        exit;
    }
}
