<?php

namespace BCLib\PrimoServices;

/**
 * @property Facet[]       $facets
 * @property BibRecord[]   $results
 * @property int           $total_results
 */
class BriefSearchResult implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_facets = array();
    private $_results = array();
    private $_total_results;

    public function filterFacets(array $facet_whitelist)
    {
        $fn = function ($facet) use ($facet_whitelist) {
            return in_array($facet->id, $facet_whitelist);
        };

        $this->_facets = array_values(array_filter($this->_facets, $fn));
    }
}