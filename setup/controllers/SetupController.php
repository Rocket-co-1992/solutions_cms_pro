<?php

namespace Pandao\Setup\Controllers;

use Pandao\Common\Services\Csrf;
use Pandao\Admin\Controllers\Controller;
use Pandao\Common\Core\Database;
use Pandao\Common\Utils\DbUtils;

class SetupController extends Controller
{
    private $field_notice = [];
    private $config_tmp = [];
    private $db_success = false;
    private $installed = false;

    /**
     * Paths of the temporary installation files.
     * The directory "/setup/" and its content can be
     * removed or moved or renamed after the successful installation
     */
    private $db_sql_file = __DIR__ . "/../../setup/db.sql";
    private $tmp_config_file = __DIR__ . "/../../setup/config-tmp.php";
    private $tmp_htaccess_file = __DIR__ . "/../../setup/htaccess.txt";

    // Paths of the required configuration files.
    private $config_file = __DIR__ . "/../../config/config.php";
    private $htaccess_file = __DIR__ . "/../../public/.htaccess";
    private $user_data;

    /**
     * Constructor to initialize the database connection.
     *
     * @param Database $db The database connection instance.
     */
    public function __construct($db)
    {
        parent::__construct($db);
        $this->pms_db = $db;
    }

    /**
     * Entry point of the setup process. 
     * Loads default values, checks if the system is already installed, 
     * and handles the installation form submission.
     */
    public function index()
    {
        // Default values
        $this->config_tmp = [
            'pms_site_title' => "",
            'pms_db_name' => "",
            'pms_db_host' => "localhost",
            'pms_db_port' => "3306",
            'pms_db_user' => "",
            'pms_db_pass' => "",
            'pms_email' => ""
        ];
        $this->user_data = [
            'login' => "",
            'password' => ""
        ];

        if ($this->pms_db !== false && DbUtils::dbTableExists($this->pms_db, "solutionsCMS_%")) {
            $this->installed = true;
            $_SESSION['msg_notice'][] = "It seems that Solutions CMS is already installed.<br>Remove your former tables from your database to make a fresh install.";
        }
        if ($this->pms_db === false && file_exists($this->config_file)) {
            $_SESSION['msg_notice'][] = "It seems that a configuration file exists, but Solutions CMS is unable to connect to the database using settings in the file config/config.php. You can retry to make an installation here.";
        }

        // Handle installation form
        if (isset($_POST['install']) && !$this->installed) {
            $this->install();
        }

        $this->viewData = array_merge($this->viewData, [
            'installed' => $this->installed,
            'field_notice' => $this->field_notice,
            'config_tmp' => $this->config_tmp,
            'user_data' => $this->user_data,
            'csrf_token' => Csrf::generateToken()
        ]);

        $this->assets['assets_css'][] = 'assets/js/plugins/validate-password/css/jquery.validate-password.css';
        $this->assets['assets_js'][] = 'assets/js/plugins/validate-password/js/jquery.validate-password.min.js';

        $this->render('setup', 'system', $this->viewData);
    }

    /**
     * Processes the installation by validating form input, 
     * setting up the database connection, and finalizing the installation.
     */
    private function install()
    {
        // Get setup post data from the form
        $this->config_tmp = array_merge($this->config_tmp, [
            'pms_site_title' => htmlentities($_POST['site_title'], ENT_QUOTES, "UTF-8"),
            'pms_db_name' => htmlentities($_POST['db_name'], ENT_QUOTES, "UTF-8"),
            'pms_db_host' => htmlentities($_POST['db_host'], ENT_QUOTES, "UTF-8"),
            'pms_db_port' => htmlentities($_POST['db_port'], ENT_QUOTES, "UTF-8"),
            'pms_db_user' => htmlentities($_POST['db_user'], ENT_QUOTES, "UTF-8"),
            'pms_db_pass' => htmlentities($_POST['db_pass'], ENT_QUOTES, "UTF-8"),
            'pms_email' => htmlentities($_POST['email'], ENT_QUOTES, "UTF-8")
        ]);

        $this->user_data = array_merge($this->user_data, [
            'login' => htmlentities($_POST['user'], ENT_QUOTES, "UTF-8"),
            'password' => $_POST['password']
        ]);

        // Validation of the fields of the form
        if ($this->config_tmp['pms_db_name'] == "") $this->field_notice['db_name'] = "Required field";
        if ($this->config_tmp['pms_db_host'] == "") $this->field_notice['db_host'] = "Required field";
        if ($this->config_tmp['pms_db_port'] == "") $this->field_notice['db_port'] = "Required field";
        if ($this->config_tmp['pms_db_user'] == "") $this->field_notice['db_user'] = "Required field";
        if ($this->config_tmp['pms_db_pass'] == "") $this->field_notice['db_pass'] = "Required field";

        if ($this->user_data['login'] == "") $this->field_notice['user'] = "Required field";
        if ($this->user_data['password'] == "") $this->field_notice['password'] = "Required field";
        elseif ($this->user_data['password'] != $_POST['password2']) $this->field_notice['password'] = "The passwords don't match";
        elseif (mb_strlen($this->user_data['password'], "UTF-8") < 6) $this->field_notice['password'] = "The password is too short";
        if ($this->config_tmp['pms_email'] == "" || !preg_match("/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i", $this->config_tmp['pms_email'])) $this->field_notice['email'] = "Invalid email address";

        if (count($this->field_notice) == 0) {
            try {
                $this->pms_db = new Database($this->config_tmp['pms_db_host'], $this->config_tmp['pms_db_name'], $this->config_tmp['pms_db_port'], $this->config_tmp['pms_db_user'], $this->config_tmp['pms_db_pass']);
                $this->pms_db->exec("SET NAMES 'utf8'");
            } catch (\PDOException $e) {
                $_SESSION['msg_error'][] = "Unable to connect to the database. Please check the database connection parameters.<br>".$e->getMessage();
            }

            if ($this->pms_db instanceof \PDO) {
                if (DbUtils::dbTableExists($this->pms_db, "solutionsCMS_%") === false) {
                    $this->db_success = $this->executeSqlFile();
                }else
                    $this->db_success = true;

                if ($this->db_success === true) {
                    $this->finalizeInstallation($this->tmp_config_file, $this->config_file, $this->config_tmp, $this->tmp_htaccess_file, $this->htaccess_file);
                    $this->installed = true;
                    $_SESSION['msg_success'][] = "Congratulations! You have successfully finished the quick installation of your website. Click on login to begin.";
                } else {
                    $_SESSION['msg_error'][] = "Unable to create or edit database.";
                }
            }
        } else {
            $_SESSION['msg_error'][] = "The following form contains some errors.";
        }
    }

