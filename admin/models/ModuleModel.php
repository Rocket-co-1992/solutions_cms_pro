<?php

namespace Pandao\Admin\Models;

use Pandao\Common\Utils\DbUtils;

class ModuleModel
{
    protected $pms_db;
    protected $adminContext;
    protected $module;

    public $tableName;
    public $languages;

    /**
     * ModuleModel constructor. Initializes the ModuleModel with the database connection and module information.
     *
     * @param Database $db Database connection.
     * @param Module $module The current module.
     */
    public function __construct($db, $module)
    {
        $this->pms_db = $db;
        $this->module = $module;
        $this->tableName = 'solutionsCMS_' . $module->name;
        $this->adminContext = AdminContext::get();
        $this->languages = MULTILINGUAL ? $this->adminContext->languages : [['id' => 0, 'title' => '', 'image' => '']];
    }
    
    /**
     * Update the status of an item.
     *
     * @param string $tableName The name of the table.
     * @param int $id The item ID.
     * @param int $status The new status value.
     *
     * @return bool True on success.
     */
    public function updateStatus($tableName, $id, $status)
    {
        $stmt = $this->pms_db->prepare("UPDATE {$tableName} SET checked = :status WHERE id = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Manage home page display (column 'home').
     *
     * @param string $tableName The name of the table.
     * @param int $id The item ID.
     * @param int $value The value to set for 'home' (0 or 1).
     *
     * @return bool True on success.
     */
    public function displayHome($tableName, $id, $value)
    {
        $stmt = $this->pms_db->prepare("UPDATE {$tableName} SET `home` = :value WHERE id = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':value', $value, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Delete a media file from the database and the server.
     *
     * @param int $id_file The ID of the media file.
     * @param bool $redirect Whether to redirect after deletion.
     *
     */
    public function deleteFile($id_file, $redirect = true)
    {
        $result = $this->pms_db->query('SELECT * FROM ' . $this->tableName . '_file WHERE id = '.$id_file);
        if($result !== false && $this->pms_db->last_row_count() > 0){
            
            $row = $result->fetch();
            
            $filename = $row['file'];
            $id_item = $row['id_item'];
            $type_item = $row['type'];
            
            if($type_item == 'other'){
        
                if(is_file(SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file . '/' . $filename))
                    unlink(SYSBASE . 'public/medias/' . MODULE . '/other/'.$id_file . '/' . $filename);
                
            }elseif ($type_item == 'image') {

                $dirs = ['big', 'medium', 'small'];
                
                foreach ($dirs as $dir) {
                    if (is_file(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file . '/' . $filename)) {
                        unlink(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file . '/' . $filename);
                    }
                    $webpFile = str_replace(pathinfo($filename, PATHINFO_EXTENSION), 'webp', $filename);
                    if (is_file(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file . '/' . $webpFile)) {
                        unlink(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file . '/' . $webpFile);
                    }
                    if (is_dir(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file)) {
                        rmdir(SYSBASE . 'public/medias/' . MODULE . '/' . $dir . '/' . $id_file);
                    }
                }
            }
            
            $this->updateRank('solutionsCMS_' . MODULE . '_file', $id_file, $id_item);
                
            if($this->pms_db->query('DELETE FROM ' . $this->tableName . '_file WHERE id = '.$id_file) !== false)
                if($redirect) $_SESSION['msg_success'][] = $filename.' - '.$this->adminContext->texts['DELETE_SUCCESS'];
            else
                if($redirect) $_SESSION['msg_error'][] = $filename.' - '.$this->adminContext->texts['UPDATE_ERROR'];
        }
    }

    /**
     * Update the rank of items in the table after deletion.
     *
     * @param string $table The name of the table.
     * @param int $id The ID of the item.
     * @param int $id_item The ID of the related item (optional).
     *
     */
    public function updateRank($table, $id, $id_item = 0)
    {
        $result = $this->pms_db->query('SELECT `rank` FROM '.$table.' WHERE id = '.$id);
        if($result !== false && $this->pms_db->last_row_count() > 0){
            
            $rank = $result->fetchColumn(0);
            if(!is_null($rank)){
                $query = 'SELECT id, `rank` FROM '.$table.' WHERE `rank` > '.$rank;
                if($id_item > 0) $query .= ' AND id_item = '.$id_item;
                $result = $this->pms_db->query($query);

                foreach($result as $row){
                    
                    $old_rank = $row['rank'];
                    $id_curr = $row['id'];
                    $new_rank = $old_rank-1;
                    $this->pms_db->query('UPDATE '.$table.' SET `rank` = '.$new_rank.' WHERE id = '.$id_curr);
                }
            }
        }
    }

    /**
     * Complete missing fields for a specific language in a module.
     *
     * @param string $module The name of the module.
     * @param int $id_lang The language ID.
     * @param bool $loop Internal loop flag (used for recursive calls).
     *
     * @return bool True on success.
     */
    public function completeLangModule($module, $id_lang, $loop = false)
    {
        $error = false;
        $title = '';
        
        if(DbUtils::dbTableExists($this->pms_db, $module)){
            
            if(DbUtils::dbColumnExists($this->pms_db, $module, 'lang')){
                        
                $title = DbUtils::dbGetFieldValue($this->pms_db, 'solutionsCMS_lang', 'title', $id_lang);
                
                $result_default = $this->pms_db->query('SELECT * FROM ' . $module . ' WHERE lang = '.PMS_DEFAULT_LANG.' ORDER BY id');
                $result_origin = $this->pms_db->query('SELECT * FROM ' . $module . ' WHERE lang = '.$id_lang.' ORDER BY id');
                
                if($result_default !== false && $result_origin !== false){
                    
                    $rows_default = $result_default->fetchAll();
                    
                    foreach($rows_default as $row_default){
                        
                        $id = $row_default['id'];
                        
                        $result_exist = $this->pms_db->query('SELECT * FROM ' . $module . ' WHERE id = '.$id.' AND lang = '.$id_lang);
                        
                        if($result_exist !== false && $this->pms_db->last_row_count() == 1){
                        
                            $row_origin = $result_exist->fetch();
                            
                            $query = 'UPDATE ' . $module . ' SET ';
                            
                            $k = 0;
                            foreach($row_origin as $colname => $value){    
                                
                                $query .= '`'.$colname.'` = ';
                                
                                if($value == ''){
                                    $col_type = DbUtils::dbColumnType($this->pms_db, $module, $colname);
                                    $query .= (is_null($value) || (preg_match('/.*(char|text).*/i', $col_type) === false && $value == '')) ? 'NULL' : $this->pms_db->quote($row_default[$colname]);
                                }else
                                    $query .= $this->pms_db->quote($value);
                                    
                                if($k < count($row_origin)-1) $query .= ', ';
                                $k++;
                            }
                            $query .= ' WHERE lang = '.$id_lang.' AND id = '.$id;
                        
                        }else{
                            
                            $row_default['lang'] = $id_lang;
                            
                            $query = 'INSERT INTO ' . $module . ' VALUES(';
                            
                            $k = 0;
                            foreach($row_default as $colname => $value){            
                                
                                if($value == ''){
                                    $col_type = DbUtils::dbColumnType($this->pms_db, $module, $colname);
                                    $query .= (is_null($value) || (preg_match('/.*(char|text).*/i', $col_type) === false && $value == '')) ? 'NULL' : $this->pms_db->quote($value);
                                }else
                                    $query .= $this->pms_db->quote($value);
                                
                                if($k < count($row_default)-1) $query .= ', ';
                                $k++;
                            }
                            $query .= ')';
                        }
                        if($this->pms_db->query($query) === false) $error = true;
                    }
                }else $error = true;
            }
        }
        
        if($error !== true){
            if(!$loop) $_SESSION['msg_success'][] = $title.' - '.$this->adminContext->texts['TRANSLATE_SUCCESS'];
            if(substr($module, -5) != '_file') $this->completeLangModule($module . '_file', $id_lang, true);
            return true;
        }else{
            if(!$loop) $_SESSION['msg_error'][] = $title.' - '.$this->adminContext->texts['UPDATE_ERROR'];
            return false;
        }
    }

    /**
     * Complete missing fields for a specific language across all modules.
     *
     * @param int $id_lang The language ID.
     *
     */
    public function completeLang($id_lang)
    {
        $modules_list = $this->adminContext->getModules(PMS_ADMIN_FOLDER . '/modules');
        
        $error = false;
        
        $title = DbUtils::dbGetFieldValue($this->pms_db, 'solutionsCMS_lang', 'title', $id_lang);
        
        foreach($modules_list as $module){
            if($this->completeLangModule('solutionsCMS_'.$module->name, $id_lang, true) === false) $error = true;
        }
        if(!$error)
            $_SESSION['msg_success'][] = $title.' - '.$this->adminContext->texts['TRANSLATE_SUCCESS'];
        else
            $_SESSION['msg_error'][] = $title.' - '.$this->adminContext->texts['UPDATE_ERROR'];
    }
}
