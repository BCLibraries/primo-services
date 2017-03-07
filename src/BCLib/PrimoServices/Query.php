<?php

namespace BCLib\PrimoServices;

class Query
{
    private $_parameters = array();
    private $_terms = array(
        'query' => array(),
        'query_inc' => array(),
        'query_exc' => array(),
    );

    public function __construct($institution, $start_idx = 1, $bulk_size = 10)
    {
        $this->_parameters['institution'] = $institution;
        $this->_parameters['indx'] = $start_idx;
        $this->_parameters['bulkSize'] = $bulk_size;
    }

    public function start($start_index)
    {
        $this->_parameters['indx'] = $start_index;
        return $this;
    }

    public function bulkSize($bulk_size)
    {
        $this->_parameters['bulkSize'] = $bulk_size;
        return $this;
    }

    public function addTerm(QueryTerm $query_term)
    {
        $this->_terms['query'][] = $query_term;
        return $this;
    }

    public function includeTerm(QueryTerm $query_term)
    {
        $this->_terms['query_inc'][] = $query_term;
        return $this;
    }

    public function excludeTerm(QueryTerm $query_term)
    {
        $this->_terms['query_exc'][] = $query_term;
        return $this;
    }

    public function getTerms($filter = null)
    {
        if (null !== $filter) {
            return $this->_terms[$filter];
        }
        return array_merge($this->_terms['query'], $this->_terms['query_inc'], $this->_terms['query_exc']);
    }

    public function getParameters()
    {
        return $this->_parameters;
    }

    public function sortField($sort_order)
    {
        $valid_sort_orders = array(
            'title'      => 'stitle',
            'date'       => 'scdate',
            'author'     => 'screator',
            'popularity' => 'popularity'
        );
        if (!array_key_exists($sort_order, $valid_sort_orders)) {
            throw new PrimoException($sort_order . ' is not a valid result sort');
        }

        $this->_parameters['sortField'] = $valid_sort_orders[$sort_order];
        return $this;
    }

    public function onCampus($on_campus = true)
    {
        if (!is_bool($on_campus)) {
            throw new PrimoException('onCampus() must take a boolean argument');
        }
        $this->_parameters['onCampus'] = $on_campus ? 'true' : 'false';
        return $this;
    }

    public function local($scope)
    {
        $this->_parameters['loc'] = "local,scope:($scope)";
        return $this;
    }

    public function articles()
    {
        $this->_parameters['loc'] = 'adaptor,primo_central_multiple_fe';
        return $this;
    }

    public function language($language_code)
    {
        $this->_parameters['lang'] = $language_code;
        return $this;
    }

    public function dym()
    {
        $this->_parameters['dym'] = 'true';
        return $this;
    }

    public function __toString()
    {
        $query_terms = '';
        foreach ($this->_terms as $k => $v) {
            $query_terms .= implode('', array_map(function(QueryTerm $query_term) use ($k) {
                return '&' . $k . '=' . urlencode($query_term->queryString());
            }, $v));
        }
        return http_build_query($this->_parameters) . $query_terms;
    }

    public function next($bulk_size = null)
    {
        $this->_parameters['indx'] += $this->_parameters['bulkSize'];

        if (null !== $bulk_size) {
            $this->_parameters['bulkSize'] = $bulk_size;
        }
    }
}