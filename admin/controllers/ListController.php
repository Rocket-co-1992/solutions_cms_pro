<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\ListModel;
use Pandao\Common\Services\Csrf;

class ListController extends ModuleController
{
    protected $listModel;

    public function __construct($db, $modulePath = null)
    {
        parent::__construct($db, $modulePath);
        $this->listModel = new ListModel($db, $this->module, $this->moduleModel);
    }

    /**
     * List module items with columns, filters, and pagination.
     *
     * @param string $action The action to perform.
     *
     */
    public function list($action, $display = '', $render = true)
    {
        if(isset($_POST['complete_lang'])) $action = 'complete_lang';

        if ($action) {
            $this->handleAction($action);
        }

        $cols = $this->listModel->getCols();
        $filters = $this->listModel->getFilters();

        // Manage the sessions of the research : filters, searched text
        if (isset($_SESSION['module_referer']) && $_SESSION['module_referer'] !== MODULE) {
            unset($_SESSION['filters']);
            unset($_SESSION['q_search']);
        }

        // Handle the research
        if (isset($_POST['search'])) {
            foreach ($filters as &$filter) {
                $fieldName = $filter->name;
            
                if (isset($_POST[$fieldName . '_start']) && isset($_POST[$fieldName . '_end'])) {
                    $value = [
                                $_POST[$fieldName . '_start'] != '' ? strtotime($_POST[$fieldName . '_start']) : null,
                                $_POST[$fieldName . '_end'] != '' ? strtotime($_POST[$fieldName . '_end']) : null
                            ];
                } else {
                    $value = isset($_POST[$fieldName]) ? htmlentities($_POST[$fieldName], ENT_QUOTES, 'UTF-8') : '';
                }
                $filter->setValue($value);
            }
            unset($filter);
            $q_search = htmlentities($_POST['q_search'], ENT_QUOTES, 'UTF-8');
            $_SESSION['filters'] = serialize($filters);
            $_SESSION['q_search'] = $q_search;
            $offset = 0;
            $_SESSION['offset'] = $offset;
        } else {
            if (isset($_SESSION['filters'])) {
                $filters = unserialize($_SESSION['filters']);
            }
            $q_search = $_SESSION['q_search'] ?? '';
        }

        $this->listModel->setFilters($filters);
        
        // Manage the order and sorting based on user selection.
        if (isset($_GET['order'])) {
            $order = htmlentities($_GET['order'], ENT_QUOTES, 'UTF-8');
        } elseif (isset($_SESSION['order']) && $_SESSION['order'] != '' && isset($_SESSION['module_referer']) && $_SESSION['module_referer'] == $this->module->name) {
            $order = $_SESSION['order'];
        } else {
            $order = $this->listModel->getOrder();
        }

        $sort = (strtolower(substr($order, -5)) == ' desc') ? 'desc' : 'asc';
        $order = trim(str_ireplace($sort, '', $order));

        if (isset($_GET['sort'])) {
            $sort = htmlentities($_GET['sort'], ENT_QUOTES, 'UTF-8');
        } elseif (isset($_SESSION['sort']) && $_SESSION['sort'] != '' && isset($_SESSION['module_referer']) && $_SESSION['module_referer'] == $this->module->name) {
            $sort = $_SESSION['sort'];
        } else {
            $sort = 'asc';
        }

        $sort_class = ($sort == 'asc') ? 'up' : 'down';
        $_SESSION['order'] = $order;
        $_SESSION['sort'] = $sort;

        $rsort = ($sort == 'asc') ? 'desc' : 'asc';

        // Manage the pagination based on user selection.
        if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
            $offset = $_GET['offset'];
        } elseif (isset($_SESSION['offset']) && isset($_SESSION['module_referer']) && $_SESSION['module_referer'] == $this->module->name) {
            $offset = $_SESSION['offset'];
        } else {
            $offset = 0;
        }

