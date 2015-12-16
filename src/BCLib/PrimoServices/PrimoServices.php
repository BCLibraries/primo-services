<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache as DoctrineCache;
use Guzzle\Http\Client;

class PrimoServices extends \Pimple
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
     * @param string        $host
     * @param string        $institution
     * @param DoctrineCache $cache
     * @param string        $version
     */
    public function __construct($host, $institution, DoctrineCache $cache = null, $version = "4.9")
    {
        $this->_host = $host;
        $this->_institution = $institution;
        if (is_null($cache)) {
            $this->_cache = new NullCache();
        } else {
            $this->_cache = new Cache($cache);
        }

        parent::__construct();

        $this['pnx_translator'] = function () use ($version) {
            return new PNXTranslator($version);
        };

        $this['facet_translator'] = function () use ($version)  {
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
     *
     * @return BriefSearchResult
     */
    public function search(Query $query, $facet_whitelist = array())
    {
        if ($cached_value = $this->_cache->fetchQueryResult($query)) {
            return $cached_value;
        }

        $sear = $this->_version === '4.7' || $this->_version === '4.8' ? 'sear:' : '';

        /* @var $response BriefSearchResult */
        $response = $this['search_result'];

        $json = $this->_send('brief', $query);
        if (is_null($json)) {
            // json_decode returns null on invalid JSON
            throw new PrimoException('Invalid or no response');
        }

        $result = $json->{$sear . 'SEGMENTS'}->{$sear . 'JAGROOT'}->{$sear . 'RESULT'};

        if (isset($result->{$sear . 'ERROR'}) && $result->{$sear.'ERROR'}->{$sear.'@CODE'} !='search.message.ui.expansion.pc') {
            throw new PrimoException($result->{$sear . 'ERROR'}->{'@MESSAGE'}, $result->{$sear . 'ERROR'}->{'@CODE'});
        }

        $docset = $result->{$sear . 'DOCSET'};
        $facetlist = $result->{$sear . 'FACETLIST'};

        if (null !== $result->{$sear . 'QUERYTRANSFORMS'}
            && null !== ($result->{$sear . 'QUERYTRANSFORMS'}->{$sear . 'QUERYTRANSFORM'}->{$sear . '@QUERY'})) {
            $response->dym = $result->{$sear . 'QUERYTRANSFORMS'}->{$sear . 'QUERYTRANSFORM'}->{$sear . '@QUERY'};
        }

        $response->total_results = $docset->{'@TOTALHITS'};

        if ($facetlist) {
            $response->facets = $this['facet_translator']->translate($facetlist);
        } else {
            $response->facets = array();
        }

        if ($response->total_results > 0) {
            $response->results = $this['pnx_translator']->translateDocSet($docset);
        } else {
            $response->results = array();
        }

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

        if (sizeof($response->total_results > 0)) {
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
    public function url($action, $query_string) {
        return 'http://' . $this->_host . '/PrimoWebServices/xservice/search/' . $action . '?json=true&' . $query_string;
    }

    protected function _send($action, $query_string)
    {
        $client = new Client();
        $request = $client->get($this->url($action, $query_string));
        return json_decode($request->send()->getBody());
    }
}