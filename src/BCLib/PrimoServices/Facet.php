<?php

namespace BCLib\PrimoServices;

/**
 * Class Facet
 * @package BCLib\PrimoServices
 *
 * @property string       $id
 * @property string       $name
 * @property int          $count
 * @property FacetValue[] $values
 */
class Facet implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_name;
    private $_id;
    private $_count;
    private $_values = array();

    public function sortByFrequency()
    {
        usort($this->_values, function ($a, $b)
        {
            return $b->count - $a->count;
        });
    }

    public function sortAlphabetically()
    {
        usort($this->_values, function ($a, $b)
        {
            return strcasecmp($a->value, $b->value);
        });
    }

    public function limit($max_values)
    {
        $this->_values = array_slice($this->_values, 0, $max_values);
    }

    public function remap(array $mapping_array)
    {
        for ($i = 0; $i < count($this->_values); $i++)
        {
            $current = $this->_values[$i]->value;

            if (isset($mapping_array[$current]))
            {
                $this->_values[$i]->display_name = $mapping_array[$current];
            }
        }
    }
}