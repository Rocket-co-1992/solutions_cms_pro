<?php

namespace Pandao\Admin\Modules\Page\Controllers;

use Pandao\Common\Utils\DbUtils;
use Pandao\Admin\Controllers\FormController as CoreFormController;

class FormController extends CoreFormController
{
    /**
     * Display the form for adding or editing an item.
     * @param string $action The action to perform (add or edit).
     * @param bool $autorender Whether to automatically render the view.
     */
    public function form($action, $display = '', $autorender = true)
    {
        parent::form($action, $display, false);

        $this->viewData['isNav'] = true;

        // Add the corresponding menu item in solutionsCMS_menu
        if (isset($_POST['add_to_menu'])
            && $_POST['add_to_menu'] == 1
            && ($this->adminContext->addAllowed) 
            && empty($_SESSION['msg_error']) 
            && $this->formModel->itemId > 0) {

            $next_id = null;
            $id_parent_menu = null;
            $id_parent = 0;
            $result_parent = $this->pms_db->prepare("SELECT id FROM `solutionsCMS_menu` WHERE `item_type` = 'page' AND `id_item` = :id_parent");
            $result_parent->bindParam(':id_parent', $id_parent, \PDO::PARAM_INT);
            
            // Get page details and add them to the menu
            $result = $this->pms_db->query("SELECT * FROM `solutionsCMS_page` WHERE `id` = '".$this->formModel->itemId."'");
            foreach ($result as $row) {

                $id_parent = $row['id_parent'];
                if($result_parent->execute() !== false && $this->pms_db->last_row_count() > 0){
                    $row_parent = $result_parent->fetch();
                    $id_parent_menu = $row_parent['id'];
                }

                $data = array();
                $data['id'] = $next_id;
                $data['name'] = $row['name'];
                $data['lang'] = $row['lang'];
                $data['item_type'] = 'page';
                $data['id_item'] = $this->formModel->itemId;
                $data['id_parent'] = $id_parent_menu;
                $data['main'] = 1;
                $data['footer'] = 0;
                $data['checked'] = $this->formModel->checked;

                $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_menu', $data);
                if($result_insert->execute() !== false) {
                    if(empty($next_id)) $next_id = $this->pms_db->lastInsertId();
                }else
                    break;
            }
        }

        if($this->action == 'add' || $this->action == 'edit') {
        
            $id_item = $this->formModel->itemId;
            if($id_item > 0) $this->refresh($id_item);
        }
        
        // Check if the page is already in the navigation
        if($this->adminContext->addAllowed) {
            $result = $this->pms_db->query("SELECT id FROM `solutionsCMS_menu` WHERE `item_type` = 'page' AND main = 1 AND `id_item` = ".$this->formModel->itemId);
            if($result !== false && $this->pms_db->last_row_count() == 0) $this->viewData['isNav'] = false;
        }
        
        $this->render('form', 'module', $this->module->name);
    }
}