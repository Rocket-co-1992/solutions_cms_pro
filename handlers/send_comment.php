<?php

use Pandao\Common\Utils\DbUtils;
use Pandao\Common\Utils\MailUtils;

header('Content-Type: application/json');

$response = ['html' => '', 'notices' => [], 'error' => '', 'success' => ''];

if(PMS_CAPTCHA_PKEY != '' && PMS_CAPTCHA_SKEY != ''){
    $secret = PMS_CAPTCHA_SKEY;
    $gresponse = $_POST['g-recaptcha-response'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_response = file_get_contents($recaptcha_url . '?secret=' . $secret . '&response=' . $gresponse . '&remoteip=' . $remoteip);
    $recaptcha_data = json_decode($recaptcha_response);

    if(!$recaptcha_data->success || $recaptcha_data->score < 0.6) {
        $response['notices']['captcha'] = $siteContext->texts['INVALID_RECAPTCHA'];
    }
}

$name = html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8');
$email = html_entity_decode($_POST['email'], ENT_QUOTES, 'UTF-8');
$msg = html_entity_decode($_POST['msg'], ENT_QUOTES, 'UTF-8');
$item_id = $_POST['item_id'];
$item_type = $_POST['item_type'];

$rating = (isset($_POST['rating'])) ? $_POST['rating'] : false;

if($name == '') $response['notices']['name'] = $siteContext->texts['REQUIRED_FIELD'];
if($msg == '') $response['notices']['msg'] = $siteContext->texts['REQUIRED_FIELD'];
if($rating !== false && (!is_numeric($rating) || $rating < 0 || $rating > 5)) $rating = null;

if($email == '' || !preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i', $email)) $response['notices']['email'] = $siteContext->texts['INVALID_EMAIL'];

if(!isset($_COOKIE['COMMENT_' . $item_type . '_' . $item_id])){
    $result_rating = $this->pms_db->query('SELECT * FROM solutionsCMS_comment WHERE item_type = ' . $this->pms_db->quote($item_type) . ' AND id_item = ' . $this->pms_db->quote($item_id) . ' AND rating > 0 AND rating <= 5 AND (UPPER(email) = ' . $this->pms_db->quote(mb_strtoupper($email, 'UTF-8')) . ' OR ip = ' . $this->pms_db->quote($_SERVER['REMOTE_ADDR']) . ')');
    if($result_rating === false || $this->pms_db->last_row_count() > 0)
        $rating = null;
}else
    $rating = null;
    
if(is_numeric($item_id) && count($response['notices']) == 0){
    
    $data = array();
    $data['id_item'] = $item_id;
    $data['item_type'] = $item_type;
    $data['name'] = $name;
    $data['email'] = $email;
    $data['msg'] = $msg;
    $data['checked'] = 0;
    $data['add_date'] = time();
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    
    if($rating !== false) $data['rating'] = $rating;
    
    $result_insert = DbUtils::dBprepareInsert($this->pms_db, 'solutionsCMS_comment', $data);
    
    if($result_insert->execute() !== false){
        if($rating !== false && $rating > 0 && $rating <= 5) setcookie('COMMENT_' . $item_type . '_' . $item_id, 1, time() + 2592000);

        $response['success'] .= $siteContext->texts['COMMENT_SUCCESS'];

        $mailContent = 'Name: ' . $name . '<br>' . "\n\n";
        $mailContent .= 'E-mail: ' . $email . '<br>' . "\n\n";
        if($rating > 0) $mailContent .= 'Rating: ' . $rating . '/5<br>' . "\n\n";
        $mailContent .= '<b>Message:</b><br>' . nl2br($msg)."\n\n";
        
        if(!MailUtils::sendMail(PMS_EMAIL, PMS_OWNER, 'New comment', $mailContent, $email, $name))
            $response['error'] .= $siteContext->texts['MAIL_DELIVERY_FAILURE'];
    }
}else
    $response['error'] .= $siteContext->texts['FORM_ERRORS'];

echo json_encode($response);