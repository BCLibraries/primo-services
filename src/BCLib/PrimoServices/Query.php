<?php

namespace BCLib\PrimoServices;

class Query
{
    private $_host;
    private $_parameters = array();
    private $_terms = array();

    const BRIEF_SEARCH_PATH = '/PrimoWebServices/xservice/search/brief';

    public function __construct($host, $institution, $start_idx = 0, $bulk_size = 10)
    {
        $this->_host = $host;
        $this->_parameters['institution'] = $institution;
        $this->_parameters['indx'] = $start_idx;
        $this->_parameters['bulkSize'] = $bulk_size;
    }

    public function addTerm(QueryTerm $query_term)
    {
        $this->_terms[] = 'query=' . urlencode($query_term->queryString());
    }

    public function __toString()
    {
        $url = 'http://' . $this->_host . Query::BRIEF_SEARCH_PATH .
            '?' . http_build_query($this->_parameters);
        $url .= (count($this->_terms) > 0) ? '&' . join('&', $this->_terms) : '';

        return $url;
    }
}