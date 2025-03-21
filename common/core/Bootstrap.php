<?php

namespace Pandao\Common\Core;

use Pandao\Common\Utils\AuthUtils;
use Pandao\Common\Utils\UrlUtils;
use Pandao\Common\Utils\DbUtils;
use Pandao\Common\Models\LangManager;

class Bootstrap
{
    protected $pms_db;
    protected $langManager;

    private $currencies = [];

    public $languages = [];
    public $env_variables = [];
    public $default_env_variables = [];

    public $config_file;

    /**
     * Bootstrap constructor. Initializes global settings and config paths.
     */
    function __construct()
    {
        $this->initializeGlobals();
        $this->config_file = SYSBASE . 'config/config.php';
        $this->pms_db = false;
    }

    /**
     * Initialize the application, start session, and handle database connection.
     */
    public function init()
    {
        $this->startSession();
        
        require_once __DIR__ . '/../../vendor/autoload.php';

        if (is_file($this->config_file)) {
            require_once $this->config_file;
            $this->connectDatabase();
        }

        // If not in setup mode
        if (!isset($_GET['module']) || $_GET['module'] != 'setup') {
            // If not installed
            if (!$this->checkSetup()) {
                
                // HTTP request
                if (!UrlUtils::isXhr()) {
                    header('Location: ' . DOCBASE . PMS_ADMIN_FOLDER . '/module=setup');
                    exit;
                } else {
                    exit;
                }
            } else {
                // Installed but failed database connection
                if ($this->pms_db === false && !ADMIN) die('Unable to connect to the database. Please contact the webmaster or try again later.');
            }
        }
        
        $this->langManager = new LangManager($this->pms_db);
        $this->default_env_variables = $this->langManager->default_env_variables;
        $this->env_variables = $this->langManager->env_variables;
        $this->languages = $this->langManager->languages;

        if (!ADMIN) $this->createAssetsLinks();
    }

    /**
     * Initialize global constants and paths.
     */
    private function initializeGlobals()
    {
        if (!defined('SYSBASE')) define('SYSBASE', str_replace('\\', '/', realpath(dirname(__FILE__).'/../../').'/'));

        $base = getenv('BASE');
        if ($base === false) {
            $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            $pos = strrpos(SYSBASE, '/'.$request_uri[0].'/');
            $base = ($pos !== false) ? substr(SYSBASE, $pos) : '/';
        }
        define('DOCBASE', $base);

        $http = 'http';
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $http .= 's';
        define('HTTP', $http);
    }

    /**
     * Start the session with a hashed session name.
     */
    private function startSession()
    {
        $session_name = hash('sha256', realpath(dirname(__FILE__))) . '_session';
        session_name($session_name);
        if (!AuthUtils::isSessionStarted()) session_start();

        foreach (['success', 'notice', 'error'] as $alertType) {
            if (!isset($_SESSION['msg_' . $alertType]) || !is_array($_SESSION['msg_' . $alertType])) {
                $_SESSION['msg_' . $alertType] = array();
            } else {
                $_SESSION['msg_' . $alertType] = array_unique($_SESSION['msg_' . $alertType]);
            }
        }
    }

    /**
     * Connect to the database.
     */
    private function connectDatabase()
    {
        $db = new Database(PMS_DB_HOST, PMS_DB_NAME, PMS_DB_PORT, PMS_DB_USER, PMS_DB_PASS);
        $this->pms_db = $db->isConnected ? $db : false;
    }

    /**
     * Check if the system setup is complete.
     *
     * @return bool True if setup is complete, false otherwise.
     */
    protected function checkSetup()
    {
        if (($this->pms_db !== false && DbUtils::dbTableExists($this->pms_db, 'solutionsCMS_%') === false) || !is_file(SYSBASE . 'config/config.php')) {
            return false;
        }
        return true;
    }

    /**
     * Get the database connection instance.
     *
     * @return mixed Database connection or false if not connected.
     */
    public function getDb()
    {
        return $this->pms_db;
    }

    /**
     * Create symbolic links for assets from the template directory to the public directory.
     */
    private function createAssetsLinks()
    {
        $sourcePath = __DIR__ . '/../../templates/' . PMS_TEMPLATE . '/assets';
        $destPath = __DIR__ . '/../../public/assets';

        if (!is_link($destPath)) {
            symlink($sourcePath, $destPath);
        }
    }

    /**
     * Define locale-related constants for the application, such as language, currency, and timezone.
     */
    public function defineLocaleConstants()
    {
        // Currency settings
        if ($this->pms_db !== false) {
            $result_currency = $this->pms_db->query('SELECT * FROM solutionsCMS_currency');
            if ($result_currency !== false) {
                foreach ($result_currency as $i => $row) {
                    if ($row['main'] == 1) {
                        $this->default_env_variables['currency_code'] = $row['code'];
                        $this->default_env_variables['currency_sign'] = $row['sign'];
                    }
                    $this->currencies[$row['code']] = $row;
                }
            }
        }

        $this->env_variables['currency_code'] = isset($_SESSION['currency']['code']) ? $_SESSION['currency']['code'] : $this->default_env_variables['currency_code'];
        $this->env_variables['currency_sign'] = isset($_SESSION['currency']['sign']) ? $_SESSION['currency']['sign'] : $this->default_env_variables['currency_sign'];
        $this->env_variables['currency_rate'] = isset($_SESSION['currency']['rate']) ? $_SESSION['currency']['rate'] : $this->default_env_variables['currency_rate'];

        define('PMS_DEFAULT_LANG', $this->default_env_variables['lang_id']);
        define('PMS_LANG_ID', $this->env_variables['lang_id']);
        define('PMS_LANG_TAG', $this->env_variables['lang_tag']);
        define('PMS_LOCALE', $this->env_variables['locale']);
        define('PMS_DEFAULT_CURRENCY_CODE', $this->default_env_variables['currency_code']);
        define('PMS_DEFAULT_CURRENCY_SIGN', $this->default_env_variables['currency_sign']);
        define('PMS_DEFAULT_CURRENCY_RATE', $this->default_env_variables['currency_rate']);
        define('PMS_CURRENCY_CODE', $this->env_variables['currency_code']);
        define('PMS_CURRENCY_SIGN', $this->env_variables['currency_sign']);
        define('PMS_CURRENCY_RATE', $this->env_variables['currency_rate']);
        define('PMS_RTL_DIR', $this->default_env_variables['rtl_dir']);

        // Timezone and locale
        if (defined('PMS_TIME_ZONE')) {
            date_default_timezone_set(PMS_TIME_ZONE);
            if (setlocale(LC_ALL, PMS_LOCALE . '.UTF-8', PMS_LOCALE) === false) {
                setlocale(LC_ALL, 'en_GB.UTF-8', 'en_GB');
            }
        }
    }
}
