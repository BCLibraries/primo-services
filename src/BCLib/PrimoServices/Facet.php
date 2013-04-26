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
}