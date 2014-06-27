<?php

namespace BCLib\PrimoServices;

class BriefSearchResult
{
    public $facets = array();
    public $results = array();
    public $total_results;

    public function filterFacets(array $facet_whitelist)
    {
        $fn = function ($facet) use ($facet_whitelist) {
            return in_array($facet->id, $facet_whitelist);
        };

        $this->facets = array_values(array_filter($this->facets, $fn));
    }
}