<?php

namespace BCLib\PrimoServices;

class QueryTerm
{
    private $_index;
    private $_precision;
    private $_term;

    const CONTAINS = 'contains';
    const EXACT = 'exact';
    const BEGINS = 'begins_with';

    public function keyword($keyword, $precision = QueryTerm::CONTAINS)
    {
        $this->set('any', $precision, $keyword);
    }

    public function title($title, $precision = QueryTerm::CONTAINS)
    {
        $this->set('title', $precision, $title);
    }

    public function creator($creator, $precision = QueryTerm::CONTAINS)
    {
        $this->set('creator', $precision, $creator);
    }

    public function subject($subject, $precision = QueryTerm::CONTAINS)
    {
        $this->set('sub', $precision, $subject);
    }

    public function date($date)
    {
        $this->set('cdate', QueryTerm::EXACT, $date);
    }

    public function isbn($isbn)
    {
        $this->set('isbn', QueryTerm::EXACT, $isbn);
    }

    public function issn($issn)
    {
        $this->set('issn', QueryTerm::EXACT, $issn);
    }

    public function queryString()
    {
        return $this->_index . ',' . $this->_precision . ',' . $this->_term;
    }

    protected function processTerm($term)
    {
        $term = str_replace(['+',','], ' ', $term);
        $term = preg_replace('/\s+/', ' ', $term);
        return $term;
    }

    public function set($index, $precision, $term)
    {
        if (is_array($term)) {
            $term = implode(',', array_map([$this, 'processTerm'], $term));
        } else {
            $term = $this->processTerm($term);
        }

        if ($precision !== QueryTerm::CONTAINS && $precision !== QueryTerm::EXACT) {
            throw new PrimoException($precision . ' is not a valid query relation');
        }

        $this->_index = $index;
        $this->_precision = $precision;
        $this->_term = $term;
    }
}