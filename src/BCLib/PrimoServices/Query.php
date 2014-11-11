<?php

namespace BCLib\PrimoServices;

class Query
{
    private $_parameters = array();
    private $_terms = array();

    public function __construct($institution, $start_idx = 0, $bulk_size = 10)
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
        $this->_terms[] = 'query=' . urlencode($query_term->queryString());
        return $this;
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
            throw new \Exception($sort_order . ' is not a valid result sort');
        }

        $this->_parameters['sortField'] = $valid_sort_orders[$sort_order];
        return $this;
    }

    public function onCampus($on_campus = true)
    {
        if (!is_bool($on_campus)) {
            throw new \Exception('onCampus() must take a boolean argument');
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

    public function __toString()
    {
        $url = http_build_query($this->_parameters);
        $url .= (count($this->_terms) > 0) ? '&' . join('&', $this->_terms) : '';
        return $url;
    }

    public function next($bulk_size = null)
    {
        $this->_parameters['indx'] += $this->_parameters['bulkSize'];

        if (isset($bulk_size)) {
            $this->_parameters['bulkSize'] = $bulk_size;
        }
    }
}