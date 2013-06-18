<?php

namespace BCLib\PrimoServices;

class FacetTranslator
{
    private $_facet_template;
    private $_facet_value_template;
    private $_facet_names = array();

    public function __construct(
        Facet $facet_template,
        FacetValue $facet_value_template,
        array $facet_names
    ) {
        $this->_facet_template = $facet_template;
        $this->_facet_value_template = $facet_value_template;
        $this->_facet_names = $facet_names;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Facet[]
     */
    public function translate(\SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $facets_xml = $xml->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:FACETLIST/sear:FACET');
        return \array_map(array($this, '_extractFacet'), $facets_xml);
    }

    /**
     * @param \SimpleXMLElement $facet_xml
     *
     * @return Facet
     */
    private function _extractFacet(\SimpleXMLElement $facet_xml)
    {
        $facet = clone $this->_facet_template;
        $facet->id = (string) $facet_xml['NAME'];
        $facet->name = (isset($this->_facet_names[$facet->id])) ? $this->_facet_names[$facet->id] : $facet->id;
        $facet->count = (string) $facet_xml['COUNT'];
        $facet_values_xml = $facet_xml->xpath('sear:FACET_VALUES');
        $facet->values = \array_map([$this, '_extractFacetValue'], $facet_values_xml);
        return $facet;
    }

    /**
     * Extracts a single facet value
     *
     * @param \SimpleXMLElement $facet_value_xml
     *
     * @return FacetValue
     */
    private function _extractFacetValue(\SimpleXMLElement $facet_value_xml)
    {
        $facet_value = clone $this->_facet_value_template;

        foreach ($facet_value_xml->attributes() as $key => $value) {
            switch ($key) {
                case 'KEY':
                    $facet_value->value = (string) $value;
                    break;
                case 'VALUE':
                    $facet_value->count = (string) $value;
            }

        }
        return $facet_value;
    }
}