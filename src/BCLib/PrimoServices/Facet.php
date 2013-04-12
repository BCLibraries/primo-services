<?php

namespace BCLib\PrimoServices;

/**
 * Class Facet
 * @package BCLib\PrimoServices
 *
 * @property string       $name
 * @property int          $count
 * @property FacetValue[] $values
 */
class Facet implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_name;
    private $_count;
    private $_values = array();
}