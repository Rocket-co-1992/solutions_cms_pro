<?php

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

if(isset($_GET['table']) && isset($_GET['list']) && isset($_GET['prefix'])){
    
    $table = $_GET['table'];
    
    $res = explode('|', $_GET['list']);
    
    for($i = 1; $i <= count($res); $i++){
        $id = str_replace($_GET['prefix'].'_', '', $res[$i-1]);
        
        if(is_numeric($id))
            $this->pms_db->query('UPDATE solutionsCMS_'.$table.' SET `rank` = '.$i.' WHERE id = '.$id);
    }
}
