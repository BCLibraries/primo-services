<?php

namespace BCLib\PrimoServices;

class BriefSearchResult
{
    /**
     * @var Facet[]
     */
    public $facets = [];

    /**
     * @var BibRecord[]
     */
    public $results = [];

    /**
     * @var int
     */
    public $total_results;

    /**
     * @var string
     */
    public $dym;

    public function filterFacets(array $facet_whitelist)
    {
        $fn = function ($facet) use ($facet_whitelist) {
            return in_array($facet->id, $facet_whitelist, true);
        };

        $this->facets = array_values(array_filter($this->facets, $fn));
    }
}