<?php

use Pandao\Common\Utils\StrUtils;

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

if(isset($_POST['uniqid']) && isset($_POST['timestamp']) && isset($_POST['dir']) && isset($_POST['lang']) && isset($_POST['exts'])){

    $verifyToken = hash('sha256', 'sessid_' . $_POST['uniqid'].$_POST['timestamp']);

    if(!empty($_FILES) && $_POST['token'] == $verifyToken){
        
        $dir = $_POST['dir'];
        $path = 'public/medias/' . $dir . '/tmp';
        $lang = $_POST['lang'];
        
        $uniqid = uniqid();
        
        // upload folder for a session
        if(!is_dir(SYSBASE . $path . '/' . $verifyToken)) mkdir(SYSBASE . $path . '/' . $verifyToken, 0777);
        chmod(SYSBASE . $path . '/' . $verifyToken, 0777);
        if(!is_dir(SYSBASE . $path . '/' . $verifyToken . '/' . $lang)) mkdir(SYSBASE . $path . '/' . $verifyToken . '/' . $lang, 0777);
        chmod(SYSBASE . $path . '/' . $verifyToken . '/' . $lang, 0777);
        if(!is_dir(SYSBASE . $path . '/' . $verifyToken . '/' . $lang . '/' . $uniqid)) mkdir(SYSBASE . $path . '/' . $verifyToken . '/' . $lang . '/' . $uniqid, 0777);
        chmod(SYSBASE . $path . '/' . $verifyToken . '/' . $lang . '/' . $uniqid, 0777);

        $tempFile = $_FILES['Filedata']['tmp_name'];

        $ext = pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION);
        $filename = StrUtils::textFormat(pathinfo($_FILES['Filedata']['name'], PATHINFO_FILENAME)) . '.' . $ext;
            
        $targetFile = $path . '/' . $verifyToken . '/' . $lang . '/' . $uniqid . '/' . $filename;
        
        // file type checking
        $fileTypes = unserialize(stripslashes($_POST['exts']));
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        
        if(in_array(mb_strtolower($fileParts['extension'], 'UTF-8'), $fileTypes)){
            
            move_uploaded_file($tempFile, SYSBASE . $targetFile);
            
            $dim = @getimagesize(SYSBASE . $targetFile);
            if(is_array($dim)){
                $w = $dim[0];
                $h = $dim[1];
            }else{
                $w = 0;
                $h = 0;
            }
            
            $bytes = floatval(filesize(SYSBASE . $targetFile));
            
            $arBytes = array(
                0 => array(
                    'UNIT' => 'To',
                    'VALUE' => pow(1024, 4)
                ),
                1 => array(
                    'UNIT' => 'Go',
                    'VALUE' => pow(1024, 3)
                ),
                2 => array(
                    'UNIT' => 'Mo',
                    'VALUE' => pow(1024, 2)
                ),
                3 => array(
                    'UNIT' => 'Ko',
                    'VALUE' => 1024
                ),
                4 => array(
                    'UNIT' => 'octets',
                    'VALUE' => 1
                ),
            );
            $result = '';
            foreach($arBytes as $arItem){
                if($bytes >= $arItem['VALUE']){
                    $result = $bytes / $arItem['VALUE'];
                    $result = str_replace('.', ',' , strval(round($result, 2))) . ' ' . $arItem['UNIT'];
                    break;
                }
            }
            
            echo DOCBASE . $targetFile . '|' . $result . '|' . $w . '|' . $h;
        }
    }
}
