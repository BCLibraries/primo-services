<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

abstract class PrimoRequest extends XServices\Request
{
    private $_scopes = array();

    protected function _setServiceUrl($url, $host = 'agama.bc.edu', $port = '1701')
    {
        $this->_setUrl('http://' . $host . ':' . $port . '/PrimoWebServices/xservice/' . $url);
    }

    protected function _addQuery($type, $operator, $term)
    {
        $parameter = 'query';
        $value = urlencode("$type,$operator,$term");
        $this->_addArgument($parameter, $value);
    }

    public function setGroup($group = 'GUEST')
    {
        $this->_addArgument('group', $group);
    }

    public function addScope($scope)
    {
        $this->_scopes[] = $scope;
    }

    public function send(\HTTP_Request2 $request)
    {
        if (count($this->_scopes))
        {
            $scopes = join(',', $this->_scopes);
            $this->_addArgument('loc', 'local,scope:(' . $scopes . ')');
        }
        return parent::send($request);
    }
}