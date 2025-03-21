<?php

header('Content-Type: application/json');

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

require __DIR__ . '/pflow_UploaderHelper.php';

use PflowUploader\UploaderHelper;

// Web path to the directory where the media files will be uploaded
$upload_path = '/medias/uploads/';

// Filesystem path to the public directory of the application
$public_path = __DIR__ . '/../../public/';

try {
    $receivedToken = $_POST['token'];
    $expectedToken = hash('sha256', 'sessid_' . ($_POST['uniqid'] ?? '') . ($_POST['timestamp'] ?? ''));

    if (!empty($_FILES) && $receivedToken === $expectedToken) {

        $front_path = rtrim($upload_path, '/') . '/' . uniqid();
        $abs_path = rtrim($public_path, '/') . $front_path;

        if (!mkdir($abs_path, 0777, true) && !is_dir($abs_path))
            throw new Exception('Failed to create directories...');
        chmod($abs_path, 0777);

        $tempFile = $_FILES['upload']['tmp_name'];
        $ext = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
        $filename = UploaderHelper::textFormat(pathinfo($_FILES['upload']['name'], PATHINFO_FILENAME)) . '.' . $ext;
        $targetFile = $abs_path . '/' . $filename;

        if (move_uploaded_file($tempFile, $targetFile)) {
            if (!UploaderHelper::imgResize($targetFile, $abs_path, 1320, 880)) {
                throw new Exception('Error resizing image.');
            }else{
                $webp_path = preg_replace('/\.\w+$/', '.webp', $front_path . '/' . $filename);
                echo json_encode(['url' => $webp_path]);
            }
        } else {
            throw new Exception('Error uploading file.');
        }
    } else {
        throw new Exception('Invalid token.');
    }
} catch (Exception $e) {
    echo json_encode(['error' => ['message' => $e->getMessage()]]);
}