        if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
            $limit = $_GET['limit'];
            $offset = 0;
        } elseif (isset($_SESSION['limit']) && isset($_SESSION['module_referer']) && $_SESSION['module_referer'] == $this->module->name) {
            $limit = $_SESSION['limit'];
        } else {
            $limit = 50;
        }

        $_SESSION['limit'] = $limit;
        $_SESSION['offset'] = $offset;

        // Getting items from the database
        $items = [];
        if($this->pms_db !== false) $items = $this->listModel->getItems($filters, $q_search, $order, $sort, $limit, $offset);

        foreach ($items as $i => $item) {
            $items[$i]['cols'] = $this->listModel->getColsValues($item, $i);
            if (NB_FILES > 0) $items[$i]['images'] = $this->listModel->getImagePaths($item['id']);
        }

        // Getting the total number of elements for the pagination
        $total = 0;
        if($this->pms_db !== false ) $total = $this->listModel->getTotalItems($filters, $q_search);
        $num_pages = ceil($total / $limit);

        $this->viewData = array_merge($this->viewData, [
            'languages' => $this->moduleModel->languages ?? [],
            'items' => $items,
            'cols' => $cols,
            'filters' => $filters,
            'q_search' => $q_search,
            'total' => $total,
            'limit' => $limit,
            'num_pages' => $num_pages,
            'offset' => $offset,
            'order' => $order,
            'sort' => $sort,
            'rsort' => $rsort,
            'sort_class' => $sort_class,
            'csrf_token' => Csrf::generateToken(),
            'filtersHtml' => $this->listModel->getFilterInputs(),
            'permissions' => $this->module->permissions,
            'display' => $display
        ]);

        $_SESSION['module_referer'] = MODULE;

        // Render the listing
        if($render) $this->render('list', 'module', $this->module->name);
    }

    /**
     * Handle actions such as add, edit, check, uncheck, delete, etc.
     *
     * @param string $action The action to perform.
     *
     */
    protected function handleAction($action)
    {
        if ($action != '' && defined('PMS_DEMO') && PMS_DEMO == 1) {
            $action = '';
            $_SESSION['msg_error'][] = 'This action is disabled in the demo mode';
        }

        $id = $_GET['id'] ?? 0;

        if (!Csrf::verifyToken(strtolower($_SERVER['REQUEST_METHOD']))) {
            die('Invalid Csrf token');
        }

        switch ($action) {
            case 'check':
            case 'uncheck':
                $this->updateStatus($this->listModel->tableName, $id, $action === 'check' ? 1 : 2);
                break;
        
            case 'check_multi':
            case 'uncheck_multi':
                $this->updateMultipleStatus($this->listModel->tableName, $action === 'check_multi' ? 1 : 2, $_POST['multiple_item'] ?? null);
                break;
        
            case 'archive':
                $this->updateStatus($this->listModel->tableName, $id, 3);
                break;
        
            case 'delete':
                $this->deleteItem($id);
                break;
        
            case 'delete_multi':
                $this->deleteMultipleItems($_POST['multiple_item'] ?? null);
                break;
        
            case 'display_home':
            case 'remove_home':
                $this->toggleHome($this->listModel->tableName, $id, $action === 'display_home' ? 1 : 0);
                break;
        
            case 'display_home_multi':
            case 'remove_home_multi':
                $this->toggleMultipleHome($this->listModel->tableName, $action === 'display_home_multi' ? 1 : 0, $_POST['multiple_item'] ?? null);
                break;
        
            case 'complete_lang':
                $this->completeLang($_POST['languages'] ?? null);
                break;
        
            case 'define_main':
                $this->defineMain($id);
                break;
        }

        $this->refresh();
    }

    /**
     * Delete an item by its ID.
     *
     * @param int $id The ID of the item to delete.
     *
     */
    protected function deleteItem($id)
    {
        if ($this->adminContext->deleteAllowed) {
            if ($this->listModel->deleteItem($id)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['DELETE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['DELETE_ERROR'];
            }
        }
    }

    /**
     * Set an item as the main one by its ID.
     *
     * @param int $id The ID of the item to set as main.
     *
     */
    protected function defineMain($id)
    {
        if ($this->adminContext->editAllowed) {
            if ($this->listModel->defineMain($id)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['UPDATE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['UPDATE_ERROR'];
            }
        }
    }

    /**
     * Delete multiple items by their IDs.
     *
     * @param array $multiple_items The array of item IDs to delete.
     *
     */
    protected function deleteMultipleItems($multiple_items)
    {
        if ($this->adminContext->deleteAllowed) {
            foreach ($multiple_items as $id) {
                $this->listModel->deleteItem($id);
            }
        }
    }

    /**
     * Complete the fields of a language in the database with corresponding values from the default language.
     *
     * @param array $lang_ids The array of language IDs to complete.
     *
     */
    protected function completeLang($lang_ids)
    {
        if ($this->adminContext->allAccess) {
            foreach($lang_ids as $id_lang){
                $this->moduleModel->completeLangModule('solutionsCMS_'.MODULE, $id_lang);
                if(NB_FILES > 0) $this->moduleModel->completeLangModule('solutionsCMS_'.MODULE.'_file', $id_lang);
            }
        }
    }
}
