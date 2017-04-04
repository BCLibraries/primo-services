<?php

namespace BCLib\PrimoServices;

class Facet
{
    public $name;
    public $id;
    public $count;

    /**
     * @var FacetValue[]
     */
    public $values = [];

    public function sortByFrequency()
    {
        usort(
            $this->values,
            function ($a, $b) {
                return $b->count - $a->count;
            }
        );
    }

    public function sortAlphabetically()
    {
        usort(
            $this->values,
            function ($a, $b) {
                return strcasecmp($a->display_name, $b->display_name);
            }
        );
    }

    public function limit($max_values)
    {
        $this->values = array_slice($this->values, 0, $max_values);
    }

    public function remap(array $mapping_array)
    {
        foreach ($this->values as $value) {
            if (isset($mapping_array[$value->value])) {
                $value->display_name = $mapping_array[$value->value];
            }
        }
    }
}