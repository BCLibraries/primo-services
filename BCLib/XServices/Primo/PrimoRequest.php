<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

abstract class PrimoRequest extends XServices\Request
{
    protected function _setServiceUrl($url)
    {
        $this->_setUrl('http://agama.bc.edu:1701/PrimoWebServices/xservice/' . $url);
    }
    
    protected function _addQuery($type, $operator, $term)
    {
        $parameter = 'query';
        $value = "$type,$operator,$term";
        $this->_addArgument($parameter, $value);
    }
}