<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

class PrimoRequest extends XServices\XServiceRequest
{
    protected function _setServiceUrl($url)
    {
        $this->_setUrl('http://agama.bc.edu:1701/PrimoWebServices/xservice/' . $url);
    }
}