<?php

namespace Pandao\Admin\Models;

use Pandao\Admin\Core\Helpers;
use Pandao\Admin\Models\Module;
use \DOMDocument;

class AdminContext
{
    protected $pms_db;

    private static $instance = null;
    private $langManager;

    public $texts = [];
    public $modules = [];
    public $indexes = [];
    public $currModule;
    public $languages;
    public $permissions = [];
    public $addAllowed = false;
    public $editAllowed = false;
    public $deleteAllowed = false;
    public $publishAllowed = false;
    public $uploadAllowed = false;
    public $viewAllowed = false;
    public $noAccess = true;
    public $allAccess = false;
    public $allowableExts = [];

    /**
     * AdminContext constructor. Initializes the class and loads texts and modules.
     *
     * @param object $db Database connection object.
     *
     */
    private function __construct($db, $langManager, $installed = true)
    {
        $this->pms_db = $db;
        if(!defined('PMS_ADMIN_LANG_FILE')) define('PMS_ADMIN_LANG_FILE', 'en.ini');
        $this->loadTexts();

        if(!$installed) return;

        if(isset($_SESSION['user'])) $this->loadModules();
        $this->setAllowableExts();
        $this->langManager = $langManager;
        $this->languages = $this->langManager->getLanguagesWithImages();
    }

    /**
     * Get the singleton instance of AdminContext.
     *
     * @param object|null $db Database connection object (required for the first instance).
     *
     * @return self The singleton instance of AdminContext.
     * @throws \Exception If the database connection is not provided for the first instance.
     */
    public static function get($db = null, $langManager = null, $installed = true)
    {
        if (self::$instance === null) {
            if ($db === null) {
                throw new \Exception("Database connection is required for the first instance of AdminContext.");
            }
            if ($langManager === null) {
                throw new \Exception("langManager is required for the first instance of AdminContext.");
            }
            self::$instance = new self($db, $langManager, $installed);
        }
        return self::$instance;
    }

    /**
     * Load the texts for the admin interface.
     *
     */
    private function loadTexts()
    {
        $texts = array();
        $admin_lang_file = SYSBASE . PMS_ADMIN_FOLDER . '/includes/langs/' . PMS_ADMIN_LANG_FILE;
    
        if(ADMIN && is_file($admin_lang_file)){
            $texts = @parse_ini_file($admin_lang_file);
            if(is_null($texts))
                $texts = @parse_ini_string(file_get_contents($admin_lang_file));
        }

        $this->texts = $texts;
    }

    /**
     * Load the modules available for the current admin session.
     *
     */
    private function loadModules()
    {
        $modules = $this->getModules(PMS_ADMIN_FOLDER . '/modules');
        $this->modules = $modules;
        $moduleName = $_GET['module'] ?? null;

        if ($moduleName && isset($this->indexes[$moduleName]))
            $this->currModule = $modules[$this->indexes[$moduleName]];
            if($this->currModule) $this->currModule->classname = 'active';
    }

