<?php

header('Content-Type: application/json');

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

try {
    $rawData = file_get_contents('php://input');

    $data = json_decode($rawData, true);

    if (!isset($data['filePath'])) 
        throw new Exception('filePath key is missing.');

    $filePath = $data['filePath'];
    $fullPath = realpath(SYSBASE . 'public/' . ltrim($filePath, DOCBASE));

    if (!$fullPath || strpos($fullPath, realpath(SYSBASE . 'public/medias/uploads')) !== 0)
        throw new Exception('Invalid file path.');
    
    if (file_exists($fullPath) && unlink($fullPath)) {
        // Now remove the parent folder if it's empty
        $parentFolder = dirname($fullPath);
        if (is_dir($parentFolder) && count(scandir($parentFolder)) == 2)
            rmdir($parentFolder);
        
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('File deletion failed.');
    }
} catch (Exception $e) {
    echo json_encode(['error' => ['message' => $e->getMessage()]]);
}