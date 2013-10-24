<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache;

class PNXTranslator
{

    private $_bib_record_template;
    private $_person_template;
    private $_bib_record_component_template;
    private $_record;

    /** @var \Doctrine\Common\Cache\Cache * */
    private $_cache;

    public function __construct(
        BibRecord $bib_record_template,
        Cache $cache = null
    ) {
        $this->_bib_record_template = $bib_record_template;
        $this->_cache = $cache;
    }

    /**
     * @param \SimpleXMLElement $doc_xml
     *
     * @return BibRecord[]
     */
    public function translate(\SimpleXMLElement $doc_xml)
    {
        $xpath_to_primo_record = '//sear:DOC/PrimoNMBib/record';
        $xpath_to_pci_record = '//sear:DOC/prim:PrimoNMBib/prim:record';

        $docs_xml = $doc_xml->xpath($xpath_to_primo_record . '|' . $xpath_to_pci_record);
        return \array_map(array($this, 'extractDoc'), $docs_xml);
    }

    public function extractDoc(\SimpleXMLElement $record_xml)
    {
        $record_xml = $record_xml->children('http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        /** @var $record BibRecord */
        $this->_record = clone $this->_bib_record_template;

        $this->_record->load($record_xml);

        if (isset($this->_cache)) {
            $cache_key = 'full-record-' . sha1($this->_record->id);;
            $this->_cache->save($cache_key, $this->_record, 1200);
        }

        return $this->_record;
    }

}