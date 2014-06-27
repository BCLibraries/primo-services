<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache;
use Guzzle\Http\Client;

class PrimoServices extends \Pimple
{
    private $_host;
    private $_institution;

    /**
     * @var Cache
     */
    private $_cache;

    const CACHE_TYPE_APC = 1;

    private $_facet_names = [
        'creator'      => 'Creator',
        'lang'         => 'Language',
        'rtype'        => 'Type',
        'topic'        => 'Topic',
        'tlevel'       => 'Availablility',
        'pfilter'      => 'Prefilter?',
        'creationdate' => 'Date',
        'genre'        => 'Genre',
        'library'      => 'Library',
        'local1'       => 'Collection',
    ];

    public function __construct($host, $institution, Cache $cache = null)
    {
        $this->_host = $host;
        $this->_institution = $institution;
        $this->_cache = $cache;

        parent::__construct();

        $this['pnx_translator'] = function () {
            return new PNXTranslator();
        };

        $this['facet_translator'] = function () {
            return new FacetTranslator($this['facet'], $this['facet_value'], $this->_facet_names);
        };

        $this['facet'] = function () {
            return new Facet();
        };

        $this['facet_value'] = function () {
            return new FacetValue();
        };

        $this['query'] = function () {
            return new Query($this->_institution);
        };

        $this['query_term'] = function () {
            return new QueryTerm();
        };

        $this['search_result'] = function () {
            return new BriefSearchResult();
        };

        $this['deep_link'] = function () {
            return new DeepLink($this->_host, $this->_institution);
        };
    }

    public function search(Query $query, $facet_whitelist = array())
    {
        $cache_key = sha1($query);

        if ($cached_value = $this->_checkCache($cache_key)) {
            return $cached_value;
        }

        $json = $this->_send('brief', $query);

        $docset = $json->{'sear:SEGMENTS'}->{'sear:JAGROOT'}->{'sear:RESULT'}->{'sear:DOCSET'};
        $facetlist = $json->{'sear:SEGMENTS'}->{'sear:JAGROOT'}->{'sear:RESULT'}->{'sear:FACETLIST'};

        /* @var $response BriefSearchResult */
        $response = $this['search_result'];
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

        if (isset($this->_cache)) {
            $this->_cache->save($cache_key, $response, 60);
        }

        return $response;
    }

    public function request($record_id)
    {
        $builder = new QueryBuilder($this->_institution);
        $query = $builder->keyword($record_id)
            ->getQuery();
        $response = $this->search($query);

        if (sizeof($response->total_results > 0)) {
            return $response->results[0];
        } else {
            return null;
        }
    }

    /**
     * @return DeepLink
     */
    public function createDeepLink()
    {
        return $this['deep_link'];
    }

    protected function _checkCache($key)
    {
        if (isset($this->_cache) && $this->_cache->contains($key)) {
            return $this->_cache->fetch($key);
        } else {
            return false;
        }
    }

    protected function _send($action, $query_string)
    {
        $client = new Client('http://' . $this->_host . '/PrimoWebServices/xservice/search/');
        $request = $client->get($action . '?json=true&' . $query_string);
        return json_decode($request->send()->getBody());
    }
}