<?php

namespace Pandao\Common\Models;

class LangManager
{
    protected $pms_db;

    public $env_variables = [];
    public $default_env_variables = [];
    public $languages = [];

    /**
     * Constructor to initialize the language manager.
     *
     * @param Database $db The database connection object.
     */
    public function __construct($db)
    {
        $this->pms_db = $db;

        // Default environment variables for language and currency settings
        $this->default_env_variables = [
            'currency_code' => 'USD',
            'currency_sign' => '$',
            'currency_rate' => 1,
            'locale' => 'en_GB',
            'rtl_dir' => false,
            'lang_id' => 2,
            'lang_tag' => 'en'
        ];
        $this->env_variables = $this->default_env_variables;

        $this->handleLanguageSettings();
    }

    /**
     * Handles the language settings by fetching languages from the database.
     * Updates environment variables based on the main language.
     *
     */
    private function handleLanguageSettings()
    {
        if($this->pms_db !== false){
            $result_lang = $this->pms_db->query('SELECT l.id AS lang_id, lf.id AS file_id, title, tag, file, locale, rtl, main FROM solutionsCMS_lang as l, solutionsCMS_lang_file as lf WHERE id_item = l.id AND l.checked = 1 AND file != \'\' ORDER BY l.rank');
            if ($result_lang !== false) {
                foreach ($result_lang as $i => $row) {
                    $lang_tag = $row['tag'];
                    if ($row['main'] == 1) {
                        // Set default language settings based on the main language from the database
                        $this->default_env_variables['lang_id'] = $row['lang_id'];
                        $this->default_env_variables['lang_tag'] = $lang_tag;
                        $this->default_env_variables['locale'] = $row['locale'];
                        $this->default_env_variables['rtl_dir'] = $row['rtl'];
                    }
                    // Store language information, including the path to the language file
                    $row['file'] = DOCBASE . 'medias/lang/big/' . $row['file_id'] . '/' . $row['file'];
                    $this->languages[$lang_tag] = $row;
                }
            }
        }

        // Update environment variables with the default language settings
        $this->env_variables['lang_id'] = $this->default_env_variables['lang_id'];
        $this->env_variables['lang_tag'] = $this->default_env_variables['lang_tag'];
        $this->env_variables['locale'] = $this->default_env_variables['locale'];
        $this->env_variables['rtl_dir'] = $this->default_env_variables['rtl_dir'];
    }

    /**
     * Get active languages with their associated images.
     *
     * @return array List of languages with ID, title, and image.
     */
    public function getLanguagesWithImages()
    {
        $languages = [];
        $query_lang = "SELECT id, title, tag FROM solutionsCMS_lang WHERE checked = 1 ORDER BY CASE main WHEN 1 THEN 0 ELSE 1 END, `rank`";
        $result_lang = $this->pms_db->query($query_lang);

        foreach ($result_lang as $i => $row_lang) {
            $id_lang = $row_lang['id'];
            $title_lang = $row_lang['title'];
            $lang_tag = $row_lang['tag'];
            
            $query_img_lang = "SELECT * FROM solutionsCMS_lang_file WHERE id_item = :id_lang AND `type` = 'image' AND file != '' ORDER BY `rank` LIMIT 1";
            $result_img_lang = $this->pms_db->prepare($query_img_lang);
            $result_img_lang->execute(['id_lang' => $id_lang]);

            $image_path = null;
            if ($result_img_lang->rowCount() > 0) {
                $row_img_lang = $result_img_lang->fetch();
                $image_path = DOCBASE . 'medias/lang/big/' . $row_img_lang['id'] . '/' . $row_img_lang['file'];
            }

            $languages[$lang_tag] = [
                'id' => $id_lang,
                'title' => $title_lang,
                'image' => $image_path
            ];
        }

        return $languages;
    }
}
