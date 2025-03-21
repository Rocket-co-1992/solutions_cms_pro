<?php

if(isset($_POST['dir']) && isset($_POST['token']) && $_POST['token'] != ""){

    $dirname = __DIR__ . "/../../public/medias/" . $_POST['dir'] . "/tmp/" . $_POST['token'];
    
    if(is_dir($dirname)){
        
        if(!is_writable($dirname))
            throw new Exception("You do not have renaming permissions!");

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirname),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        while($iterator->valid()){
            if(!$iterator->isDot()){
                if($iterator->isLink() && false === (boolean) $followLinks) $iterator->next();
                if($iterator->isFile()) unlink($iterator->getPathname());
                else if ($iterator->isDir()) rmdir($iterator->getPathname());
            }
            $iterator->next();
        }
        rmdir($dirname);
        unset($iterator);
    }
}
