<?php

namespace Pandao\Admin\Models;

use Pandao\Admin\Core\Helpers;
use Pandao\Common\Utils\DbUtils;
use Pandao\Common\Utils\StrUtils;
use Pandao\Common\Utils\DateUtils;

class ListModel
{
    protected $pms_db;
    protected $module;
    protected $cols;
    protected $filters;
    protected $adminContext;
    protected $moduleModel;

    public $tableName;

    /**
     * ListModel constructor. Initializes a ListModel object with the provided parameters.
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
        $this->cols = $this->getCols();
        $this->filters = $this->getFilters();
        $this->moduleModel = $moduleModel;
    }

    /**
     * Get the columns defined in the module configuration file.
     *
     * @return array A collection of Column objects.
     */
    public function getCols()
    {
        $dom = $this->module->configDom;
        
        $root = $dom->getElementsByTagName('module')->item(0);
        $list = $root->getElementsByTagName('list')->item(0);
        $itemList = $list->getElementsByTagName('col');

        $columns = array();
        
        foreach ($itemList as $item){
            
            $label = Helpers::getTranslation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'), $this->adminContext->texts);
            $name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
            $type = htmlentities($item->getAttribute('type'), ENT_QUOTES, 'UTF-8');
            $link = htmlentities($item->getAttribute('link'), ENT_QUOTES, 'UTF-8');
            $table = htmlentities($item->getAttribute('table'), ENT_QUOTES, 'UTF-8');
            $fieldRef = htmlentities($item->getAttribute('fieldref'), ENT_QUOTES, 'UTF-8');
            $fieldValue = htmlentities($item->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
            
            $caseValues = array();
            $itemValues = $item->getElementsByTagName('values')->item(0);
            if(!empty($itemValues)){
                $valueList = $itemValues->getElementsByTagName('value');
                if($valueList->length > 0){
                    foreach($valueList as $value)
                        $caseValues[htmlentities($value->getAttribute('case'), ENT_QUOTES, 'UTF-8')] = Helpers::getTranslation($value->nodeValue, $this->adminContext->texts);
                }
            }
            
            if($fieldValue != '' && $table != '') $key = $table.'.'.$fieldValue; else $key = $name;
            
            $columns[$key] = new Column($name, $label, $type, $link, $table, $fieldRef, $fieldValue, $caseValues);
        }
        return $columns;
    }
    
    /**
     * Set and return the values of columns based on the current database row.
     *
     * @param array $row The current row in the database.
     * @param int $i The index of the current row.
     *
     * @return array A collection of Column objects.
     */
    public function getColsValues($row, $i)
    {
        foreach($this->cols as $col){
            $table = $col->table;
            $colname = $col->name;

            if($table != ''){
                $value = $row[$colname];
                
                if($this->pms_db !== false && DbUtils::dbTableExists($this->pms_db, $table)){
                    
                    if(preg_match('/.*(int).*/i', DbUtils::dbColumnType($this->pms_db, $table, $col->fieldRef)) === false || !empty($value)){
                        
                        if($value == 0) $value = '-';
                        else{
                            $req_table = 'SELECT * FROM '.$table.' WHERE `'.$col->fieldRef.'` IN('.$value.')';
                            if(DbUtils::dbColumnExists($this->pms_db, $table, 'lang')) $req_table .= ' AND lang = '.PMS_DEFAULT_LANG;
                            $res_table = $this->pms_db->query($req_table);
                            if($res_table !== false){
                                $value = '';
                                $nb_values = $this->pms_db->last_row_count();
                                
                                foreach($res_table as $j => $row_table){
                                    $fieldValue = $col->fieldValue;

                                    $arr_fieldValue = preg_split('/([^a-z0-9_]+)/i', $fieldValue);
                                    $seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $fieldValue)));
                                    
                                    $label = '';
                                    $n2 = 0;
                                    $lgt2 = count($arr_fieldValue);
                                    foreach($arr_fieldValue as $str_fieldValue){
                                        $value .= $row_table[$str_fieldValue];
                                        if(isset($seps[$n2]) && $n2+1 < $lgt2) $value .= $seps[$n2];
                                        $n2++;
                                    }
                                    if($j+1 < $nb_values) $value .= ', ';
                                }
                            }
                        }
                    }
                }else
                    die($table.': Table not found, check the file config.xml');
            }else{

                $arr_colname = preg_split('/([^a-z0-9_]+)/i', $colname);
                $lgt1 = count($arr_colname);
                
                if($lgt1 > 1){
                    $arr_seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $colname)));
                    $value = '';
                    $n1 = 0;
                    foreach($arr_colname as $str_colname){

                        $curr_value = $row[$str_colname];
                        
                        if(!is_null($curr_value) && $curr_value != '' && isset($arr_seps[$n1-1])) $value .= $arr_seps[$n1-1].$curr_value;
                        else $value .= $curr_value;
                        
                        $n1++;
                    }
                }else{
                    $value = $row[$colname];
                    if(!is_null($value)){
                        switch($col->type){
                            case 'date' :
                                $value = DateUtils::strftime(PMS_DATE_FORMAT, $value, true);
                            break;
                            case 'datetime' :
                                $value = DateUtils::strftime(PMS_DATE_FORMAT.' '.PMS_TIME_FORMAT, $value, true);
                            break;
                            case 'price' :
                                $value = StrUtils::formatPrice($value, PMS_DEFAULT_CURRENCY_SIGN);
                            break;
                            case 'case' :
                                $value = $col->getCaseValue($value);
                            break;
                            default :
                                $value = preg_replace('/\s\s+/', ' ', preg_replace('/([\n\r])/', ' ', $value));
                            break;
                        }
                        $value = StrUtils::strtrunc($value, 50);
                    }else
                        $value = '';
                }
            }
            if(!empty($col->link)) {
                $value = '<a href="' . str_replace('{id}', $row['id'], $col->link) . '"><b>' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '</b></a>';
            }
            $col->setValue($i, $value);
        }
        return $this->cols;
    }

    /**
     * Set the values of the filters defined in the module configuration file.
     *
     * @param array A collection of Filter objects.
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Get the filters defined in the module configuration file.
     *
     * @return array A collection of Filter objects.
     */
    public function getFilters()
    {
        $dom = $this->module->configDom;
        
        $root = $dom->getElementsByTagName('module')->item(0);
        $list = $root->getElementsByTagName('list')->item(0);
        $itemList = $list->getElementsByTagName('filter');

        $filters = array();
        
        foreach($itemList as $item){
            
            $label = Helpers::getTranslation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'), $this->adminContext->texts);
            $name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
            $type = htmlentities($item->getAttribute('type'), ENT_QUOTES, 'UTF-8');
            $options = array();
            $optFilters = array();
            $optionList = null;
            $optionTable = '';
            $order = '';
            
            $itemOptions = $item->getElementsByTagName('options')->item(0);
            if(!empty($itemOptions)) {
                $optionList = $itemOptions->getElementsByTagName('option');
                $optionTable = htmlentities($itemOptions->getAttribute('table'), ENT_QUOTES, 'UTF-8');
                $fieldLabel = htmlentities($itemOptions->getAttribute('fieldlabel'), ENT_QUOTES, 'UTF-8');
                $fieldValue = htmlentities($itemOptions->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
                $filterName = htmlentities($itemOptions->getAttribute('filtername'), ENT_QUOTES, 'UTF-8');
                $optFilter = htmlentities($itemOptions->getAttribute('optfilter'), ENT_QUOTES, 'UTF-8');

                if($optionTable != '' && $fieldLabel != '' && $fieldValue != ''){
                    if($optionList->length > 0){
                        foreach($optionList as $option)
                            $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = Helpers::getTranslation($option->nodeValue, $this->adminContext->texts);
                    }
                    $order = htmlentities($itemOptions->getAttribute('order'), ENT_QUOTES, 'UTF-8');
                    if($order != ''){
                        $order_select = ','.str_ireplace(' asc', '', $order);
                        $order_select = str_ireplace(' desc', '', $order_select);
                    }else $order_select = '';
                    
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
                    
                    if($order != '') $query_option .= ' ORDER BY '.$order;

                    $result_option = $this->pms_db->query($query_option);
                    if($result_option !== false){
                        $optionLabel = '';
                        foreach($result_option as $row_option){
                            
                            $arr_fieldLabel = preg_split('/([^a-z0-9_]+)/i', $fieldLabel);
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
                        $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = Helpers::getTranslation($option->nodeValue, $this->adminContext->texts);
                }
            }
            $filters[$name] = new Filter($name, $label, $type, $options, $filterName, $optFilters);
        }
        return $filters;
    }

    /**
     * Display the filter fields in the listing.
     *
     * @return string HTML output for the filter fields.
     */
    public function getFilterInputs()
    {
        $html = '';
        foreach($this->filters as $filter) {
            
            $label = $filter->label;
            $name = $filter->name;
            $type = $filter->type;
            $options = $filter->options;
            $value = $filter->value;
            $optFilters = $filter->optFilters;
            $filterName = $filter->filterName;
            
            $str_filter = ($filterName != '') ? ' data-filter="'.$filterName.'"' : '';

            if($type == 'date') {
                
                $html .= '<div class="input-group input-group-sm">'."\n";
                $html .= '<span class="input-group-text"><i class="fas fa-calendar"></i></span>'."\n";
                $startDate = isset($value[0]) ? date('Y-m-d', $value[0]) : '';
                $html .= '<input type="text" name="' . $name . '_start" id="' . $name . '_start" class="form-control form-control-sm datepicker" placeholder="' . $this->adminContext->texts['FROM_DATE'] . '" value="' . $startDate . '">'."\n";
                $html .= '<span class="input-group-text"><i class="fas fa-angle-right"></i></span>'."\n";
                $endDate = isset($value[1]) ? date('Y-m-d', $value[1]) : '';
                $html .= '<input type="text" name="' . $name . '_end" id="' . $name . '_end" class="form-control form-control-sm datepicker" placeholder="' . $this->adminContext->texts['TO_DATE'] . '" value="' . $endDate . '">'."\n";
                $html .= '</div>'."\n";

            } else {
            
                $html .= '<select name="'.$name.'" id="'.$name.'" class="form-select form-select-sm"'.$str_filter.'>'."\n";
                $html .= '<option value="">- '.$label.' -</option>'."\n";
                foreach($options as $option){
                    $key = key($options);
                    $selected = ($value == $key) ? ' selected="selected"' : '';
                    $rel = (is_array($optFilters) && isset($optFilters[$key])) ? ' rel="'.$optFilters[$key].'"' : '';
                    $html .= '<option value="'.$key.'"'.$rel.' '.$selected.'>'.$options[$key].'</option>'."\n";
                    next($options);
                }
                $html .= '</select>'."\n";
            }
        }
        return $html;
    }

    /**
     * Get the list of searchable fields in the listing.
     *
     * @return array An array of column names.
     */
    public function getSearchFieldsList()
    {
        $list = array();
        foreach($this->cols as $col){
            if($col->table == '') $list[] = $col->name;
        }
        return $list;
    }

    /**
     * Get the value of the "order" attribute from the module configuration.
     *
     * @return string The value of the "order" attribute.
     */
    public function getOrder()
    {
        $dom = $this->module->configDom;

        $root = $dom->getElementsByTagName('module')->item(0);
        $list = $root->getElementsByTagName('list')->item(0);

        $order = $list->getAttribute('order');
        
        return $order;
    }

    /**
     * Retrieve the items of the module based on filters and pagination parameters.
     *
     * @param array $filters Filters to apply.
     * @param string $q_search The search query.
     * @param string $order The column to sort by.
     * @param string $sort The sort order (asc, desc).
     * @param int $limit The number of items per page.
     * @param int $offset The pagination offset.
     *
     * @return array The list of items.
     */
    public function getItems($filters = [], $q_search = '', $order = 'id', $sort = 'asc', $limit = 50, $offset = 0)
    {
        $condition = $this->buildFilterCondition($filters);

        if(MULTILINGUAL) $condition .= ' AND lang = ' . PMS_DEFAULT_LANG;

        $colsNames = array_values(array_map(fn($col) => $col->name, $this->cols));

        $query = DbUtils::dbGetSearchRequest($this->pms_db, $this->tableName, $colsNames, $q_search, $limit, $offset, $condition, '', $order, $sort);
        
        $stmt = $this->pms_db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Build the SQL condition based on the applied filters.
     *
     * @param array $filters The applied filters.
     *
     * @return string The SQL condition.
     */
    protected function buildFilterCondition($filters)
    {
        $condition = '';
        foreach ($filters as $filter) {
            $fieldName = $filter->name;
            $fieldValue = $filter->value;
            if ($fieldValue != '') {
                if (is_array($fieldValue)) {
                    $start = $fieldValue[0];
                    $end = $fieldValue[1];
                    if(!empty($start) && !empty($end)) {
                        $condition .= " AND {$fieldName} > " . $start;
                        $condition .= " AND {$fieldName} < " . $end;
                    }
                } else {
                    $condition .= " AND {$fieldName} = " . $this->pms_db->quote($fieldValue);
                }
            }
        }
        return $condition;
    }

    /**
     * Get the total number of items for pagination.
     *
     * @param array $filters Filters to apply.
     *
     * @return int The total number of items.
     */
    public function getTotalItems($filters = [])
    {
        $condition = $this->buildFilterCondition($filters);
        $query = "SELECT COUNT(id) as total FROM {$this->tableName} WHERE 1 {$condition}";
        $stmt = $this->pms_db->query($query);
        return $stmt->fetchColumn();
    }

    /**
     * Get the paths of image files associated with an item.
     *
     * @param int $itemId The ID of the item.
     *
     * @return array An array with preview and zoom paths.
     */
    public function getImagePaths($itemId)
    {
        $paths = [
            'preview' => '',
            'zoom' => ''
        ];

        $query = 'SELECT * FROM ' . $this->tableName . '_file WHERE `type` = \'image\' AND id_item = :id_item AND file != \'\' ORDER BY `rank` LIMIT 1';
        $stmt = $this->pms_db->prepare($query);
        $stmt->bindParam(':id_item', $itemId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $filename_img = $row['file'];
            $id_img_file = $row['id'];

            $big_path = 'medias/' . MODULE . '/big/' . $id_img_file . '/' . $filename_img;
            $medium_path = 'medias/' . MODULE . '/medium/' . $id_img_file . '/' . $filename_img;
            $small_path = 'medias/' . MODULE . '/small/' . $id_img_file . '/' . $filename_img;
        
            if (RESIZING == 0 && is_file(SYSBASE . 'public/' . $big_path)) {
                $paths['preview'] = '../' . $big_path;
            } elseif (RESIZING == 1 && is_file(SYSBASE . 'public/' . $medium_path)) {
                $paths['preview'] = '../' . $medium_path;
            } elseif (is_file(SYSBASE . 'public/' . $small_path)) {
                $paths['preview'] = '../' . $small_path;
            } elseif (is_file(SYSBASE . 'public/' . $medium_path)) {
                $paths['preview'] = '../' . $medium_path;
            } elseif (is_file(SYSBASE . 'public/' . $big_path)) {
                $paths['preview'] = '../' . $big_path;
            }

            if (is_file(SYSBASE . 'public/' . $big_path)) {
                $paths['zoom'] = '../' . $big_path;
            } elseif (is_file(SYSBASE . 'public/' . $medium_path)) {
                $paths['zoom'] = '../' . $medium_path;
            } elseif (is_file(SYSBASE . 'public/' . $small_path)) {
                $paths['zoom'] = '../' . $small_path;
            }
        }
        return $paths;
    }

    /**
     * Delete an item and its associated files.
     *
     * @param int $id The ID of the item to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteItem($id)
    {
        if(NB_FILES > 0) $this->deleteFiles($id);
        $stmt = $this->pms_db->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete the files associated with an item.
     *
     * @param int $id The ID of the item.
     *
     */
    public function deleteFiles($id)
    {
        $stmt = $this->pms_db->prepare('SELECT file, id FROM solutionsCMS_' . MODULE . '_file WHERE id_item = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $files = $stmt->fetchAll();
            foreach ($files as $row) {
                $this->moduleModel->deleteFile($row['id'], false);
            }
        }
    }

    /**
     * Set an item as the main item (column 'main').
     *
     * @param int $id The ID of the item.
     *
     * @return bool True if the operation was successful, false otherwise.
     */
    public function defineMain($id)
    {
        if(MODULE == 'lang') $this->moduleModel->completeLang($id);
        $this->pms_db->query("UPDATE {$this->tableName} SET `main` = 0");
        return $this->pms_db->query("UPDATE {$this->tableName} SET `main` = 1 WHERE id = $id");
    }
}