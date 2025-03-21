<?php

use Pandao\Common\Utils\StrUtils;
use Pandao\Common\Utils\FileUtils;

if (defined('PMS_DEMO') && PMS_DEMO == 1) exit;

header('Content-Type: application/json');

try {
    $receivedToken = $_POST['token'];
    $expectedToken = hash('sha256', 'sessid_'.($_POST['uniqid'] ?? '').($_POST['timestamp'] ?? ''));

    if (!empty($_FILES) && $receivedToken === $expectedToken) {
        $path = 'medias/uploads/'.uniqid();

        if (!mkdir(SYSBASE . 'public/' .$path, 0777, true) && !is_dir(SYSBASE . 'public/' . $path))
            throw new Exception('Failed to create directories...');
        chmod(SYSBASE . 'public/' .$path, 0777);

        $tempFile = $_FILES['upload']['tmp_name'];
        $ext = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
        $filename = StrUtils::textFormat(pathinfo($_FILES['upload']['name'], PATHINFO_FILENAME)) . '.' . $ext;
        $targetFile = $path . '/' . $filename;

        if (move_uploaded_file($tempFile, SYSBASE . 'public/' .$targetFile)) {
            //list($width, $height) = getimagesize(SYSBASE . 'public/' .$targetFile);
            //if ($width > 1320 || $height > 880)
            if (!FileUtils::imgResize(SYSBASE . 'public/' .$targetFile, SYSBASE . 'public/' . $path, 1320, 880)) {
                throw new Exception('Error resizing image.');
            }else{
                $webp_path = preg_replace('/\.\w+$/', '.webp', $targetFile);
                echo json_encode(['url' => DOCBASE . $webp_path]);
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