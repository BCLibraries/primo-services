<?php

namespace BCLib\PrimoServices;

use BCLib\PrimoServices\Availability\AvailibilityClient;

class BriefSearchResult
{
    /**
     * @var Facet[]
     */
    public $facets = array();

    /**
     * @var BibRecord[]
     */
    public $results = array();

    /**
     * @var int
     */
    public $total_results;

    public function filterFacets(array $facet_whitelist)
    {
        $fn = function ($facet) use ($facet_whitelist) {
            return in_array($facet->id, $facet_whitelist);
        };

        $this->facets = array_values(array_filter($this->facets, $fn));
    }
}