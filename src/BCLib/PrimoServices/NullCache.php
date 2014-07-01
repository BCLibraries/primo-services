<?php

namespace BCLib\PrimoServices;

class NullCache extends Cache
{
    public function __construct() { }

    public function fetchQueryResult(Query $query)
    {
        return false;
    }

    public function fetchRecord($record_id)
    {
        return false;
    }

    public function saveQueryResult(Query $query, BriefSearchResult $result)
    {
        // no-op
    }

    public function saveRecord($record_id, BibRecord $record)
    {
        // no-op
    }

} 