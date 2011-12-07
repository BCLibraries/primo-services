<?php

namespace BCLib\XServices\Primo;

class BriefSearchResult extends \BCLib\XServices\XServiceResult
{
    public function __construct()
    {
        $this->_setResults(array('foo','bar','foobar'));
    }
}