<?php

namespace BCLib\DeepLinks;

class BriefSearch extends DeepLink
{
    private $_query;

    public function __construct(\BCLib\PrimoTools\Query $query,
                                \BCLib\PrimoTools\Scope $scope,
                                $host = 'bc-primo.hosted.exlibrisgroup.com',
                                $port = '0')
    {
        $this->_setURL('dlSearch.do', $host, $port);
        $this->_query_string_fields[] = 'institution=BCL&vid=bclib&onCampus=true&group=GUEST';
        $this->_query_string_fields[] = (string) $scope;
        $this->_query_string_fields[] = (string) $query;
    }

    public function tab($tab)
    {
        $this->_query_string_fields[] = 'tab=' . $tab;
    }
}