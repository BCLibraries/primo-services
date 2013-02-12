<?php

namespace BCLib\PrimoTools;

/**
 * Generate queries for Deep Links and XService requests
 */
class Query
{
    private $_fields = array();

    public function isbn($isbn)
    {
        $isbn = str_replace('%20', '', $isbn);
        $isbn = preg_replace('/( |\-)/', '', $isbn);
        return $this->_addQuery('isbn', 'contains', $isbn);
    }

    public function issn($issn = '')
    {
        $issn = str_replace('%20', ' ', $issn);
        $issn = preg_replace('/(\d\d\d\d)(.|)(\d\d\d\d)/', "$1+$3", $issn);
        return $this->_addQuery('issn', 'contains', $issn);
    }

    public function keyword($keyword)
    {
        return $this->_addQuery('any', 'contains', $keyword);
    }

    public function phrase($phrase)
    {
        return $this->_addQuery('any', 'exact', $phrase);
    }

    public function subject($subject)
    {
        return $this->_addQuery('sub', 'contains', $subject);
    }

    public function title($title)
    {
        return $this->_addQuery('title', 'contains', $title);
    }

    public function author($author)
    {
        return $this->_addQuery('creator','contains',$author);
    }

    public function oclcid($oclc_id)
    {
        return $this->_addQuery('lsr05', 'contains', $oclc_id);
    }

    public function collection($collection)
    {
        return $this->_addQuery('lsr30', 'contains', $collection);
    }

    private function _addQuery($field, $delimiter, $value)
    {

        $value = $this->_scrubTerm($value);

        if (isset ($this->_fields[$field]))
        {
            $this->_fields[$field]->values[] = $value;
        }
        else
        {
            $this->_fields[$field] = new \stdClass();
            $this->_fields[$field]->delimiter = $delimiter;
            $this->_fields[$field]->values = array($value);
        }

        return $this;
    }

    private function _queryString()
    {
        $queries = array();

        foreach ($this->_fields as $name => $parameters)
        {
            $queries[] = $this->_buildQuery($name, $parameters->delimiter, $parameters->values);
        }

        return implode('&', $queries);
    }

    private function _buildQuery($name, $delimiter, array $values)
    {
        return 'query=' . $name . ',' . $delimiter . ',' . implode('+OR+', $values);
    }

    private function _scrubTerm($term)
    {
        return str_replace(array('%2C', ',', ' '), '+', $term);
    }

    public function __toString()
    {
        return $this->_queryString();
    }

}