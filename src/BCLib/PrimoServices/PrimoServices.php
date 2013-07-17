<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\Cache;

class PrimoServices extends \Pimple
{
    private $_host;
    private $_institution;
    private $_cache_enabled = true;

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

    public function __construct($host, $institution = 'BCL')
    {
        $this->_host = $host;
        $this->_institution = $institution;

        parent::__construct();

        $this['person'] = function () {
            return new Person();
        };

        $this['bib_record_component'] = function () {
            return new BibRecordComponent();
        };

        $this['bib_record'] = function () {
            return new BibRecord();
        };

        $this['pnx_translator'] = function () {
            return new PNXTranslator($this['bib_record'],
                $this['person'],
                $this['bib_record_component'],
                $this['apc_cache']);
        };

        $this['apc_cache'] = function () {
            return new ApcCache();
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
    }

    public function search(Query $query, $facet_whitelist = array())
    {
        /**
         * @var Cache $cache
         */
        $cache = $this['apc_cache'];
        $cache_key = sha1($query);
        if ($this->_cache_enabled && $cache->contains($cache_key)) {
            return $cache->fetch($cache_key);
        }

        $url = 'http://' . $this->_host . '/PrimoWebServices/xservice/search/brief?' . $query;
        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $url,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $xml = curl_exec($curl);

        $xml_result = simplexml_load_string($xml);
        $xml_result->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $xml_result->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        /* @var $result BriefSearchResult */
        $result = $this['search_result'];
        $result->facets = [];
        $result->results = [];

        $result->facets = $this['facet_translator']->translate($xml_result);
        $result->results = $this['pnx_translator']->translate($xml_result);

        $docset = $xml_result->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:DOCSET');
        $result->total_results = (string) $docset[0]['TOTALHITS'];

        if (count($facet_whitelist) > 0) {
            $result->filterFacets($facet_whitelist);
        }

        $cache->save($cache_key, $result, 120);

        return $result;
    }

    public function request($record_id)
    {
        $cache = $this['apc_cache'];
        $cache_key = 'full-record-' . sha1($record_id);
        if ($this->_cache_enabled && $cache->contains($cache_key)) {
            return $cache->fetch($cache_key);
        }

        $url = 'http://' . $this->_host . '/PrimoWebServices/xservice/search/full?docId=' . $record_id . '&institution=01_BCL';

        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $url,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $xml = curl_exec($curl);

        $xml_result = simplexml_load_string($xml);
        $xml_result->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $xml_result->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        $item_xml = $xml_result->JAGROOT->RESULT->DOCSET->DOC->PrimoNMBib->record;

        /* @var $result BibRecord */
        $result = $this['pnx_translator']->extractDoc($item_xml);

        $cache->save($cache_key, $result, 120);

        return $result;
    }

    public function cache($cache_enabled)
    {
        $this->_cache_enabled = $cache_enabled;
    }
}