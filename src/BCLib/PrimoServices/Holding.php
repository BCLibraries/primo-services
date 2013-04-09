<?php

namespace BCLib\PrimoServices;

/**
 * Class Holding
 * @package BCLib\PrimoServices
 *
 * @property string $openurl
 * @property string $type
 */
abstract class Holding
{
    use Accessor, EncodeJson;

    protected $_type;

}