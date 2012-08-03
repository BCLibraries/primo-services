<?php

namespace BCLib\DeepLinks;

class BriefSearch extends DeepLink
{
    private $_search_fields = array();

    public function __construct($host = 'bc-primo.hosted.exlibrisgroup.com', $port = '0')
    {
        $this->_setURL('dlSearch', $host, $port);
    }

    private function _addQuery($field, $delimiter, $value)
    {
        if (!isset ($this->_search_fields[$field]))
        {
            $this->_search_fields[$field] = array();
            $this->_search_fields[$field]['value'] += '+OR+'.$value;
        }
        else
        {
            $this->_search_fields[$field]['delimeter'] = $delimiter;
            $this->_search_fields[$field]['value'] = $value;
        }
    }

    public function ISBN($isbn)
    {

        $this->_addQuery('isbn','contains', $isbn);
    }
}