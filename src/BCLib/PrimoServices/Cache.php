<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache as DoctrineCache;

class Cache
{
    private $query_ttl;
    private $record_ttl;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $doctrine_cache;

    /**
     * @param DoctrineCache $doctrine_cache
     * @param int           $query_ttl  How many seconds should a search be cached?.
     * @param int           $record_ttl How many seconds should a record be cached?.
     */
    public function __construct(DoctrineCache $doctrine_cache, $query_ttl = 300, $record_ttl = 1800)
    {
        $this->query_ttl = $query_ttl;
        $this->record_ttl = $record_ttl;
        $this->doctrine_cache = $doctrine_cache;
    }

    public function fetchQueryResult(Query $query)
    {
        return $this->checkAndFetch($this->queryKey($query));
    }

    public function fetchRecord($record_id)
    {
        return $this->checkAndFetch($this->recordKey($record_id));
    }

    public function saveQueryResult(Query $query, BriefSearchResult $result)
    {
        $this->doctrine_cache->save($this->queryKey($query), $result, $this->query_ttl);
    }

    public function saveRecord($record_id, BibRecord $record)
    {
        $this->doctrine_cache->save($this->recordKey($record_id), $record, $this->record_ttl);
    }

    private function checkAndFetch($key)
    {
        $value = false;
        if ($this->doctrine_cache->contains($key)) {
            $value = $this->doctrine_cache->fetch($key);
        }
        return $value;
    }

    private function queryKey(Query $query)
    {
        return 'primo-services-query-' . sha1((string) $query);
    }

    private function recordKey($record_id)
    {
        return 'primo-services-record-' . $record_id;
    }
}