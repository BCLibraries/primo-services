<?php

namespace BCLib\PrimoServices;

class FacetTranslator
{
    /**
     * @param string $version
     */
    public function __construct($version = '4.7')
    {
        $this->_sear = ($version === '4.8' || $version === '4.7') ? 'sear:' : '';
    }

    /**
     * @param array $sear_facetlist a 'FACETLIST' array
     *
     * @return Facet[]
     */
    public function translate($sear_facetlist)
    {
        return \array_map([$this, '_extractFacet'], $sear_facetlist->{$this->_sear . 'FACET'});
    }

    /**
     * @param \stdClass $sear_facet
     *
     * @return Facet
     */
    private function _extractFacet(\stdClass $sear_facet)
    {
        $facet = new Facet();

        $facet->name = $sear_facet->{'@NAME'};
        $facet->id = $sear_facet->{'@NAME'};
        $facet->count = $sear_facet->{'@COUNT'};

        $facet_values = $sear_facet->{$this->_sear . 'FACET_VALUES'};

        if (!is_array($facet_values)) {
            $facet_values = array($facet_values);
        }

        $facet->values = \array_map([$this, '_extractFacetValue'], $facet_values);

        return $facet;
    }

    /**
     * Extracts a single facet value
     *
     * @param \stdClass $sear_facet_value
     *
     * @return FacetValue
     */
    private function _extractFacetValue($sear_facet_value)
    {
        $facet_value = new FacetValue();
        $facet_value->value = $sear_facet_value->{'@KEY'};
        $facet_value->count = $sear_facet_value->{'@VALUE'};
        return $facet_value;
    }
}