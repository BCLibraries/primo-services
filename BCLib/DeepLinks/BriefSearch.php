<?php

namespace BCLib\DeepLinks;

class BriefSearch extends DeepLink
{
    private $_queries = array();
    private $_scope;

    public function __construct($host = 'bc-primo.hosted.exlibrisgroup.com', $port = '0')
    {
        $this->_setURL('dlSearch.do', $host, $port);
        $this->_query_string_fields[] = 'institution=BCL&vid=bclib&onCampus=true&group=GUEST';
    }



}