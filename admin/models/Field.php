<?php

namespace Pandao\Admin\Models;

/**
 * Class of the fields displayed in the form of a module
 */
class Field
{
    private $values;
    private $notices;

    public $name;
    public $label;
    public $type;
    public $required;
    public $options;
    public $validation;
    public $multilingual;
    public $unique;
    public $comment;
    public $active;
    public $editor;
    public $optionTable;
    public $roles;
    public $filterName;
    public $optFilters;

    /**
     * Field constructor. Initializes a Field object with the provided parameters.
     *
     * @param string $name The name of the field.
     * @param string $label The label of the field.
     * @param string $type The type of the field.
     * @param int $required Whether the field is required (1 for true, 0 for false).
     * @param string|null $validation The validation rules for the field.
     * @param array|null $options The options available for the field (if applicable).
     * @param int $multilingual Whether the field is multilingual (1 for true, 0 for false).
     * @param int $unique Whether the field requires a unique value (1 for true, 0 for false).
     * @param string|null $comment A comment or note related to the field.
     * @param int $active Whether the field is active (1 for true, 0 for false).
     * @param int $editor Whether the field is an editor (1 for true, 0 for false).
     * @param string|null $optionTable The table where options for the field are stored.
     * @param array|null $roles The roles that have permission to access the field.
     * @param string|null $filterName The name of the filter associated with the field.
     * @param array|null $optFilters Additional filters for the field's options.
     *
     */
    public function __construct($name, $label, $type, $required, $validation, $options, $multilingual, $unique, $comment, $active, $editor, $optionTable, $roles, $filterName, $optFilters)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        if (is_numeric($required) && ($required == 1 || $required == 0))
            $this->required = $required;
        if (is_array($options))
            $this->options = $options;
        $this->validation = $validation;
        $this->values = array();
        if (is_numeric($multilingual) && ($multilingual == 1 || $multilingual == 0))
            $this->multilingual = $multilingual;
        if (is_numeric($unique) && ($unique == 1 || $unique == 0))
            $this->unique = $unique;
        else
            $this->active = 0;
        $this->comment = $comment;
        if (is_numeric($active) && ($active == 1 || $active == 0))
            $this->active = $active;
        else
            $this->active = 1;
        if (is_numeric($editor) && ($editor == 1 || $editor == 0))
            $this->editor = $editor;
        else
            $this->editor = 0;
        $this->notices = array();
        $this->optionTable = $optionTable;
        $this->roles = $roles;
        $this->filterName = $filterName;
        $this->optFilters = $optFilters;
    }

    /**
     * Get the value of the field.
     *
     * @param bool $encode Whether to encode the value as HTML entities.
     * @param int $index The index of the value to retrieve.
     * @param int $id_lang The language ID to use. Defaults to PMS_DEFAULT_LANG.
     *
     * @return string|array The value of the field or an empty string if not set.
     */
    public function getValue($encode = false, $index = 0, $id_lang = PMS_DEFAULT_LANG)
    {
        if (!MULTILINGUAL) $id_lang = 0;
        if (isset($this->values[$index][$id_lang])) {
            if (!is_array($this->values[$index][$id_lang]))
                return ($encode) ? htmlentities($this->values[$index][$id_lang], ENT_QUOTES, "UTF-8") : stripslashes($this->values[$index][$id_lang]);
            else
                return $this->values[$index][$id_lang];
        } else
            return "";
    }

    /**
     * Remove the value of the field at the specified index.
     *
     * @param int $index The index of the value to remove.
     *
     */
    public function removeValue($index)
    {
        if (isset($this->values[$index]))
            unset($this->values[$index]);
    }

    /**
     * Get all values of the field for a specific language or all languages.
     *
     * @param int|null $id_lang The language ID to filter the values. If null, return all values.
     *
     * @return array The array of all values.
     */
    public function getAllValues($id_lang = null)
    {
        if (!is_null($id_lang)) {
            $all_values = array();
            if (!MULTILINGUAL) $id_lang = 0;
            foreach ($this->values as $i => $values) {
                if (isset($values[$id_lang])) {
                    if (!is_array($values[$id_lang]))
                        $all_values[$i][$id_lang] = stripslashes($values[$id_lang]);
                    else
                        $all_values[$i][$id_lang] = $values[$id_lang];
                }
            }
            return $all_values;
        } else
            return $this->values;
    }

    /**
     * Get the notice (warning or message) for the field at the specified index.
     *
     * @param int $index The index of the notice to retrieve.
     *
     * @return string The notice message or an empty string if not set.
     */
    public function getNotice($index = 0)
    {
        if (isset($this->notices[$index]))
            return $this->notices[$index];
        else
            return "";
    }

    /**
     * Check if the specified role type is allowed to access the field.
     *
     * @param string $type The role type to check.
     *
     * @return bool True if the role is allowed, false otherwise.
     */
    public function isAllowed($type)
    {
        $roles = $this->roles;
        return (in_array($type, $roles) || in_array("all", $roles));
    }

    /**
     * Set the value of the field for a specific index and language.
     *
     * @param string|array $value The value to set.
     * @param int $index The index of the value.
     * @param int|null $id_lang The language ID. If null, set for all languages.
     *
     */
    public function setValue($value, $index = 0, $id_lang = null)
    {
        if (!is_null($id_lang)) {
            if (is_array($value)) {
                $this->values[$index][$id_lang] = $value;
            } else {
                $this->values[$index][$id_lang] = (is_null($value) ? '' : html_entity_decode($value, ENT_QUOTES, "UTF-8"));
            }
        } else {
            for ($i = 0; $i < count($this->values); $i++) {
                if (is_array($value)) {
                    $this->values[$index][$i] = $value;
                } else {
                    $this->values[$index][$i] = (is_null($value) ? '' : html_entity_decode($value, ENT_QUOTES, "UTF-8"));
                }
            }
        }
    }

    /**
     * Set the notice (warning) for the field at the specified index.
     *
     * @param string $notice The notice message to set.
     * @param int $index The index of the notice.
     *
     */
    public function setNotice($notice, $index = 0)
    {
        $this->notices[$index] = $notice;
    }
}