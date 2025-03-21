<?php

header('Content-Type: application/json');

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

// Web path to the directory where the media files will be uploaded
$upload_path = '/medias/uploads/';

// Filesystem path to the public directory of the application
$public_path = __DIR__ . '/../../public/';

try {
    $rawData = file_get_contents('php://input');

    $data = json_decode($rawData, true);

    if (!isset($data['filePath'])) 
        throw new Exception('filePath key is missing.');

    $public_path = rtrim($public_path, '/');

    $filePath = $data['filePath'];
    $fullPath = realpath($public_path . '/' . $filePath);

    if (!$fullPath || strpos($fullPath, realpath($public_path . $upload_path)) !== 0)
        throw new Exception('Invalid file path.');
    
    if (file_exists($fullPath) && unlink($fullPath)) {
        $webpFile = str_replace(pathinfo($fullPath, PATHINFO_EXTENSION), 'webp', $fullPath);
        if(file_exists($webpFile)) unlink($webpFile);
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