    /**
     * Executes the SQL script file to set up the database.
     *
     * @return bool Returns true if the SQL execution is successful, false otherwise.
     */
    private function executeSqlFile()
    {
        $pms_dbsql = file_get_contents($this->db_sql_file);
        $pms_dbsql = str_replace("MY_DATABASE", $this->config_tmp['pms_db_name'], $pms_dbsql);
        $pms_dbsql = str_replace("MY_DB_USER", $this->config_tmp['pms_db_user'], $pms_dbsql);
        $pms_dbsql = str_replace("MY_DB_PASS", $this->config_tmp['pms_db_pass'], $pms_dbsql);
        $pms_dbsql = str_replace("USER_LOGIN", $this->user_data['login'], $pms_dbsql);
        $pms_dbsql = str_replace("USER_EMAIL", $this->config_tmp['pms_email'], $pms_dbsql);
        $pms_dbsql = str_replace("USER_PASS_HASH", password_hash($this->user_data['password'], PASSWORD_DEFAULT), $pms_dbsql);
        $pms_dbsql = str_replace("INSTALL_DATE", time(), $pms_dbsql);

        $result = $this->pms_db->query($pms_dbsql);

        if ($result !== false)
            return true;
        else {
            $_SESSION['msg_error'][] = preg_replace("/(\r\n|\n|\r)/", "", nl2br(print_r($this->pms_db->errorInfo(), true)));
            return false;
        }
    }

    /**
     * Finalizes the installation by writing configuration files and 
     * creating necessary system files like .htaccess.
     *
     * @return void
     */
    private function finalizeInstallation()
    {
        $config_str = file_get_contents($this->tmp_config_file);
        foreach ($this->config_tmp as $key => $value) {
            $key = mb_strtoupper($key, "UTF-8");
            if ($value != "") {
                $value = strtr($value, ["\\\\" => "\\\\\\\\", "$" => "\\$"]);
                $config_str = preg_replace("/define\((\"|')".$key."(\"|'),\s*(\"|')?([^\n\"']*)(\"|')?\);/", "define('".$key."', '".$value."');", $config_str);
            }
        }

        if (file_put_contents($this->config_file, $config_str) === false) {
            $_SESSION['msg_notice'][] = "<b>But... We cannot write into the file config/config.php.<br>";
            $_SESSION['msg_notice'][] = "To complete the installation, edit manualy this file, copy and past the following lines:</b><br>";
            $_SESSION['msg_notice'][] = preg_replace("/(\r\n|\n|\r)/", "", nl2br(htmlentities($config_str, ENT_QUOTES, "UTF-8")));
        }

        // Creation of .htaccess file if needed
        if (!is_file($this->htaccess_file)) {
            $ht_content = file_get_contents($this->tmp_htaccess_file);
            $ht_content = preg_replace('/\[E=BASE:\{DOCBASE\}\]/', '[E=BASE:' . DOCBASE . ']', $ht_content);
            if (file_put_contents($this->htaccess_file, $ht_content) === false) {
                $_SESSION['msg_notice'][] = "<b>We cannot write into the file public/.htaccess.<br>";
                $_SESSION['msg_notice'][] = "To complete the installation, edit manualy this file, copy and past the following lines:</b><br>";
                $_SESSION['msg_notice'][] = preg_replace("/(\r\n|\n|\r)/", "", nl2br(htmlentities($ht_content, ENT_QUOTES, "UTF-8")));
            }
        }
    }
}
