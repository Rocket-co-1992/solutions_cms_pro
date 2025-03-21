<?php

namespace Pandao\Admin\Models;

use Pandao\Common\Utils\DbUtils;
use Pandao\Common\Utils\DateUtils;
use Pandao\Common\Utils\StrUtils;
use Pandao\Common\Utils\FileUtils;
use Pandao\Admin\Core\Helpers;

class FormModel
{
    protected $pms_db;
    protected $module;
    protected $adminContext;
    protected $mediaModel;
    protected $moduleModel;

    public $tableName;
    public $itemId = 0;
    public $languages = [];
    public $fields = [];
    public $medias = [];
    public $uploadedFiles = [];
    public $tmpFiles = [];
    public $file = [];
    public $img = [];
    public $img_label = [];
    public $file_label = [];
    public $fields_checked = true;
    public $total_lang = 1;
    public $rank = 0;
    public $old_rank = 0;
    public $home = 0;
    public $checked = 0;
    public $add_date;
    public $edit_date;
    public $publish_date;
    public $unpublish_date;
    public $users = [];
    public $filesData = [];
    public $usersIn = [];
    public $usersNotIn = [];
    public $mediasCounter = [];

    /**
     * FormModel constructor. Initializes a FormModel object with the provided parameters.
     *
     * @param object $db Database connection object.
     * @param object $module The module object.
     * @param object $moduleModel The module model object.
     *
     */
    public function __construct($db, $module, $moduleModel)
    {
        $this->pms_db = $db;
        $this->module = $module;
        $this->tableName = 'solutionsCMS_' . $module->name;
        $this->adminContext = AdminContext::get();
        $this->edit_date = time();
        $this->publish_date = time();
        $this->users = array($_SESSION['user']['id']);
        $this->languages = $moduleModel->languages ?? [];
        $this->mediaModel = new MediaModel($db, $this->languages);
        $this->moduleModel = $moduleModel;
        $this->total_lang = count($this->languages);
    }

