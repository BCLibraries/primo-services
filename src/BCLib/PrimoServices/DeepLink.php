<?php

namespace BCLib\PrimoServices;

class DeepLink
{
    protected $_host;
    protected $_institution;
    protected $_view_id;
    protected $_group;
    protected $_on_campus;
    protected $_language;

    public function __construct($host, $institution)
    {
        $this->_host = $host;
        $this->_institution = $institution;
    }

    public function view($view_id)
    {
        $this->_view_id = $view_id;
        return $this;
    }

    public function group($group)
    {
        $this->_group = $group;
        return $this;
    }

    public function onCampus($on_campus)
    {
        $this->_on_campus = $on_campus;
        return $this;
    }

    public function language($languange)
    {
        $this->_language = $languange;
    }

    public function search(QueryTerm $term)
    {
        $params['query'] = $term->queryString();
        return $this->_buildURL('dlSearch.do', $params);
    }

    public function link($id)
    {
        $params['docId'] = $id;
        return $this->_buildURL('dlDisplay.do', $params);
    }

    protected function _buildURL($action, array $params)
    {
        $params = array_merge($params, $this->_baseParams());
        $base = $this->_host . '/primo_library/libweb/action/' . $action;
        return $base . '?' . http_build_query($params);
    }

    protected function _baseParams()
    {
        return array(
            'vid'         => $this->_view_id,
            'institution' => $this->_institution,
            'group'       => $this->_group,
            'onCampus'    => $this->_on_campus,
            'lang'        => $this->_language
        );
    }
}