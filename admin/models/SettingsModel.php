<?php

namespace Pandao\Admin\Models;

class SettingsModel
{
    protected $pms_db;
    protected $configFile;
    protected $htaccessFile;

    /**
     * SettingsModel constructor. Initializes the SettingsModel with the database connection and config/htaccess file paths.
     *
     * @param Database $db Database connection.
     */
    public function __construct($db)
    {
        $this->pms_db = $db;
        $this->configFile = SYSBASE . 'config/config.php';
        $this->htaccessFile = SYSBASE . '.htaccess';
    }

    /**
     * Get current configuration constants.
     *
     * @return array Current configuration values.
     */
    public function getConfig()
    {
        $config = [];
        $constants = get_defined_constants(true);
        if (isset($constants['user'])) {
            foreach ($constants['user'] as $key => $value) {
                if (strpos($key, 'PMS_') === 0) {
                    $config[mb_strtolower($key)] = $value;
                }
            }
        }
        return $config;
    }

    /**
     * Check if a user already exists (excluding the current user).
     *
     * @param string $user Username to check.
     * @param int $currentUserId Current user's ID.
     *
     * @return bool True if the user exists.
     */
    public function userExists($user, $currentUserId)
    {
        $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_user WHERE login = :login AND id != :id');
        $stmt->execute(['login' => $user, 'id' => $currentUserId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Test database connection with new configuration parameters.
     *
     * @param array $config_tmp Temporary configuration array.
     *
     * @return \PDO|false The PDO connection object or false on failure.
     */
    public function testDbConnection($config_tmp)
    {
        try {
            $db = new \PDO('mysql:host='.$config_tmp['pms_db_host'].';port='.$config_tmp['pms_db_port'].';dbname='.$config_tmp['pms_db_name'].';charset=utf8', $config_tmp['pms_db_user'], $config_tmp['pms_db_pass']);
            $db->exec('SET NAMES \'utf8\'');
            return $db;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Rename the admin folder and update the .htaccess file.
     *
     * @param array $config_tmp New configuration array.
     * @param string $curr_dirname Current directory name.
     * @param string $curr_folder Current folder name.
     *
     * @return bool True if the folder is renamed successfully.
     */
    public function renameAdminFolder($config_tmp, $curr_dirname, $curr_folder)
    {
        $renamed = false;
        if ($config_tmp['pms_admin_folder'] != '') {
            $renamed = @rename($curr_dirname, SYSBASE . $config_tmp['pms_admin_folder']);
            if ($renamed && is_file($this->htaccessFile)) {
                $admin_rule = 'RewriteCond %{REQUEST_URI} /'.PMS_ADMIN_FOLDER.'/';
                $new_admin_rule = 'RewriteCond %{REQUEST_URI} /'.$config_tmp['pms_admin_folder'].'/';
                
                $ht_content = str_replace($admin_rule, $new_admin_rule, file_get_contents($this->htaccessFile));
                if (file_put_contents($this->htaccessFile, $ht_content) === false) {
                    return false;
                }
            }
        }
        return $renamed;
    }

    /**
     * Update the configuration file with new values.
     *
     * @param array $config_tmp New configuration array.
     * @param bool $renamed True if the admin folder was renamed.
     *
     * @return bool True on successful update.
     */
    public function updateConfigFile($config_tmp, $renamed)
    {
        $config_str = file_get_contents($this->configFile);
        $count = substr_count($config_str, 'define(');

        foreach ($config_tmp as $key => $value) {
            if ($key != 'pms_admin_folder' || ($config_tmp['pms_admin_folder'] != '' && $renamed)) {
                $key_upper = mb_strtoupper($key, 'UTF-8');
                $value = strtr($value, array('\\\\' => '\\\\\\\\', '$' => '\\$'));
                $config_str = preg_replace('/define\(("|\')'.$key_upper.'("|\'),\s*("|\')?([^\n\r]*)("|\')?\);/i', 'define(\''.$key_upper.'\', \''.$value.'\');', $config_str);
            }
        }

        if ($config_str == '' || substr_count($config_str, 'define(') != $count || file_put_contents($this->configFile, $config_str) === false) {
            return false;
        }
        opcache_invalidate($this->configFile, true);
        
        return true;
    }

    /**
     * Update user information in the database.
     *
     * @param array $data User data array.
     *
     * @return bool True on success.
     */
    public function updateUser($data)
    {
        $sql = 'UPDATE solutionsCMS_user SET ';
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $fields[] = "$key = :$key";
            }
        }
        $sql .= implode(', ', $fields);
        $sql .= ' WHERE id = :id';

        $stmt = $this->pms_db->prepare($sql);
        return $stmt->execute($data);
    }
}
