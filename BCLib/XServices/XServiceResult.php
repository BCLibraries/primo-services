<?php

namespace BCLib\XServices;

abstract class XServiceResult implements \IteratorAggregate, \ArrayAccess
{

    private $_results = array();

    public function getIterator()
    {
        return new \ArrayIterator($this->_results);
    }

    public function offsetExists($offset)
    {
        return isset($this->_results[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_results[$offset]) ? $this->_results[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->_results[] = $value;
        }
        else
        {
            $this->_results[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->_results[$offset]);
    }

    protected function _setResults(array $results)
    {
        $this->_results = $results;
    }

}