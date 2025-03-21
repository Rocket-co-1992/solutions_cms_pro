<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\FormModel;
use Pandao\Admin\Models\MediaModel;
use Pandao\Common\Services\Csrf;
use Pandao\Common\Utils\DbUtils;
use Pandao\Common\Utils\FileUtils;

class FormController extends ModuleController
{
    protected $formModel;

    public function __construct($db, $modulePath = null)
    {
        parent::__construct($db, $modulePath);
        $this->formModel = new FormModel($db, $this->module, $this->moduleModel);
        $_SESSION['redirect'] = false;
    }

    /**
     * Display the form to add or edit an item.
     *
     * @param string $action The action to perform ('add' or 'edit').
     * @param bool $autorender Whether to auto-render the form. Defaults to true.
     *
     */
    public function form($action, $display = '', $autorender = true)
    {
        // Get item ID
        if (isset($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id'];
        elseif (isset($_POST['id']) && is_numeric($_POST['id'])) $id = $_POST['id'];
        else {
            header('Location: module='. MODULE .'&view=list');
            exit;
        }
        
        $id_file = (isset($_GET['file']) && is_numeric($_GET['file'])) ? $_GET['file'] : 0;
        $id_row = (isset($_GET['row']) && is_numeric($_GET['row'])) ? $_GET['row'] : 0;

        // Action to perform
        $action = (isset($_GET['action'])) ? htmlentities($_GET['action'], ENT_QUOTES, 'UTF-8') : '';
        if (isset($_POST['edit']) || isset($_POST['edit_back'])) {
            $action = 'edit';
        }
        if (isset($_POST['add']) || isset($_POST['add_back'])) {
            $action = 'add';
            $id = 0;
        }
        if ($action != '' && defined('PMS_DEMO') && PMS_DEMO == 1) {
            $action = '';
            $_SESSION['msg_error'][] = 'This action is disabled in the demo mode';
        }
        $this->action = $action;

        if($this->pms_db !== false ){

            $this->formModel->setDataFromDB($id);

            // Insersion / update
            if (($this->adminContext->addAllowed || $this->adminContext->editAllowed)
            && ($action == 'add' || $action == 'edit')
            && (Csrf::verifyToken('post')))
                $this->saveItem($action, $autorender);

            // Handle other actions
            if(!empty($action) && $action != 'edit' && $action != 'add') $this->handleAction($action, $id, $id_row, $id_file);
        }

        // Notice messages
        if (NB_FILES > 0) $_SESSION['msg_notice'][] = $this->adminContext->texts['EXPECTED_IMAGES_SIZE'] . ' ' . MAX_W_BIG . ' x ' . MAX_H_BIG . 'px';

        // Creation of the unique token for uploadifive
        if (!isset($_SESSION['uniqid'])) $_SESSION['uniqid'] = uniqid();
        if (!isset($_SESSION['timestamp'])) $_SESSION['timestamp'] = time();
        if (!isset($_SESSION['token'])) $_SESSION['token'] = hash('sha256', 'sessid_'.$_SESSION['uniqid'].$_SESSION['timestamp']);

        // Uploaded medias
        $default_lang = (MULTILINGUAL) ? $this->moduleModel->languages[0]['id'] ?? 0 : 0;

        $upload_allowed = $this->prepareMediasFolders();
        $this->formModel->setTmpFiles();
        $this->formModel->setFilesDataFromDb($id);
        $this->formModel->setMediasCounter();

        // Users management ?
        $is_users_field = (isset($_SESSION['user']['type']) 
            && $_SESSION['user']['type'] == 'administrator' 
            && $this->pms_db !== false && DbUtils::dbColumnExists($this->pms_db, 'solutionsCMS_' . MODULE, 'users'));

        $this->viewData = array_merge($this->viewData, [
            'model' => $this->formModel,
            'languages' => $this->moduleModel->languages ?? [],
            'fields' => $this->formModel->fields,
            'filesData' => $this->formModel->filesData,
            'upload_allowed' => $upload_allowed,
            'tmpFiles' => $this->formModel->tmpFiles,
            'mediasCounter' => $this->formModel->mediasCounter,
            'csrf_token' => Csrf::generateToken(),
            'is_users_field' => $is_users_field,
            'usersIn' => $this->formModel->usersIn,
            'usersNotIn' => $this->formModel->usersNotIn,
            'directory' => $this->module->dir,
            'id' => $id,
            'display' => $display,
            'show_langs' => PMS_LANG_ENABLED && DbUtils::dbColumnExists($this->pms_db, $this->formModel->tableName, 'show_langs'),
            'hide_langs' => PMS_LANG_ENABLED && DbUtils::dbColumnExists($this->pms_db, $this->formModel->tableName, 'hide_langs'),
            'db_access' => ($this->pms_db !== false)
        ]);

        // Form render
        if($autorender) $this->render('form', 'module', $this->module->name);
    }

    /**
     * Prepare the media folders for the module, create them if they do not exist, and grant write permissions.
     *
     */
    private function prepareMediasFolders()
    {
        $upload_allowed = false;
        if(NB_FILES > 0){
            $upload_allowed = true;
            $msg_notice = '';
            $media_path = SYSBASE . 'public/medias/' . MODULE . '/';
            
            if(is_writable(SYSBASE . 'public/medias/')){
            
                if(!is_dir($media_path)){
                    mkdir($media_path, 0777);
                    chmod($media_path, 0777);
                }
                if(!is_writable($media_path) && !$_SESSION['redirect'])
                    $msg_notice  .= str_replace('../', '', $media_path) . ' ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                
                if(!is_dir($media_path . 'tmp/')){
                    mkdir($media_path . 'tmp/', 0777);
                    chmod($media_path . 'tmp/', 0777);
                }
                if(!is_writable($media_path . 'tmp/') && !$_SESSION['redirect'])
                    $msg_notice .= str_replace('../', '', $media_path) . 'tmp/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                
                if(RESIZING == 0 || RESIZING == 1){
                    if(!is_dir($media_path . 'big/')){
                        mkdir($media_path . 'big/', 0777);
                        chmod($media_path . 'big/', 0777);
                    }
                    if(!is_writable($media_path . 'big/') && !$_SESSION['redirect'])
                        $msg_notice .= str_replace('../', '', $media_path) . 'big/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                }
                if(RESIZING == 1){
                    if(!is_dir($media_path . 'medium/')){
                        mkdir($media_path . 'medium/', 0777);
                        chmod($media_path . 'medium/', 0777);
                    }
                    if(!is_writable($media_path . 'medium/') && !$_SESSION['redirect'])
                        $msg_notice .= str_replace('../', '', $media_path) . 'medium/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                    
                    if(!is_dir($media_path . 'small/')){
                        mkdir($media_path . 'small/', 0777);
                        chmod($media_path . 'small/', 0777);
                    }
                    if(!is_writable($media_path . 'small/') && !$_SESSION['redirect'])
                        $msg_notice .= str_replace('../', '', $media_path) . 'small/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                }
                
                if(!is_dir($media_path . 'other/')){
                    mkdir($media_path . 'other/', 0777);
                    chmod($media_path . 'other/', 0777);
                }
                if(!is_writable($media_path . 'other/') && !$_SESSION['redirect'])
                    $msg_notice .= str_replace('../', '', $media_path) . 'other/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                    
            }elseif(!$_SESSION['redirect'])
                $msg_notice .= '/medias/ ' . $this->adminContext->texts['NO_WRITING'] . '<br>';
                
            if($msg_notice != ''){
                $upload_allowed = false;
                $_SESSION['msg_notice'][] = trim($msg_notice, '<br>');
            }
        }
        return $upload_allowed;
    }

    /**
     * Main method to handle actions.
     *
     * @param string $action The action to perform.
     * @param int $id The ID of the item.
     * @param int|null $id_row Optional. The ID of the row, if applicable.
     * @param int|null $id_file Optional. The ID of the file, if applicable.
     *
     */
    public function handleAction($action, $id, $id_row = null, $id_file = null)
    {
        if ($action != '' && defined('PMS_DEMO') && PMS_DEMO == 1) {
            $action = '';
            $_SESSION['msg_error'][] = 'This action is disabled in the demo mode';
        }
        
        if (!Csrf::verifyToken(strtolower($_SERVER['REQUEST_METHOD']))) {
            die('Invalid Csrf token');
        }

        switch ($action) {
            case 'delete_row':
                $this->deleteRow($id, $id_row, $_GET['table'] ?? null, $_GET['fieldref'] ?? null);
                break;

            case 'delete_file':
                $this->deleteFile($id_file);
                break;

            case 'delete_multi_file':
                $this->deleteMultiFiles($_POST['multiple_file'] ?? null);
                break;

            case 'check_file':
            case 'uncheck_file':
                $this->updateStatus($this->formModel->tableName . '_file', $id, $action === 'check_file' ? 1 : 2);
                break;

            case 'check_file_multi':
            case 'uncheck_file_multi':
                $this->updateMultipleStatus($this->formModel->tableName . '_file', $_POST['multiple_file'], $action === 'check_file_multi' ? 1 : 2);
                break;

            case 'display_home_file':
            case 'remove_home_file':
                $this->toggleHome($this->formModel->tableName . '_file', $id, $action === 'display_home_file' ? 1 : 0);
                break;

            case 'display_home_file_multi':
            case 'remove_home_file_multi':
                $this->toggleMultipleHome($this->formModel->tableName . '_file', $_POST['multiple_file'], $action === 'display_home_multi' ? 1 : 0);
                break;
                
            case 'download':
                $this->downloadFile($id_file);
                break;
        }
        
        $this->refresh();
    }

    /**
     * Download the specified file by its ID and send the appropriate headers.
     *
     * @param int $id_file The ID of the file to download.
     *
     */
    private function downloadFile($id_file)
    {
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
            if ($id_file > 0) {
                if ($type == 'image' || $type == 'other') {
                    $mediaModel = new MediaModel($this->pms_db);
                    $filepath = $mediaModel->getFilePath($id_file, $type);
                    
                    if ($filepath) {
                        $mime = FileUtils::getFileMimeType($filepath);
                        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') == false) {
                            header('Content-disposition: attachment; filename=' . basename($filepath));
                            header('Content-Type: ' . $mime);
                            header('Content-Transfer-Encoding: ' . $mime . "\n");
                            header('Content-Length: ' . filesize($filepath));
                            header('Pragma: no-cache');
                            header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public');
                            header('Expires: 0');
                        }
                        readfile($filepath);
                    }
                }
            }
        }
    }

    // Méthode pour supprimer une ligne
    protected function deleteRow($id, $id_row, $table, $fieldref)
    {
        if ($this->adminContext->deleteAllowed && $id_row > 0 && isset($table) && isset($fieldref)) {
            if ($this->formModel->deleteRow($id, $id_row, $table, $fieldref)) {
                $_SESSION['msg_success'][] = $this->adminContext->texts['DELETE_SUCCESS'];
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['DELETE_ERROR'];
            }
        }
    }
    
    private function saveItem($action, $autorender)
    {
        $id_item = $this->formModel->saveItem($action);

        $back = false;
        if (isset($_POST['add_back'])) $back = true;
        if (isset($_POST['edit_back'])) $back = true;

        if($id_item !== false) {
        
            if ($back === true) {
                header('Location: module=' . MODULE . '&view=list');
                exit;
            } else {
                if($autorender) $this->refresh($id_item);
            }
        }
    }
}
