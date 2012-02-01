<?php

namespace BCLib\XServices\Primo;

class BriefSearchTranslator implements \BCLib\XServices\Translator
{

    /** @var \BCLib\XServices\Primo\PNXTranslator * */
    private $_pnx_translator;

    public function __construct(PNXTranslator $pnx_translator = NULL)
    {
        if (is_null($pnx_translator))
        {
            $this->_pnx_translator = new PNXTranslator();
        }
        else
        {
            $this->_pnx_translator = $pnx_translator;
        }
    }

    public function translate(\SimpleXMLElement $xml)
    {
        $result = new \stdClass();

        $result->items = $this->_pnx_translator->translate($xml);

        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $facets_xml = $xml->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:FACETLIST/sear:FACET');
        $result->facets = \array_map(array($this, '_extractFacet'), $facets_xml);

        return $result;
    }

    /**
     * Extracts a single facet
     * 
     * @param \SimpleXMLElement $facet_xml
     * @return \stdClass 
     */
    private function _extractFacet(\SimpleXMLElement $facet_xml)
    {
        $facet = new \stdClass();
        $facet->name = (string) $facet_xml['NAME'];
        foreach ($facet_xml->children('sear', true) as $facet_value_xml)
        {
            $facet->values[] = $this->_extractFacetValue($facet_value_xml);
        }
        return $facet;
    }

    /**
     * Extracts a single facet value
     * 
     * @param \SimpleXMLElement $facet_value_xml
     * @return \stdClass 
     */
    private function _extractFacetValue(\SimpleXMLElement $facet_value_xml)
    {
        $facet_value = new \stdClass();
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