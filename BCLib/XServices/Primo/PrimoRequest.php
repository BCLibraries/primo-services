<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

abstract class PrimoRequest extends XServices\Request
{
    protected function _setServiceUrl($url, $host = 'agama.bc.edu', $port = '1701' )
    {
        $this->_setUrl('http://'.$host.':'.$port.'/PrimoWebServices/xservice/' . $url);
    }
    
    protected function _addQuery($type, $operator, $term)
    {
        $parameter = 'query';
        $value = urlencode("$type,$operator,$term");
        $this->_addArgument($parameter, $value);
    }
}