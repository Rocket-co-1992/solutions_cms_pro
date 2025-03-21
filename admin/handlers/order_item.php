<?php

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

if(isset($_GET['table'])){

    $table = $_GET['table'];
    $offset = (is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;
    
    $res = $_POST['listing_base'];
    for($i = 1; $i <= count($res); $i++){
        $id = str_replace('item_', '', $res[$i - 1]);
        
        if(is_numeric($id)) {
            $this->pms_db->query('UPDATE solutionsCMS_'.$table.' SET `rank` = '.(($i-1)+$offset).' WHERE id = '.$id);
        }
    }
}