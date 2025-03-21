<?php

namespace Pandao\Admin\Models;

/**
 * Class of the filters displayed in the search engine in the listing of a module
 */
class Filter
{
    public $name;
    public $label;
    public $value;
    public $type;
    public $options;
    public $filterName;
    public $optFilters;

    /**
     * Filter constructor. Initializes a Filter object with the provided parameters.
     *
     * @param string $name The name of the filter.
     * @param string $label The label of the filter.
     * @param array $options The options available for the filter.
     * @param string|null $filterName The name of the filter to apply.
     * @param array|null $optFilters Additional filters for the filter's options.
     *
     */
    public function __construct ($name, $label, $type, $options, $filterName, $optFilters)
    {
        $this->name = $name;
        $this->label = $label;
        if(is_array($options))
            $this->options = $options;
        $this->filterName = $filterName;
        $this->optFilters = $optFilters;
        $this->type = $type;
    }

    /**
     * Set the value of the filter.
     *
     * @param mixed $value The value to set for the filter.
     *
     */
    function setValue($value)
    {
        $this->value = $value;
    }
}
