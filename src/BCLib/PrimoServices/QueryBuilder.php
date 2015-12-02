<?php

namespace BCLib\PrimoServices;

class QueryBuilder
{
    /**
     * @var \BCLib\PrimoServices\Query
     */
    protected $_query;

    protected $_institution;

    public function __construct($institution)
    {
        $this->_institution = $institution;
        $this->_buildQuery();
    }

    public function getQuery()
    {
        $finished_query = $this->_query;
        $this->_buildQuery();
        return $finished_query;
    }

    public function keyword($keyword, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('keyword', $keyword, $precision);
    }

    public function title($title, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('title', $title, $precision);
    }

    public function creator($creator, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('creator', $creator, $precision);
    }

    public function subject($subject, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('subject', $subject, $precision);
    }

    public function date($date, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('date', $date, $precision);
    }

    public function isbn($isbn, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('isbn', $isbn, $precision);
    }

    public function issn($issn, $precision = QueryTerm::CONTAINS)
    {
        return $this->_addTerm('issn', $issn, $precision);
    }

    public function custom($index, $precision, $value)
    {
        $term = new QueryTerm();
        $term->set($index, $precision, $value);
        $this->_query->addTerm($term);
        return $this;
    }

    public function local($scope)
    {
        $this->_query->local($scope);
        return $this;
    }

    public function articles()
    {
        $this->_query->articles();
        return $this;
    }

    public function dym()
    {
        $this->_query->dym();
        return $this;
    }

    protected function _addTerm($name, $value, $precision)
    {
        $term = new QueryTerm();
        $term->$name($value, $precision);
        $this->_query->addTerm($term);
        return $this;
    }

    protected function _buildQuery()
    {
        $this->_query = new Query($this->_institution);
    }
} 