    /**
     * Recursively retrieve modules from the specified directory.
     *
     * @param string $dir The directory path to scan for modules.
     * @param array $modules The array to collect modules.
     *
     * @return array The array of loaded modules.
     */
    public function getModules($dir, $modules = array()) {
        
        $realdir = SYSBASE . $dir;

        $rep = opendir($realdir) or die('Error directory opening: ' . $realdir);

        while ($entry = @readdir($rep)) {
            if (is_dir($realdir . '/' . $entry) && $entry != '.' && $entry != '..' && substr($entry, 0, 1) != '.') {
                $modules = self::getModules($dir . '/' . $entry, $modules);
            } else {
                if (is_file($realdir . '/' . $entry) && $entry == 'config.xml') {

                    $dom = new DOMDocument();
                    $dom->load($realdir . '/config.xml') or die('Unable to load the XML file');
                    $dom->schemaValidate(__DIR__ . '/../includes/config.xsd') or die('The XML file does not respect the schema');

                    $moduleDom = $dom->getElementsByTagName('module')->item(0);

                    $index = htmlentities($moduleDom->getAttribute('index'), ENT_QUOTES, 'UTF-8');
                    $name = htmlentities($moduleDom->getAttribute('name'), ENT_QUOTES, 'UTF-8');
                    $title = Helpers::getTranslation(htmlentities($moduleDom->getAttribute('title'), ENT_QUOTES, 'UTF-8'), $this->texts);
                    $multi = htmlentities($moduleDom->getAttribute('multi'), ENT_QUOTES, 'UTF-8');
                    $library = htmlentities($moduleDom->getAttribute('library'), ENT_QUOTES, 'UTF-8');
                    $dashboard = htmlentities($moduleDom->getAttribute('dashboard'), ENT_QUOTES, 'UTF-8');
                    $ranking = htmlentities($moduleDom->getAttribute('ranking'), ENT_QUOTES, 'UTF-8');
                    $home = htmlentities($moduleDom->getAttribute('home'), ENT_QUOTES, 'UTF-8');
                    $main = htmlentities($moduleDom->getAttribute('main'), ENT_QUOTES, 'UTF-8');
                    $validation = htmlentities($moduleDom->getAttribute('validation'), ENT_QUOTES, 'UTF-8');
                    $dates = htmlentities($moduleDom->getAttribute('dates'), ENT_QUOTES, 'UTF-8');
                    $release = htmlentities($moduleDom->getAttribute('release'), ENT_QUOTES, 'UTF-8');
                    $icon = htmlentities($moduleDom->getAttribute('icon'), ENT_QUOTES, 'UTF-8');
                    $editorType = htmlentities($moduleDom->getAttribute('editorType'), ENT_QUOTES, 'UTF-8');

                    // Medias
                    $medias = $moduleDom->getElementsByTagName('medias')->item(0);
                    $max_medias = htmlentities($medias->getAttribute('max'), ENT_QUOTES, 'UTF-8');
                    $medias_multi = htmlentities($medias->getAttribute('multi'), ENT_QUOTES, 'UTF-8');
                    $resizing = htmlentities($medias->getAttribute('resizing'), ENT_QUOTES, 'UTF-8');

                    $big = $medias->getElementsByTagName('big')->item(0);
                    $max_w_big = htmlentities($big->getAttribute('maxw'), ENT_QUOTES, 'UTF-8');
                    $max_h_big = htmlentities($big->getAttribute('maxh'), ENT_QUOTES, 'UTF-8');

                    $medium = $medias->getElementsByTagName('medium')->item(0);
                    $max_w_medium = htmlentities($medium->getAttribute('maxw'), ENT_QUOTES, 'UTF-8');
                    $max_h_medium = htmlentities($medium->getAttribute('maxh'), ENT_QUOTES, 'UTF-8');

                    $small = $medias->getElementsByTagName('small')->item(0);
                    $max_w_small = htmlentities($small->getAttribute('maxw'), ENT_QUOTES, 'UTF-8');
                    $max_h_small = htmlentities($small->getAttribute('maxh'), ENT_QUOTES, 'UTF-8');

                    // Permissions
                    $permissions = [];
                    $usersDom = $moduleDom->getElementsByTagName('user');
                    foreach ($usersDom as $user) {
                        $type = htmlentities($user->getAttribute('type'), ENT_QUOTES, 'UTF-8');
                        $permissions[$type] = explode(',', str_replace(' ', '', htmlentities($user->getAttribute('permissions'), ENT_QUOTES, 'UTF-8')));
                    }
                    $rights = $permissions[$_SESSION['user']['type']] ?? null;
                    if(!in_array("no_access", $rights) && !empty($permissions)){

                        $modules[$index] = new Module(
                            $name, $title, $realdir, $multi, $ranking, $home, $main, $validation, $dates, $release, $library, 
                            $dashboard, $max_medias, $medias_multi, $resizing, $max_w_big, $max_h_big, $max_w_medium, $max_h_medium, 
                            $max_w_small, $max_h_small, $icon, $rights, $dom, $editorType
                        );

                        $this->indexes[$name] = $index;
                    }
                }
            }
        }
        closedir($rep);
        ksort($modules);
        return $modules;
    }

    /**
     * Set permissions based on the current module and user type.
     *
     */
    public function setPermissions()
    {
        $permissions = $this->currModule->permissions;

        $this->addAllowed = in_array('all', $permissions) || in_array('add', $permissions);
        $this->editAllowed = in_array('all', $permissions) || in_array('edit', $permissions);
        $this->deleteAllowed = in_array('all', $permissions) || in_array('delete', $permissions);
        $this->publishAllowed = in_array('all', $permissions) || in_array('publish', $permissions);
        $this->uploadAllowed = in_array('all', $permissions) || in_array('upload', $permissions);
        $this->viewAllowed = in_array('all', $permissions) || in_array('view', $permissions);
        $this->allAccess = in_array('all', $permissions);
        $this->noAccess = in_array('no_access', $permissions);
    }

    /**
     * Set the allowable file extensions and their corresponding icons.
     *
     */
    public function setAllowableExts()
    {
        $this->allowableExts = array(
            'pdf' => 'file-pdf',
            'doc' => 'file-word',
            'docx' => 'file-word',
            'odt' => 'file-word',
            'xls' => 'file-excel',
            'xlsx' => 'file-excel',
            'ods' => 'file-excel',
            'ppt' => 'file-powerpoint',
            'pptx' => 'file-powerpoint',
            'odp' => 'file-powerpoint',
            'txt' => 'file-text',
            'csv' => 'file-csv',
            'jpg' => 'file-image',
            'jpeg' => 'file-image',
            'png' => 'file-image',
            'gif' => 'file-image',
            'mp4' => 'file-video',
            'mov' => 'file-video',
            'webm' => 'file-video'
        );
    }
}
