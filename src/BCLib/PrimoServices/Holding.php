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
    protected $_openurl;

    protected function _set_openurl($openurl)
    {
        if (($type != 'viewit') && ($type != 'getit'))
        {
            throw new \Exception("$type is not a valid service type");
        }
        $this->_openurl = $openurl;
    }

}