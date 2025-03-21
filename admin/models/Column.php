<?php

namespace Pandao\Admin\Models;

use Pandao\Common\Utils\StrUtils;

/**
 * Class of the columns displayed in the listing of a module
 */
class Column
{
    public $name;
    public $label;
    public $type;
    public $link;
    public $table;
    public $fieldRef;
    public $fieldValue;

    private $caseValues;
    public $values;

    /**
     * Column constructor. Initializes a Column object with the provided parameters.
     *
     * @param string $name The name of the column.
     * @param string $label The label of the column.
     * @param string $type The type of the column.
     * @param string $table The table from which the column values are sourced, if applicable.
     * @param string $fieldRef The foreign key column name in the module's table.
     * @param string $fieldValue The column name in the target table that contains the value to display.
     * @param array $caseValues The case-specific values for the column.
     *
     */
    public function __construct($name, $label, $type, $link, $table, $fieldRef, $fieldValue, $caseValues)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->link = $link;
        $this->values = array();
        $this->table = $table;
        $this->fieldRef = $fieldRef;
        $this->fieldValue = $fieldValue;
        $this->caseValues = $caseValues;
    }

    /**
     * Get the value of the column for a specific row.
     *
     * @param int $row The row index.
     *
     * @return string|null The value for the row, or null if not found.
     */
    public function getValue($row)
    {
        return !empty($this->values[$row]) ? $this->values[$row] : null;
    }

    /**
     * Get the case-specific value for a given case.
     *
     * @param mixed $case The case to retrieve the value for.
     *
     * @return string The case-specific value, or the case itself if not found.
     */
    public function getCaseValue($case)
    {
        return !empty($this->caseValues[$case]) ? htmlentities($this->caseValues[$case], ENT_QUOTES, "UTF-8") : $case;
    }

    /**
     * Set the value of the column for a specific row.
     *
     * @param int $row The row index.
     * @param string $value The value to set for the row.
     *
     */
    public function setValue($row, $value)
    {
        $this->values[$row] = empty($this->link) ? StrUtils::encodeIfHtml($value) : $value;
    }
}