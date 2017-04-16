<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache as DoctrineCache;
use Guzzle\Http\Client;
use Pimple\Container;

class PrimoServices extends Container
{
    private $_host;
    private $_institution;

    /**
     * @var Cache
     */
    private $_cache;
    /**
     * @var string
     */
    private $_version;
    /**
     * @var array
     */
    private $ignore_errors;

    /**
     * @param string        $host
     * @param string        $institution
     * @param DoctrineCache $cache
     * @param string        $version
     * @param array         $ignore_errors
     */
    public function __construct(
        $host,
        $institution,
        DoctrineCache $cache = null,
        $version = '4.9',
        array $ignore_errors = []
    ) {
        if (strpos($host, 'http://') === 0 || strpos($host, 'https://') === 0) {
            $this->_host = $host;
        } else {
            $this->_host = 'http://' . $host;
        }

        $this->_institution = $institution;
        if (null === $cache) {
            $this->_cache = new NullCache();
        } else {
            $this->_cache = new Cache($cache);
        }

        parent::__construct();

        $this['pnx_translator'] = function () use ($version) {
            return new PNXTranslator($version);
        };

        $this['facet_translator'] = function () use ($version) {
            return new FacetTranslator($version);
        };

        $this['query'] = function () use ($institution) {
            return new Query($institution);
        };

        $this['query_term'] = function () {
            return new QueryTerm();
        };

        $this['search_result'] = function () {
            return new BriefSearchResult();
        };

        $this['deep_link'] = function () use ($host, $institution) {
            return new DeepLink($host, $institution);
        };
        $this->_version = $version;

        if (empty($ignore_errors)) {
            $ignore_errors = [
                'search.message.ui.expansion.pc',
                'search.message.ui.expansion',
                'search.error.wildcards.toomanyclauses'
            ];
        }

        $this->ignore_errors = $ignore_errors;
    }

    /**
     * Set the cache
     *
     * @param Cache $cache
     */
    public function cache(Cache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Perform a search
     *
     * @param Query $query           the search query
     * @param array $facet_whitelist a list of facets to fetch
     * @return BriefSearchResult
     * @throws PrimoException
     */
    public function search(Query $query, array $facet_whitelist = [])
    {
        if ($cached_value = $this->_cache->fetchQueryResult($query)) {
            return $cached_value;
        }

        $sear = $this->_version === '4.7' || $this->_version === '4.8' ? 'sear:' : '';

        /* @var $response BriefSearchResult */
        $response = $this['search_result'];

        $json = $this->_send('brief', $query);
        if (null === $json) {
            // json_decode returns null on invalid JSON
            throw new PrimoException('Invalid or no response');
        }

        $result = $json->{$sear . 'SEGMENTS'}->{$sear . 'JAGROOT'}->{$sear . 'RESULT'};


        $this->checkErrors($sear, $result);

        $docset = $result->{$sear . 'DOCSET'};
        $facetlist = $result->{$sear . 'FACETLIST'};

        if (null !== $result->{$sear . 'QUERYTRANSFORMS'}
            && null !== $result->{$sear . 'QUERYTRANSFORMS'}->{$sear . 'QUERYTRANSFORM'}->{$sear . '@QUERY'}
        ) {
            $response->dym = $result->{$sear . 'QUERYTRANSFORMS'}->{$sear . 'QUERYTRANSFORM'}->{$sear . '@QUERY'};
        }

        $response->total_results = $docset->{'@TOTALHITS'};

        $response->facets = $facetlist ? $this['facet_translator']->translate($facetlist) : [];
        $response->results = $response->total_results > 0 ? $this['pnx_translator']->translateDocSet($docset) : [];

        if (count($facet_whitelist) > 0) {
            $response->filterFacets($facet_whitelist);
        }

        $this->_cache->saveQueryResult($query, $response);

        foreach ($response->results as $result) {
            $this->_cache->saveRecord($result->id, $result);
        }

        return $response;
    }

    /**
     * Request a single record
     *
     * @param $record_id
     *
     * @return BibRecord|null
     */
    public function request($record_id)
    {
        if ($this->_cache->fetchRecord($record_id)) {
            return $this->_cache->fetchRecord($record_id);
        }

        $builder = new QueryBuilder($this->_institution);
        $query = $builder->keyword($record_id)->getQuery();
        $response = $this->search($query);

        $record = null;

        if ($response->total_results > 0) {
            $record = $response->results[0];
            $this->_cache->saveRecord($record_id, $record);
        }

        return $record;
    }

    /**
     * Create a Deep Link to a search
     *
     * @return DeepLink
     */
    public function createDeepLink()
    {
        return $this['deep_link'];
    }

    /**
     * Generate a Primo API url
     *
     * @param $action
     * @param $query_string
     *
     * @return string
     */
    public function url($action, $query_string)
    {
        return "{$this->_host}/PrimoWebServices/xservice/search/$action?json=true&$query_string";
    }

    public function getHost() {
        return $this->_host;
    }

    public function getInstitution() {
        return $this->_institution;
    }

    protected function _send($action, $query_string)
    {
        $client = new Client();
        $request = $client->get($this->url($action, $query_string));
        return json_decode($request->send()->getBody());
    }

    /**
     * @param $sear
     * @param $result
     * @return void
     * @throws \BCLib\PrimoServices\PrimoException
     */
    private function checkErrors($sear, $result)
    {
        if (!isset($result->{$sear . 'ERROR'})) {
            return;
        }
        if (in_array(
            $result->{$sear . 'ERROR'}->{$sear . '@CODE'},
            $this->ignore_errors,
            true
        )) {
            return;
        }

        $message = $result->{$sear . 'ERROR'}->{$sear . '@CODE'} . " : " . $result->{$sear . 'ERROR'}->{$sear . '@MESSAGE'};
        throw new PrimoException($message);
    }
}