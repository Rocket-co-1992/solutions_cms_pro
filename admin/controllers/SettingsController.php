<?php

namespace Pandao\Admin\Controllers;

use Pandao\Admin\Models\SettingsModel;
use Pandao\Common\Services\Csrf;
use Pandao\Common\Utils\StrUtils;

class SettingsController extends Controller
{
    protected $settingsModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->settingsModel = new SettingsModel($db);
    }

    /**
     * Display the settings page.
     *
     */
    public function index()
    {
        $this->viewData['user_data'] = $_SESSION['user'];
        $this->viewData['config_tmp'] = $this->settingsModel->getConfig();
        $this->viewData['csrf_token'] = Csrf::generateToken();

        $this->assets['assets_css'][] = 'assets/js/plugins/validate-password/css/jquery.validate-password.css';
        $this->assets['assets_js'][] = 'assets/js/plugins/validate-password/js/jquery.validate-password.min.js';

        if(isset($_POST['edit_settings'])) $this->saveSettings();

        $this->render('settings', 'system', $this->viewData);
    }

    /**
     * Save the general settings.
     * 
     * This method handles saving configuration settings.
     *
     */
    public function saveSettings()
    {
        if (defined('PMS_DEMO') && PMS_DEMO == 1) {
            $_SESSION['msg_error'][] = 'This action is disabled in the demo mode';
            return;
        }

        $field_notice = [];
        $config_tmp = [];
        $userId = $_SESSION['user']['id'];

        if (Csrf::verifyToken('post')) {

            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
            $user = htmlspecialchars($_POST['user'], ENT_QUOTES, 'UTF-8');
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if ($_SESSION['user']['type'] == 'administrator') {

                // Getting the posted config values
                $config_tmp = $this->collectConfigData();

                $field_notice = array_merge($field_notice, $this->validateAdminFields($config_tmp));

                // Verification of the admin folder
                $curr_dirname = dirname(__FILE__);
                $curr_folder = basename($curr_dirname);
                $folder_exists = $this->checkAdminFolderExists($config_tmp['pms_admin_folder'], $curr_folder);
                if ($folder_exists) {
                    $field_notice['admin_folder'] = $this->adminContext->texts['FOLDER_EXISTS'];
                }
            }

            // Validation of the user fields
            if ($user == '') $field_notice['user'] = $this->adminContext->texts['REQUIRED_FIELD'];
            if ($password != '' && mb_strlen($password, 'UTF-8') < 6) $field_notice['password'] = $this->adminContext->texts['PASSWORD_TOO_SHORT'];
            elseif ($password != $password2) $field_notice['password'] = $this->adminContext->texts['PASSWORD_DONT_MATCH'];

            if ($email == '' || !preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i', $email)) $field_notice['email'] = $this->adminContext->texts['INVALID_EMAIL'];

            // Checks if the user exists
            if ($this->pms_db !== false && $this->settingsModel->userExists($user, $userId)) {
                $field_notice['user'] = $this->adminContext->texts['USER_EXISTS'];
            }

            if (count($field_notice) == 0) {

                // Test of the database connection with the new settings
                if ($_SESSION['user']['type'] == 'administrator') {
                    $db_test = $this->settingsModel->testDbConnection($config_tmp);
                    if ($db_test === false) {
                        $_SESSION['msg_error'][] = $this->adminContext->texts['DATABASE_ERROR'];
                    } else {
                        // Removing of the previous error messages
                        if (is_array($_SESSION['msg_error'])) {
                            $key = array_search($this->adminContext->texts['DATABASE_ERROR'], $_SESSION['msg_error']);
                            if ($key !== false) unset($_SESSION['msg_error'][$key]);
                        }

                        // Rename admin folder if needed
                        $renamed = $this->settingsModel->renameAdminFolder($config_tmp, $curr_dirname, $curr_folder);

                        // Update the configuration file
                        $config_updated = $this->settingsModel->updateConfigFile($config_tmp, $renamed);
                        if ($config_updated) {
                            $_SESSION['msg_success'][] = $this->adminContext->texts['CONFIG_SAVED'];
                        } else {
                            $_SESSION['msg_notice'][] = $this->adminContext->texts['CONFIG_NOTICE'];
                        }
                    }
                }

                // Update the user information
                $data = [
                    'id' => $userId,
                    'login' => $user,
                    'email' => $email
                ];
                if ($password != '') {
                    $data['pass'] = password_hash($password, PASSWORD_DEFAULT);
                }

                if ($this->pms_db !== false && $this->settingsModel->updateUser($data)) {
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['login'] = $user;
                    $_SESSION['msg_success'][] = $this->adminContext->texts['PROFILE_SUCCESS'];
                }

                // Redirection
                if (isset($renamed) && $renamed) {
                    header('Location: ' . DOCBASE . $config_tmp['pms_admin_folder'].'/module=settings');
                } else {
                    header('Location: module=settings');
                }
                exit;
            } else {
                $_SESSION['msg_error'][] = $this->adminContext->texts['FORM_ERRORS'];
            }
        } else {
            $_SESSION['msg_error'][] = $this->adminContext->texts['BAD_TOKEN1'];
        }
    }

    /**
     * Collect configuration parameters from the form.
     *
     * @return array The configuration data collected from the form.
     */
    private function collectConfigData()
    {
        $config_tmp = [];

        $config_tmp['pms_site_title'] = htmlspecialchars($_POST['site_title'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_time_zone'] = htmlspecialchars($_POST['time_zone'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_date_format'] = htmlspecialchars($_POST['date_format'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_time_format'] = htmlspecialchars($_POST['time_format'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_currency_enabled'] = isset($_POST['currency_enabled']) ? htmlspecialchars($_POST['currency_enabled'], ENT_QUOTES, 'UTF-8') : '';
        $config_tmp['pms_currency_pos'] = isset($_POST['currency_pos']) ? htmlspecialchars($_POST['currency_pos'], ENT_QUOTES, 'UTF-8') : '';
        $config_tmp['pms_lang_enabled'] = isset($_POST['lang_enabled']) ? htmlspecialchars($_POST['lang_enabled'], ENT_QUOTES, 'UTF-8') : '';
        $config_tmp['pms_admin_lang_file'] = htmlspecialchars($_POST['admin_lang_file'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_template'] = htmlspecialchars($_POST['template'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_owner'] = htmlspecialchars($_POST['owner'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_address'] = addslashes(preg_replace('/([\n\r])/', '', nl2br(StrUtils::ripTags($_POST['address']))));
        $config_tmp['pms_phone'] = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_mobile'] = htmlspecialchars($_POST['mobile'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_email'] = htmlspecialchars($_POST['email2'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_db_name'] = htmlspecialchars($_POST['db_name'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_db_host'] = htmlspecialchars($_POST['db_host'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_db_port'] = htmlspecialchars($_POST['db_port'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_db_user'] = htmlspecialchars($_POST['db_user'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_db_pass'] = $_POST['db_pass'];
        $config_tmp['pms_sender_email'] = htmlspecialchars($_POST['sender_email'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_sender_name'] = htmlspecialchars($_POST['sender_name'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_use_smtp'] = isset($_POST['use_smtp']) ? htmlspecialchars($_POST['use_smtp'], ENT_QUOTES, 'UTF-8') : 0;
        $config_tmp['pms_smtp_security'] = htmlspecialchars($_POST['smtp_security'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_smtp_auth'] = isset($_POST['smtp_auth']) ? htmlspecialchars($_POST['smtp_auth'], ENT_QUOTES, 'UTF-8') : 0;
        $config_tmp['pms_smtp_host'] = htmlspecialchars($_POST['smtp_host'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_smtp_user'] = htmlspecialchars($_POST['smtp_user'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_smtp_pass'] = $_POST['smtp_pass'];
        $config_tmp['pms_smtp_port'] = htmlspecialchars($_POST['smtp_port'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_enable_cookies_notice'] = isset($_POST['enable_cookies_notice']) ? htmlspecialchars($_POST['enable_cookies_notice'], ENT_QUOTES, 'UTF-8') : '';
        $config_tmp['pms_maintenance_mode'] = isset($_POST['maintenance_mode']) ? htmlspecialchars($_POST['maintenance_mode'], ENT_QUOTES, 'UTF-8') : '';
        $config_tmp['pms_maintenance_msg'] = addslashes(preg_replace('/([\n\r])/', '', $_POST['maintenance_msg']));
        $config_tmp['pms_gmaps_api_key'] = htmlspecialchars($_POST['gmaps_api_key'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_analytics_code'] = addslashes(preg_replace('/([\n\r])/', '', $_POST['analytics_code']));
        $config_tmp['pms_admin_folder'] = htmlspecialchars($_POST['admin_folder'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_captcha_pkey'] = htmlspecialchars($_POST['captcha_pkey'], ENT_QUOTES, 'UTF-8');
        $config_tmp['pms_captcha_skey'] = htmlspecialchars($_POST['captcha_skey'], ENT_QUOTES, 'UTF-8');

        return $config_tmp;
    }

    /**
     * Validate administrator fields.
     *
     * @param array $config_tmp Temporary configuration data to validate.
     *
     * @return bool True if the fields are valid, false otherwise.
     */
    private function validateAdminFields($config_tmp)
    {
        $field_notice = [];

        if($config_tmp['pms_time_zone'] == '') $field_notice['time_zone'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if(!is_numeric($config_tmp['pms_lang_enabled'])) $field_notice['lang_enabled'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if(!is_numeric($config_tmp['pms_currency_enabled'])) $field_notice['currency_enabled'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if(!is_numeric($config_tmp['pms_enable_cookies_notice'])) $field_notice['enable_cookies_notice'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if(!is_numeric($config_tmp['pms_maintenance_mode'])) $field_notice['maintenance_mode'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_template'] == '') $field_notice['template'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_admin_lang_file'] == '' || !is_file('includes/langs/'.$config_tmp['pms_admin_lang_file'])) $field_notice['admin_lang_file'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_db_name'] == '') $field_notice['db_name'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_db_host'] == '') $field_notice['db_host'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_db_port'] == '') $field_notice['db_port'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_db_user'] == '') $field_notice['db_user'] = $this->adminContext->texts['REQUIRED_FIELD'];
        if($config_tmp['pms_db_pass'] == '') $field_notice['db_pass'] = $this->adminContext->texts['REQUIRED_FIELD'];
        
        if($config_tmp['pms_admin_folder'] != '' && preg_match('/(^[a-z0-9]+$)/i', $config_tmp['pms_admin_folder']) !== 1) $field_notice['admin_folder'] = $this->adminContext->texts['ALPHANUM_ONLY'];

        return $field_notice;
    }

    /**
     * Check if the admin folder already exists.
     *
     * @param string $new_folder The new folder name to check.
     * @param string $current_folder The current folder name.
     *
     * @return bool True if the folder exists, false otherwise.
     */
    private function checkAdminFolderExists($new_folder, $current_folder)
    {
        $rep = opendir(SYSBASE);
        while($entry = @readdir($rep)){
            if($entry != '.' && $entry != '..' && is_dir(SYSBASE . $entry) && $entry != $current_folder){
                if($entry == $new_folder){
                    closedir($rep);
                    return true;
                }
            }
        }
        closedir($rep);
        return false;
    }
}
