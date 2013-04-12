<?php

namespace BCLib\PrimoServices;

class FacetTranslator
{
    private $_facet_factory;
    private $_facet_value_factory;

    public function __construct($facet_factory, $facet_value_factory)
    {
        $this->_facet_factory = $facet_factory;
        $this->_facet_value_factory = $facet_value_factory;
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
        $facet = $this->_facet_factory->__invoke();
        $facet->name = (string) $facet_xml['NAME'];
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
        $facet_value = $this->_facet_value_factory->__invoke();

        foreach ($facet_value_xml->attributes() as $key => $value)
        {
            switch ($key)
            {
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