    /**
     * Set data from the database for the item with the given ID.
     *
     * @param int $id The ID of the item.
     *
     */
    public function setDataFromDB($id)
    {
        $this->itemId = $id;

        // Last rank selection
        if (RANKING && $this->pms_db != false) {
            $result_rank = $this->pms_db->query('SELECT `rank` FROM ' . $this->tableName . ' ORDER BY `rank` DESC LIMIT 1');
            $this->rank = ($result_rank !== false && $this->pms_db->last_row_count() > 0) ? $result_rank->fetchColumn(0) + 1 : 1;
        }

        $fields = $this->getFields();
        if (is_null($fields)) $fields = [];
        $this->fields = $fields;

        // Getting datas in the database
        $result = $this->pms_db->query('SELECT * FROM ' . $this->tableName . ' WHERE id = '.$this->itemId);
        if ($result !== false) {
            
            // Datas of the module

            foreach ($result as $row) {

                $id_lang = (MULTILINGUAL) ? $row['lang'] : 0;

                foreach ($fields[MODULE]['fields'] as $fieldName => $field) {
                    if ($field->type != 'separator') {
                        $field->setValue($row[$fieldName], 0, $id_lang);
                    }
                }

                if ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0) {

                    $this->home = HOME ? $row['home'] : NULL;
                    $this->checked = VALIDATION ? $row['checked'] : NULL;
                    $this->old_rank = RANKING ? $row['rank'] : NULL;
                    $this->add_date = DATES ? $row['add_date'] : NULL;
                    if (RELEASE) {
                        $this->publish_date = $row['publish_date'];
                        $this->unpublish_date = $row['unpublish_date'];
                    } else {
                        $this->publish_date = NULL;
                        $this->unpublish_date = NULL;
                    }

                    $this->show_langs = DbUtils::dbColumnExists($this->pms_db, $this->tableName, 'show_langs') ? explode(',', $row['show_langs']) : NULL;
                    $this->hide_langs = DbUtils::dbColumnExists($this->pms_db, $this->tableName, 'hide_langs') ? explode(',', $row['hide_langs']) : NULL;

                    if (DbUtils::dbColumnExists($this->pms_db, 'solutionsCMS_' . MODULE, 'users')) {
                        $users = explode(',', $row['users']);
                        if (!in_array($_SESSION['user']['type'], array('administrator', 'manager', 'editor')) && !in_array($_SESSION['user']['id'], $users)) {
                            header('Location: view=list');
                            exit;
                        }else{
                            $result_user = $this->pms_db->query('SELECT * FROM solutionsCMS_user ORDER BY login');
                            if ($result_user !== false) {
                                foreach ($result_user as $row_user){
                                    if (in_array($row_user['id'], $users)) 
                                        $this->usersIn[] = $row_user;
                                    else
                                        $this->usersNotIn[] = $row_user;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Datas of the module's tables
        foreach ($this->fields as $tableName => $fields_table) {

            $n = 0;

            if ($tableName != MODULE) {

                $result = $this->pms_db->query('SELECT * FROM solutionsCMS_'.$tableName.' WHERE '.$fields_table['table']['fieldRef'].' = '.$this->itemId.' ORDER BY id');
                if ($result !== false) {
                    $prev_id = 0;
                    foreach ($result as $row) {
                        $id_lang = ($fields_table['table']['multi'] == 1 && isset($row['lang'])) ? $row['lang'] : 0;
                        if ($prev_id != 0 && $prev_id != $row['id']) $n++;
                        foreach ($fields_table['fields'] as $fieldName => $field) {
                            if ($field->type != 'separator')
                                $field->setValue($row[$fieldName], $n, $id_lang);
                        }
                        $prev_id = $row['id'];
                    }
                }
            }
            foreach ($fields_table['fields'] as $fieldName => $field) {
                if ($field->type != 'separator') {
                    $id_lang = (isset($fields_table['table']['multi']) && $fields_table['table']['multi'] == 1) ? PMS_DEFAULT_LANG : 0;
                    $inputName = $tableName.'_'.$fieldName.'_'.$id_lang;
                    if (isset($_GET[$inputName]) && empty($_POST))
                        $field->setValue($_GET[$inputName], $n, $id_lang);
                }
            }

            $this->setNumMaxRows($tableName);
        }
    }

    /**
     * Set file data from the database for the item with the given ID.
     *
     * @param int $id The ID of the item.
     *
     */
    public function setFilesDataFromDB($id)
    {
        $this->filesData = (NB_FILES > 0) ? $this->mediaModel->getFilesData($id) : [];
    }

    /**
     * Save the item to the database, handling add or edit actions.
     *
     * @param string $action The action to perform ('add' or 'edit').
     *
     */
    public function saveItem($action)
    {
        // Getting POST values
        foreach ($this->languages as $lang_tag => $language) {

            $id_lang = $language['id'];

            foreach ($this->fields as $tableName => $fields_table) {

                foreach ($fields_table['fields'] as $fieldName => $field) {

                    if ($id_lang == PMS_DEFAULT_LANG || $field->multilingual || $id_lang == 0) {

                        $fieldName = $tableName.'_'.$fieldName.'_';

                        if ($tableName == MODULE)
                            $fieldName .= (MULTILINGUAL && !$field->multilingual) ? PMS_DEFAULT_LANG : $id_lang;
                        else {
                            $id_lang = ($fields_table['table']['multi'] == 1) ? $id_lang : 0;
                            $fieldName .= ($fields_table['table']['multi'] == 1 && !$field->multilingual) ? PMS_DEFAULT_LANG : $id_lang;
                        }

                        if (isset($_POST[$fieldName])) {

                            foreach ($_POST[$fieldName] as $index => $value) {

                                switch ($field->type) {
                                    case 'date':
                                        $date = isset($_POST[$fieldName][$index]['date']) ? $_POST[$fieldName][$index]['date'] : '';
                                        if (!empty($date)) $date = DateUtils::gmStrtotime($date.' 00:00:00');
                                        if (is_numeric($date) && $date !== false)
                                            $field->setValue($date, $index, $id_lang);
                                        else
                                            $field->setValue(NULL, $index, $id_lang);
                                        break;
                                    case 'datetime':
                                        $date = isset($_POST[$fieldName][$index]['date']) ? $_POST[$fieldName][$index]['date'] : '';
                                        $hour = isset($_POST[$fieldName][$index]['hour']) ? $_POST[$fieldName][$index]['hour'] : '';
                                        $minute = isset($_POST[$fieldName][$index]['minute']) ? $_POST[$fieldName][$index]['minute'] : 0;
                                        if (!empty($date) && is_numeric($hour) && is_numeric($minute)) $date = DateUtils::gmStrtotime($date.' '.$hour.':'.$minute.':00');
                                        if (is_numeric($date) && $date !== false)
                                            $field->setValue($date, $index, $id_lang);
                                        else
                                            $field->setValue(NULL, $index, $id_lang);
                                        break;
                                    case 'password':
                                        $value = ($value != '') ? md5($value) : '';
                                        if ($value == '') $value = $field->getValue(false, $index, $id_lang);
                                        $field->setValue($value, $index, $id_lang);
                                        break;
                                    case 'checkbox':
                                    case 'multiselect':
                                        $value = (isset($_POST[$fieldName][$index]) && is_array($_POST[$fieldName][$index])) ? implode(',', $_POST[$fieldName][$index]) : null;
                                        $field->setValue($value, $index, $id_lang);
                                        break;
                                    case 'alias':
                                        $value = StrUtils::textFormat($_POST[$fieldName][$index]);
                                        $field->setValue($value, $index, $id_lang);
                                        break;
                                    default:
                                        $value = isset($_POST[$fieldName][$index]) ? $_POST[$fieldName][$index] : '';
                                        $field->setValue($value, $index, $id_lang);
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->setNumMaxRows($tableName);

        // Remove row if (all fields = empty) and if (tableName != MODULE)
        foreach ($this->fields as $tableName => $fields_table) {
            if ($tableName != MODULE) {

                $id_lang_table = ($fields_table['table']['multi'] == 1) ? PMS_DEFAULT_LANG : 0;

                $max_rows = $this->fields[$tableName]['maxRows'];

                for ($index = 0; $index < $max_rows; $index++) {

                    $empty = true;
                    $id_row = 0;
                    if (isset($_POST[$tableName.'_id_'.$id_lang_table][$index]))
                        $id_row = $_POST[$tableName.'_id_'.$id_lang_table][$index];

                    if ($id_row == 0 || $id_row == '') {

                        foreach ($fields_table['fields'] as $fieldName => $field) {
                            $value = $field->getValue(false, $index, $id_lang_table);
                            if (!empty($value)) $empty = false;
                        }
                        if ($empty) {
                            foreach ($fields_table['fields'] as $fieldName => $field) {
                                $field->removeValue($index);
                            }
                        }
                    }
                }
            }
        }

        if (VALIDATION && isset($_POST['checked']) && is_numeric($_POST['checked'])) $this->checked = $_POST['checked'];
        if (HOME && isset($_POST['home']) && is_numeric($_POST['home'])) $this->home = $_POST['home'];
        if (DATES && (!is_numeric($this->add_date) || $this->add_date == 0)) $this->add_date = time();
        if (RELEASE) {
            $day = (isset($_POST['publish_date_day'])) ? $_POST['publish_date_day'] : '';
            $month = (isset($_POST['publish_date_month'])) ? $_POST['publish_date_month'] : '';
            $year = (isset($_POST['publish_date_year'])) ? $_POST['publish_date_year'] : '';
            $hour = (isset($_POST['publish_date_hour'])) ? $_POST['publish_date_hour'] : '';
            $minute = (isset($_POST['publish_date_minute'])) ? $_POST['publish_date_minute'] : '';
            if (is_numeric($day) && is_numeric($month) && is_numeric($year) && is_numeric($hour) && is_numeric($minute))
                $this->publish_date = mktime($hour, $minute, 0, $month, $day, $year);
            else
                $this->publish_date = NULL;

            $day = (isset($_POST['unpublish_date_day'])) ? $_POST['unpublish_date_day'] : '';
            $month = (isset($_POST['unpublish_date_month'])) ? $_POST['unpublish_date_month'] : '';
            $year = (isset($_POST['unpublish_date_year'])) ? $_POST['unpublish_date_year'] : '';
            $hour = (isset($_POST['unpublish_date_hour'])) ? $_POST['unpublish_date_hour'] : '';
            $minute = (isset($_POST['unpublish_date_minute'])) ? $_POST['unpublish_date_minute'] : '';
            if (is_numeric($day) && is_numeric($month) && is_numeric($year) && is_numeric($hour) && is_numeric($minute))
                $this->unpublish_date = mktime($hour, $minute, 0, $month, $day, $year);
            else
                $this->unpublish_date = NULL;
        }
        if (isset($_POST['users'])) $this->users = $_POST['users'];
        if (!is_array($this->users)) $this->users = explode(',', $this->users);

        if (isset($_POST['show_langs'])) $this->show_langs = $_POST['show_langs'];
        if (!is_array($this->show_langs)) $this->show_langs = explode(',', $this->show_langs);
        if (isset($_POST['hide_langs'])) $this->hide_langs = $_POST['hide_langs'];
        if (!is_array($this->hide_langs)) $this->hide_langs = explode(',', $this->hide_langs);

        if ($this->checkFields()) {

            foreach ($this->languages as $lang_tag => $language) {

                $id_lang = $language['id'];

                // Add / Edit item in the table of the module
                $data = [];
                $data['id'] = $this->itemId;
                $data['lang'] = $id_lang;
                $data['rank'] = $this->rank;
                $data['home'] = $this->home;
                $data['checked'] = $this->checked;
                $data['add_date'] = $this->add_date;
                $data['edit_date'] = $this->edit_date;
                $data['publish_date'] = $this->publish_date;
                $data['unpublish_date'] = $this->unpublish_date;
                $data['show_langs'] = implode(',', $this->show_langs);
                $data['hide_langs'] = implode(',', $this->hide_langs);
                $data['users'] = implode(',', $this->users);

                foreach ($this->fields[MODULE]['fields'] as $fieldName => $field){
                    $data[$fieldName] = ($field->multilingual) ? $field->getValue(false, 0, $id_lang) : $field->getValue(false, 0, PMS_DEFAULT_LANG);
                }

                if ($action == 'add' && $this->adminContext->addAllowed) {

                    $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . MODULE, $data);

                    $this->addItem($result_insert, $id_lang);

                } elseif ($action == 'edit' && $this->adminContext->editAllowed) {

                    $query_exist = 'SELECT * FROM ' . $this->tableName . ' WHERE id = '.$this->itemId;
                    if (MULTILINGUAL) $query_exist .= ' AND lang = '.$id_lang;
                    $result_exist = $this->pms_db->query($query_exist);

                    $data['rank'] = $this->old_rank;

                    if ($result_exist !== false) {
                        if ($this->pms_db->last_row_count() > 0) {

                            $result_update = DbUtils::dbPrepareUpdate($this->pms_db, 'solutionsCMS_' . MODULE, $data);

                            $this->editItem($result_update, $id_lang);
                        } else {
                            $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . MODULE, $data);

                            $this->addItem($result_insert, $id_lang);
                        }
                    }
                }

                // Add / Edit items in other tables
                if (empty($_SESSION['msg_error']) && $this->itemId > 0) {

                    foreach ($this->fields as $tableName => $fields_table) {

                        if ($tableName != MODULE) {

                            $max_rows = $this->fields[$tableName]['maxRows'];

                            $id_lang_table = ($fields_table['table']['multi'] == 1) ? $id_lang : 0;
                            for ($index = 0; $index < $max_rows; $index++) {

                                if($action == 'add') $fields_table['fields']['id']->setValue(null, $index, $id_lang_table);

                                $id_row = $fields_table['fields']['id']->getValue(false, $index, $id_lang_table);

                                $data = [];
                                $data['lang'] = $id_lang_table;
                                $data[$fields_table['table']['fieldRef']] = $this->itemId;

                                foreach ($fields_table['fields'] as $fieldName => $field)
                                    $data[$fieldName] = $field->getValue(false, $index, $id_lang_table);

                                if (($id_lang_table == 0 || $id_lang_table == PMS_DEFAULT_LANG) && empty($id_row) && ($this->adminContext->addAllowed)) {

                                    $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . $tableName, $data);
                                    if ($result_insert->execute() !== false) {
                                        $id_item = $this->pms_db->lastInsertId();
                                        $fields_table['fields']['id']->setValue($id_item, $index, $id_lang_table);

                                        if ($id_lang_table > 0) {
                                            foreach ($this->languages as $language) {
                                                $lang2 = $language['id'];
                                                if ($lang2 != $id_lang_table) {
                                                    $data['id'] = $id_item;
                                                    $data['lang'] = $lang2;
                                                    $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . $tableName, $data);
                                                    if ($result_insert->execute() !== false) {
                                                        foreach ($fields_table['fields'] as $fieldName => $field) {
                                                            $fields_table['fields'][$fieldName]->setValue($field->getValue(false, $index, $id_lang_table), $index, $lang2);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } elseif ($id_row > 0 && ($this->adminContext->editAllowed)) {

                                    $query_exist = 'SELECT * FROM solutionsCMS_' . $tableName . ' WHERE id = ' . $id_row;
                                    if ($fields_table['table']['multi'] == 1) $query_exist .= ' AND lang = '.$id_lang_table;
                                    $result_exist = $this->pms_db->query($query_exist);

                                    if ($result_exist !== false) {
                                        if ($this->pms_db->last_row_count() > 0) {
                                            $result_update = DbUtils::dbPrepareUpdate($this->pms_db, 'solutionsCMS_'.$tableName, $data);
                                            $result_update->execute();
                                        } else {
                                            $result_insert = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_'.$tableName, $data);
                                            if ($result_insert->execute() !== false) {
                                                $fields_table['fields']['id']->setValue($id_row, $index, $id_lang_table);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $this->itemId;

        } else {
            $_SESSION['msg_error'][] = $this->adminContext->texts['FORM_ERRORS'];
            return false;
        }
    }

    /**
     * Check the form fields submitted via POST.
     *
     * @return bool True if all fields are valid, false otherwise.
     */
    private function checkFields()
    {
        $valid = true;
        
        foreach($this->fields as $tableName => $fields_table){
                    
            $id_lang = ($tableName != MODULE && $fields_table['table']['multi'] == 0) ? 0 : PMS_DEFAULT_LANG;
                                    
            foreach($fields_table['fields'] as $fieldName => $field){
                
                $values = $field->getAllValues();
                
                foreach($values as $index => $value){
                    
                    $value = $field->getValue(false, $index, $id_lang);
                    
                    if($field->required && $value == ''){
                        $field->setNotice($this->adminContext->texts['REQUIRED_FIELD'], $index);
                        $valid = false;
                    }
                    
                    switch($field->validation){
                        case 'mail':
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL) && $value != ''){
                                $field->setNotice($this->adminContext->texts['INVALID_EMAIL'], $index);
                                $valid = false;
                            }
                        break;
                        case 'numeric':
                            if(!is_numeric($value)){
                                if($value != ''){
                                    $field->setNotice($this->adminContext->texts['NUMERIC_EXPECTED'], $index);
                                    $valid = false;
                                }
                                $field->setValue(0, $index);
                            }                        
                        break;
                    }
                    
                    if($field->unique && $value != '' && $this->pms_db != false){
                        $query_unique = 'SELECT * FROM solutionsCMS_'.$tableName.' WHERE `'.$fieldName.'` = \''.$value.'\' AND id != ';
                        $query_unique .= ($tableName == MODULE) ? $this->itemId : $fields_table['fields']['id']->getValue(false, $index);
                        if(DbUtils::dbColumnExists($this->pms_db, 'solutionsCMS_'.$tableName, 'lang')) $query_unique .= ' AND lang = '.PMS_DEFAULT_LANG;
                        $res_unique = $this->pms_db->query($query_unique);
                        if($res_unique !== false && $this->pms_db->last_row_count() > 0){
                            $field->setNotice($this->adminContext->texts['VALUE_ALREADY_EXISTS'], $index);
                            $valid = false;
                        }
                    }
                }
            }
        }
        return $valid;
    }

    /**
     * Get fields from a DOMNodeList and return a collection of Field objects.
     *
     * @param DOMNodeList $itemList The list of DOM nodes to process.
     *
     * @return array A collection of Field objects.
     */
    private function getFieldsFromNode($itemList)
    {
        $fields = [];
                
        foreach($itemList as $item){
            
            $type = htmlentities($item->getAttribute('type'), ENT_QUOTES, 'UTF-8');
            $label = Helpers::getTranslation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'), $this->adminContext->texts);
            $name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
            $required = htmlentities($item->getAttribute('required'), ENT_QUOTES, 'UTF-8');
            $multilingual = htmlentities($item->getAttribute('multi'), ENT_QUOTES, 'UTF-8');
            $editor = htmlentities($item->getAttribute('editor'), ENT_QUOTES, 'UTF-8');
            $options = [];
            $optFilters = [];
            $validation = htmlentities($item->getAttribute('validation'), ENT_QUOTES, 'UTF-8');
            $unique = htmlentities($item->getAttribute('unique'), ENT_QUOTES, 'UTF-8');
            $comment = Helpers::getTranslation(htmlentities($item->getAttribute('comment'), ENT_QUOTES, 'UTF-8'), $this->adminContext->texts);
            $active = htmlentities($item->getAttribute('active'), ENT_QUOTES, 'UTF-8');
            $optionTable = '';
            $roles = htmlentities($item->getAttribute('roles'), ENT_QUOTES, 'UTF-8');
            if($roles == '') $roles = 'all';
            $roles = explode(',', str_replace(' ', '', $roles));
            $filterName = '';
            $itemOptions = $item->getElementsByTagName('options')->item(0);
            
            if(in_array($_SESSION['user']['type'], $roles) || in_array('all', $roles)){

                if($comment != '') $comment = str_ireplace('{currency}', PMS_DEFAULT_CURRENCY_SIGN, $comment);

                if(!empty($itemOptions)){
                    $optionList = $itemOptions->getElementsByTagName('option');
                    $optionTable = htmlentities($itemOptions->getAttribute('table'), ENT_QUOTES, 'UTF-8');
                    $fieldLabel = htmlentities($itemOptions->getAttribute('fieldlabel'), ENT_QUOTES, 'UTF-8');
                    $fieldValue = htmlentities($itemOptions->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
                    $filterName = htmlentities($itemOptions->getAttribute('filtername'), ENT_QUOTES, 'UTF-8');
                    $optFilter = htmlentities($itemOptions->getAttribute('optfilter'), ENT_QUOTES, 'UTF-8');
                    
                    if($this->pms_db !== false && $optionTable != '' && $fieldLabel != '' && $fieldValue != ''){
                        if($optionList->length > 0){
                            foreach($optionList as $option)
                                $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = htmlentities($option->nodeValue, ENT_QUOTES, 'UTF-8');
                        }
                        
                        $order = htmlentities($itemOptions->getAttribute('order'), ENT_QUOTES, 'UTF-8');
                        
                        $query_option = 'SELECT * FROM '.$optionTable;
                        $query_option_cond = '';
                        
                        if(DbUtils::dbColumnExists($this->pms_db, $optionTable, 'lang')){
                            $query_option_cond .= ($query_option_cond != '') ? ' AND ' : ' WHERE ';
                            $query_option_cond .= 'lang = '.PMS_DEFAULT_LANG;
                        }
                        
                        if(!in_array($_SESSION['user']['type'], array('administrator', 'manager', 'editor')) && DbUtils::dbColumnExists($this->pms_db, $optionTable, 'users')){
                            $query_option_cond .= ($query_option_cond != '') ? ' AND ' : ' WHERE ';
                            $query_option_cond .= 'users REGEXP \'(^|,)'.$_SESSION['user']['id'].'(,|$)\'';
                        }
                        
                        $query_option .= $query_option_cond;

                        $result_option = $this->pms_db->query($query_option);
                        if($result_option !== false){
                            $optionLabel = '';
                            foreach($result_option as $row_option){
                                
                                $arr_fieldLabel = array_filter(preg_split('/([^a-z0-9_]+)/i', $fieldLabel));
                                $seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $fieldLabel)));
                                
                                $optionLabel = '';
                                $n2 = 0;
                                $lgt2 = count($arr_fieldLabel);
                                foreach($arr_fieldLabel as $str_fieldLabel){
                                    $optionLabel .= $row_option[$str_fieldLabel];
                                    if(isset($seps[$n2]) && $n2+1 < $lgt2) $optionLabel .= $seps[$n2];
                                    $n2++;
                                }
                                $optionValue = $row_option[$fieldValue];
                                $options[$optionValue] = $optionLabel;
                                if($optFilter != '')
                                    $optFilters[$optionValue] = $row_option[$optFilter];
                            }
                        }
                        
                    }elseif($optionList->length > 0){
                        foreach($optionList as $option)
                            $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = Helpers::getTranslation(htmlentities(str_ireplace('{currency}', PMS_DEFAULT_CURRENCY_SIGN, $option->nodeValue), ENT_QUOTES, 'UTF-8'), $this->adminContext->texts);
                    
                    }elseif($itemOptions->getElementsByTagName('min')->length == 1 && $itemOptions->getElementsByTagName('max')->length == 1){
                        $min = htmlentities($itemOptions->getElementsByTagName('min')->item(0)->nodeValue, ENT_QUOTES, 'UTF-8');
                        $max = htmlentities($itemOptions->getElementsByTagName('max')->item(0)->nodeValue, ENT_QUOTES, 'UTF-8');
                        if(is_numeric($min) && is_numeric($max)){
                            for($i = $min; $i <= $max; $i++)
                                $options[$i] = $i;
                        }
                    }
                }
                if($type == 'filelist'){
                    $itemOptions = $item->getElementsByTagName('options')->item(0);
                    $optionDirectory = htmlentities($itemOptions->getAttribute('directory'), ENT_QUOTES, 'UTF-8');
                    $optionDirectory = SYSBASE . str_replace('{template}', PMS_TEMPLATE, $optionDirectory);
                    $rep = opendir($optionDirectory) or die('Error directory opening : '.$optionDirectory);
                    while($entry = @readdir($rep)){
                        if($entry != '.' && $entry != '..' && is_file($optionDirectory . '/' . $entry)){
                            $entry = str_replace('.php', '', $entry);
                            $options[$entry] = $entry;
                        }
                    }
                    closedir($rep);
                }
                $fields[$name] = new Field($name, $label, $type, $required, $validation, $options, $multilingual, $unique, $comment, $active, $editor, $optionTable, $roles, $filterName, $optFilters);
            }
        }
        return $fields;
    }

    /**
     * Get fields from the module configuration and return a collection of Field objects.
     *
     * @return array A collection of Field objects.
     */
    private function getFields()
    {
        $dom = $this->module->configDom;
            
        $root = $dom->getElementsByTagName('module')->item(0);
        $form = $root->getElementsByTagName('form')->item(0);
        
        $fields = [];
        
        $tables = $form->getElementsByTagName('table');
        foreach($tables as $table){
            $tableName = $table->getAttribute('name');
            $tableLabel = Helpers::getTranslation($table->getAttribute('label'), $this->adminContext->texts);
            $fieldRef = $table->getAttribute('fieldref');
            $multi = $table->getAttribute('multi');
            $itemList = $table->getElementsByTagName('field');
            
            $tmp_fields = [];
            $tmp_fields['id'] = new Field('id', 'ID', 'id', 0, 'numeric', null, 0, 1, '', 0, 0, '', 'all', '', null);
            $tmp_fields += $this->getFieldsFromNode($itemList);
            
            $fields[$tableName] = array('table' => array('tableLabel' => $tableLabel, 'fieldRef' => $fieldRef, 'multi' => $multi), 'fields' => $tmp_fields);
        }
        
        if($form->hasChildNodes()){  

            $childNodes = $form->childNodes;
            $tableNodes = [];
            foreach($childNodes as $node)
                if($node->nodeName == 'table') $tableNodes[] = $node;
            
            foreach($tableNodes as $node)
                $node->parentNode->removeChild($node);
                
            $itemList = $form->getElementsByTagName('field');

            $fields = array(MODULE => array('table' => null, 'fields' => $this->getFieldsFromNode($itemList))) + $fields;
        }
        
        return $fields;
    }

    /**
     * Display a field in the form.
     *
     * @param Field $field The field object.
     * @param string $table The name of the table.
     * @param int $index The index of the field.
     * @param int $id_lang The ID of the current language.
     *
     */
    public function displayField($field, $table, $index, $id_lang)
    {
        $name = $field->name;
        $type = $field->type;
        $options = $field->options;
        $editor = $field->editor;
        $notice = $field->getNotice($index);
        $active = $field->active;
        $optFilters = $field->optFilters;
        $filterName = $field->filterName;
        
        $value = $field->getValue(true, $index, $id_lang);
        if(!is_array($value)) $value = stripslashes($value);
        
        $str_active = ($active == 0) ? ' readonly="readonly"' : '';
        
        $str_filter = ($filterName != '') ? ' data-filter="'.$filterName.'_'.$id_lang.'['.$index.']"' : '';
        
        $class = '';
        if($notice != '' && ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0)) $class .= ' is-invalid';

        $inputdescr = $table.'_'.$name.'_'.$id_lang;
        $input_id = $inputdescr.'_'.$index;
        $inputname = $inputdescr.'['.$index.']';
                    
        switch($type){
            case 'id' :
                if($value > 0) echo $value;
                echo '<input type="hidden"'.$str_active.' name="'.$inputname.'" id="'.$input_id.'" value="'.$value.'">'."\n";
            break;
            case 'text' :
            case 'alias' :
                echo '<input type="text"'.$str_active.' name="'.$inputname.'" id="'.$input_id.'" value="'.$value.'" rel='.$inputdescr.' class="form-control'.$class.' typeahead">'."\n";
                if(!empty($options) && $index == 0){
                    echo '
                    <script>
                        var dataset_'.$inputdescr.' = new Bloodhound({
                            datumTokenizer: Bloodhound.tokenizers.whitespace,
                            queryTokenizer: Bloodhound.tokenizers.whitespace,
                            local: [';
                                foreach($options as $option){
                                    $key = key($options);
                                    echo '\''.addslashes($options[$key]).'\'';
                                    if(next($options) !== false) echo ', ';
                                }
                                echo '
                            ]
                        });
                        function search_'.$inputdescr.'(q, sync){
                            if(q === \'\')
                                sync(dataset_'.$inputdescr.'.index.all());
                            else
                                dataset_'.$inputdescr.'.search(q, sync);
                        }
                        typeahead_opts[\''.$inputdescr.'\'] = {
                            name: \''.$inputdescr.'\',
                            source: search_'.$inputdescr.'
                        };
                    </script>';
                }
            break;
            case 'password' :
                echo '<input type="password"'.$str_active.' name="'.$inputname.'" value="" size="30" class="form-control'.$class.'">'."\n";
            break;
            case 'textarea' :
                if($editor == 1 && EDITOR_TYPE == 'builder') echo '<div class="pageflow pflow-compact" rel="#'.$input_id.'"></div>';
                echo '<textarea name="'.$inputname.'"'.$str_active.' id="'.$input_id.'" cols="40" rows="5" class="form-control'.$class.'" data-editor="'.$editor.'">'.$value.'</textarea>'."\n";
            break;
            case 'select' :
            case 'filelist' :
                echo '<select name="'.$inputname.'"'.$str_active.' id="'.$input_id.'" class="form-select'.$class.'"'.$str_filter.'>'."\n";
                
                $selected = ($value == '') ? ' selected="selected"' : '';
                echo '<option value=""'.$selected.'>-</option>'."\n";
                
                foreach($options as $option){
                    $key = key($options);
                    $selected = (strval($value) == strval($key)) ? ' selected="selected"' : '';
                    $rel = (is_array($optFilters) && isset($optFilters[$key])) ? ' rel="'.$optFilters[$key].'"' : '';
                    echo '<option value="'.$key.'"'.$rel.' '.$selected.'>'.$options[$key].'</option>'."\n";
                    next($options);
                }
                echo '</select>'."\n";
            break;
            case 'multiselect' :
                $size = (count($options) > 4) ? 8 : 4;
                $selected = [];
                $value = explode(',', $value);
                
                echo '<select name="'.$inputname.'_tmp[]" multiple="multiple" id="'.$input_id.'_tmp" size="'.$size.'"'.$str_active.' class="form-select'.$class.'">'."\n";
                
                foreach($options as $key => $option){
                    if((is_array($value) && !in_array($key, $value)) || (!is_array($value) && $key != $value))
                        echo '<option value="'.$key.'">'.$options[$key].'</option>'."\n";
                }
                echo '</select>';
                
                echo '<div class="d-flex flex-column">
                    <a href="#" class="btn btn-circle btn-secondary remove_option mb-2" rel="'.$input_id.'"><i class="fa fa-fw fa-arrow-left"></i></a>
                    <a href="#" class="btn btn-circle btn-secondary add_option" rel="'.$input_id.'"><i class="fa fa-fw fa-arrow-right"></i></a>
                    </div>
                    <select name="'.$inputname.'[]" multiple="multiple" id="'.$input_id.'" size="'.$size.'"'.$str_active.' class="form-select">'."\n";
                    foreach($options as $key => $option){
                        if(((is_array($value) && in_array($key, $value)) || (!is_array($value) && $key == $value)) && $key != '')
                            echo '<option value="'.$key.'" selected="selected">'.$options[$key].'</option>'."\n";
                    }
                    echo '</select>'."\n";
            break;
            case 'checkbox' :
                foreach($options as $option){
                    $key = key($options);
                    $checked = (in_array($key, explode(',', $value))) ? ' checked="checked"' : '';     
                    $switch = (count($options) == 1) ? ' form-switch' : '';        
                    echo '<div class="form-check'.$switch.' form-check-inline"><label class="form-check-label"><input name="'.$inputname.'[]" type="checkbox"'.$str_active.' class="form-check-input" value="'.$key.'"'.$checked.'>&nbsp;'.$options[$key].'</label></div>'."\n";
                    next($options);
                }
            break;
            case 'radio' :
                foreach($options as $option){
                    $key = key($options);
                    $checked = ($value == $key) ? ' checked="checked"' : '';        
                    echo '<div class="form-check form-check-inline"><label class="form-check-label"><input name="'.$inputname.'" type="radio"'.$str_active.' class="form-check-input" value="'.$key.'"'.$checked.'>&nbsp;'.$options[$key].'</label></div>'."\n";
                    next($options);
                }
            break;
            case 'date' :
            case 'datetime' :
                    
                if($type == 'datetime'){
                    $date = is_numeric($value) ? gmdate('Y-m-d', $value) : '';
                    if(is_numeric($value)){
                        $hour = gmdate('H', $value);
                        $minute = gmdate('i', $value);
                    }else{
                        $hour = '';
                        $minute = 0;
                    }
                }else
                    $date = is_numeric($value) ? gmdate('Y-m-d', $value) : '';
                    
                
                echo '
                <div class="input-group">
                    <div class="input-group-text"><i class="fa fa-fw fa-calendar"></i></div>
                    <input type="text" class="form-control datepicker'.$class.'" name="'.$inputname.'[date]"'.$str_active.' value="'.$date.'">
                </div>';
                
                if($type == 'datetime'){
                    echo '&nbsp;&nbsp;<select name="'.$inputname.'[hour]"'.$str_active.' class="form-control'.$class.'"'.$str_active.'>'."\n";
                    $selected = ($hour == '') ? ' selected="selected"' : '';
                    echo '<option value=""'.$selected.'>-</option>'."\n";
                    for($i = 0; $i <= 23; $i++){
                        $selected = (strval($i) == strval($hour)) ? ' selected="selected"' : '';
                        echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>'."\n";
                    }
                    echo '</select>&nbsp;:&nbsp;'."\n";
                    
                    echo '<select name="'.$inputname.'[minute]"'.$str_active.' class="form-control'.$class.'"'.$str_active.'>'."\n";
                    $selected = ($minute == '') ? ' selected="selected"' : '';
                    echo '<option value=""'.$selected.'>-</option>'."\n";
                    for($i = 0; $i <= 59; $i++){
                        $selected = (strval($i) == strval($minute)) ? ' selected="selected"' : '';
                        $zero = ($i < 10) ? '0' : '';
                        echo '<option value="'.$i.'"'.$selected.'>'.$zero.$i.'</option>'."\n";
                    }
                    echo '</select>'."\n";
                }
            break;
        }
    }

    /**
     * Generate the class name attribute for a field.
     *
     * @param string $type The type of the field.
     * @param string $validation The validation rule for the field.
     * @param string $notice The notice message for the field.
     * @param int $id_lang The ID of the current language.
     *
     * @return string The generated class name attribute.
     */
    public function getClassAttr($type, $validation, $notice, $id_lang)
    {
        $class = '';
        if(($type == 'text' || $type == 'select') && $validation == 'numeric')
            $class .= ' numeric';
        if(($type == 'text' && $validation == 'numeric')
            || $type == 'select'
            || $type == 'filelist'
            || $type == 'multiselect'
            || $type == 'date'
            || $type == 'datetime')
            $class .= ' form-inline';
        if($notice != '' && ($id_lang == PMS_DEFAULT_LANG || $id_lang == 0))
            $class .= ' is-invalid';
        return $class;
    }

    /**
     * Set the maximum number of rows for a table in the form.
     *
     * @param string $tableName The name of the table.
     *
     */
    private function setNumMaxRows($tableName)
    {
        $maxRows = 0;
        foreach($this->fields[$tableName]['fields'] as $fieldName => $field){
            $numRows = count($field->getAllValues());
            if($numRows > $maxRows) $maxRows = $numRows;
        }
        $this->fields[$tableName]['maxRows'] = $maxRows;
    }

    /**
     * Browse the media directory and retrieve all files.
     *
     * @param string $dir The directory containing the files.
     * @param array $files The array to store the file data.
     *
     * @return array The array of file data.
     */
    public function browseFiles($dir, $files = array())
    {
        if(is_dir($dir)){
            $rep = opendir($dir) or die('Error directory opening : '.$dir);
        
            while($entry = readdir($rep)){
                if(is_dir($dir . '/' . $entry) && $entry != '.' && $entry != '..')
                    
                    $files = $this->browseFiles($dir . '/' . $entry, $files);
                    
                else{
                    if(is_file($dir . '/' . $entry)){
                        
                        $ext = substr($entry, strrpos($entry, '.')+1);
                        $weight = FileUtils::fileSizeConvert(filesize($dir . '/' . $entry));
                        $dim = @getimagesize($dir . '/' . $entry);
                        
                        if((is_array($dim) && $dim[0] > 0 && $dim[1] > 0) || stripos(FileUtils::getFileMimeType($dir . '/' . $entry), 'image') !== false){
                            $w = $dim[0];
                            $h = $dim[1];
                        }else{
                            $w = '';
                            $h = '';
                        }
                        $filename = str_replace('.'.$ext, '', substr($dir . '/' . $entry, strrpos($dir . '/' . $entry, '/')+1));
                        
                        $files[] = array($dir . '/' . $entry, $filename, $ext, $weight, $w, $h);
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Upload files and insert file records into the database.
     *
     * @param int $id_lang The ID of the current language.
     * @param string $dir The directory containing the files.
     *
     */
    public function uploadFiles($id_lang, $dir)
    {
        if($id_lang == 0 || $id_lang == PMS_DEFAULT_LANG || FILE_MULTI){
            
            $browsed_files = $this->browseFiles($dir);
        
            foreach($browsed_files as $file){
            
                $type = ($file[4] == '' && $file[5] == '') ? 'other' : 'image';

                $data['id'] = null;
                $data['lang'] = $id_lang;
                $data['file'] = $file[1].'.'.$file[2];
                $data['id_item'] = $this->itemId;
                $data['type'] = $type;
                $data['checked'] = 1;
                $data['home'] = 0;
                    
                $result_rank = $this->pms_db->query('SELECT `rank` FROM ' . $this->tableName . '_file ORDER BY `rank` DESC LIMIT 1');
                $rank = ($result_rank !== false && $this->pms_db->last_row_count() > 0) ? $result_rank->fetchColumn(0) + 1 : 1;
                $data['rank'] = $rank;
                
                $result = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . MODULE . '_file', $data);
                if($result->execute() !== false){
                    
                    $error = true;
                    
                    $id_file = $this->pms_db->lastInsertId();
                
                    if($type == 'other'){
                        
                        if(!is_dir(SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file))
                            mkdir(SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file, 0777);
                        chmod(SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file, 0777);
                        
                        if(copy($file[0], SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file . '/' . $file[1].'.'.$file[2]))
                            $error = false;
                        
                    }elseif($type == 'image'){
                        
                        if(RESIZING == 0 || RESIZING == 1){
                        
                            if(!is_dir(SYSBASE . 'public/medias/' . MODULE . '/big/'.$id_file))
                                mkdir(SYSBASE . 'public/medias/' . MODULE . '/big/'.$id_file, 0777);
                            chmod(SYSBASE . 'public/medias/' . MODULE . '/big/'.$id_file, 0777);
                            
                            if(FileUtils::imgResize($file[0], SYSBASE . 'public/medias/' . MODULE . '/big/'.$id_file, MAX_W_BIG, MAX_H_BIG))
                                $error = false;
                        }
                        if(RESIZING == 1){
                            
                            if(!is_dir(SYSBASE . 'public/medias/' . MODULE . '/medium/'.$id_file))
                                mkdir(SYSBASE . 'public/medias/' . MODULE . '/medium/'.$id_file, 0777);
                            chmod(SYSBASE . 'public/medias/' . MODULE . '/medium/'.$id_file, 0777);
                            
                            if(FileUtils::imgResize($file[0], SYSBASE . 'public/medias/' . MODULE . '/medium/'.$id_file, MAX_W_MEDIUM, MAX_H_MEDIUM))
                                $error = false;
                            
                            if(!is_dir(SYSBASE . 'public/medias/' . MODULE . '/small/'.$id_file))
                                mkdir(SYSBASE . 'public/medias/' . MODULE . '/small/'.$id_file, 0777);
                            chmod(SYSBASE . 'public/medias/' . MODULE . '/small/'.$id_file, 0777);
                            
                            if(FileUtils::imgResize($file[0], SYSBASE . 'public/medias/' . MODULE . '/small/'.$id_file, MAX_W_SMALL, MAX_H_SMALL))
                                $error = false;
                        }
                    }
                    if(is_file($file[0])) unlink($file[0]);
                    
                    if($error === true)
                        $this->pms_db->query('DELETE FROM ' . $this->tableName . '_file WHERE id = '.$id_file);
                    else{
                        $data['id'] = $id_file;
                        $this->uploadedFiles[] = $data;
                    }
                }
            }
        }else{
            foreach($this->uploadedFiles as $file){
                $file['lang'] = $id_lang;
                $result = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_' . MODULE . '_file', $file);
                $result->execute();
            }
        }
    }

    /**
     * Insert an item into the database and handle file updates.
     *
     * @param PDOStatement $result_insert The prepared query for insertion.
     * @param int $id_lang The ID of the current language.
     *
     */
    public function addItem($result_insert, $id_lang)
    {
        $lang = '';
        
        if(MULTILINGUAL){
            $result_lang = $this->pms_db->query('SELECT title FROM solutionsCMS_lang WHERE id = '.$id_lang);
            if($result_lang !== false && $this->pms_db->last_row_count() > 0) $lang = $result_lang->fetchColumn(0).' - ';
        }
        
        if($result_insert->execute() !== false){
            
            if($this->itemId == 0) $this->itemId = $this->pms_db->lastInsertId();
            
            if(is_numeric($this->itemId) && $this->itemId > 0)
                $_SESSION['msg_success'][] = $lang.' '.$this->adminContext->texts['ADD_SUCCESS'];
            else
                $_SESSION['msg_error'][] = $lang.' '.$this->adminContext->texts['UPDATE_ERROR'];
        }else
            $_SESSION['msg_error'][] = $lang.' '.$this->adminContext->texts['UPDATE_ERROR'];
            
        if(NB_FILES > 0){
            
            $dir = SYSBASE . 'public/medias/' . MODULE . '/tmp/'.$_SESSION['token'] . '/' . $id_lang;
            
            $this->uploadFiles($id_lang, $dir);
            
            $this->updateFileLabel($id_lang);
        }

        if(MODULE == 'lang') $this->moduleModel->completeLang($this->itemId);
    }

    /**
     * Update an item in the database and handle file updates.
     *
     * @param PDOStatement $result_update The prepared query for updating.
     * @param int $id_lang The ID of the current language.
     *
     */
    public function editItem($result_update, $id_lang)
    {
        $lang = '';
        
        if(MULTILINGUAL){
            $result_lang = $this->pms_db->query('SELECT title FROM solutionsCMS_lang WHERE id = '.$id_lang);
            if($result_lang !== false && $this->pms_db->last_row_count() > 0) $lang = $result_lang->fetchColumn(0).' - ';
        }

        try {
            if ($result_update->execute() !== false)
                $_SESSION['msg_success'][] = $lang . ' ' . $this->adminContext->texts['UPDATE_SUCCESS'];
            else
                $_SESSION['msg_error'][] = $lang . ' ' . $this->adminContext->texts['UPDATE_ERROR'];
        } catch (\PDOException $e) {

        }
            
        if(NB_FILES > 0){
            
            $dir = SYSBASE . 'public/medias/' . MODULE . '/tmp/' . $_SESSION['token'] . '/' . $id_lang;
            
            $this->uploadFiles($id_lang, $dir);
            
            $this->updateFileLabel($id_lang);
        }
    }

    /**
     * Update the label of a media file in the database.
     *
     * @param int $id_lang The ID of the current language.
     *
     */
    public function updateFileLabel($id_lang)
    {
        $query_file = 'SELECT * FROM ' . $this->tableName . '_file WHERE id_item = '.$this->itemId;
        if(MULTILINGUAL) $query_file .= ' AND lang = '.$id_lang;

        $result_file = $this->pms_db->query($query_file);
        if($result_file !== false){
            foreach($result_file as $row){
                
                $file_id = $row['id'];
                $file_type = $row['type'];
                
                if(isset($_POST['file_'.$file_id.'_'.$id_lang.'_label'])){
                    $file_label = $this->pms_db->quote($_POST['file_'.$file_id.'_'.$id_lang.'_label']);
                        
                    $query = 'UPDATE solutionsCMS_' . MODULE . '_file SET label = '.$file_label.' WHERE id = '.$file_id;
                    if(MULTILINGUAL) $query .= ' AND lang = '.$id_lang;
                    
                    $this->pms_db->query($query);
                }
            }
        }
    }

    /**
     * Delete a row from a table in the form.
     *
     * @param int $id The ID of the item.
     * @param int $id_row The ID of the row to delete.
     * @param string $table The name of the table.
     * @param string $fieldref The foreign key column name.
     *
     */
    public function deleteRow($id, $id_row, $table, $fieldref)
    {
        if(DbUtils::dbTableExists($this->pms_db, 'solutionsCMS_' . $table) && DbUtils::dbColumnExists($this->pms_db, 'solutionsCMS_' . $table, $fieldref)){
            if($this->pms_db->query('DELETE FROM solutionsCMS_' . $table . ' WHERE id = ' . $id_row . ' AND ' . $fieldref . ' = ' . $id) !== false)
                $_SESSION['msg_success'][] = $table . ' (ID ' . $id_row . ') - '. $this->adminContext->texts['DELETE_SUCCESS'];
        }
    }

    /**
     * Set temporary files for the current session.
     *
     */
    public function setTmpFiles()
    {
        $tmpFiles = [];
        
        foreach ($this->languages as $lang) {

            if (!empty($_SESSION['msg_error']) && empty($_SESSION['msg_success'])) {
                $tmpFiles[$lang['id']] = $this->browseFiles(SYSBASE . 'public/medias/' . MODULE . '/tmp/' . $_SESSION['token'] . '/' . $lang['id']);
            }
        }
        $this->tmpFiles = $tmpFiles;
    }

    /**
     * Set the media counter for uploaded and available files.
     *
     */
    public function setMediasCounter()
    {
        $count = [];

        foreach ($this->languages as $lang) {

            $count[$lang['id']]['num_files'] = count($this->filesData[$lang['id']] ?? []);
            $count[$lang['id']]['num_uploaded'] = count($this->tmpFiles[$lang['id']] ?? []) + $count[$lang['id']]['num_files'];
            $count[$lang['id']]['max_files'] = NB_FILES - $count[$lang['id']]['num_uploaded'];
        }
        $this->mediasCounter = $count;
    }
}