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

    public function checkAvailability(AvailibilityClient $client)
    {
        $availability_results = $client->checkAvailability($this->results);

        $ids = array_keys($availability_results);

        $all_components = array();

        foreach ($this->results as $result) {
            foreach ($result->components as $component) {
                $component_key = preg_replace('/\D/', '', $component->source_record_id);
                $all_components[$component_key] = $component;
            }
        }

        foreach ($ids as $id) {
            $all_components[$id]->availability = $availability_results[$id];
        }
    }
}