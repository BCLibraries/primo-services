<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache;

class PNXTranslator
{

    /** @var \BCLib\PrimoServices\BibRecord */
    private $_bib_record_template;

    /** @var \Doctrine\Common\Cache\Cache */
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
        $dom = dom_import_simplexml($doc_xml)->ownerDocument;
        $xpath = new \DOMXPath($dom);


        $xpath->registerNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $xpath->registerNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        $xpath_to_primo_record = '//sear:DOC';
        $xpath_to_pci_record = '//sear:DOC';
        $docs_xml = $xpath->query("$xpath_to_pci_record|$xpath_to_primo_record");

        $records = [];

        foreach ($docs_xml as $doc_xml) {
            $bib_record = clone $this->_bib_record_template;

            $attr = $dom->createAttribute('xmlns:sear');
            $attr->value = 'http://www.exlibrisgroup.com/xsd/jaguar/search';
            $doc_xml->appendChild($attr);
            $record_doc = new \DOMDocument();
            $record_doc->loadXML($dom->saveXML($doc_xml));

            $bib_record->load($record_doc);

            $records[] = $bib_record;

            if (isset($this->_cache)) {
                $cache_key = 'full-record-' . sha1($bib_record->id);;
                $this->_cache->save($cache_key, $bib_record, 1200);
            }
        }
        return $records;
    }

    public function extractDoc(\SimpleXMLElement $xml)
    {
        $dom = dom_import_simplexml($xml)->ownerDocument;

        $bib_record = clone $this->_bib_record_template;

        $record_doc = new \DOMDocument();
        $record_doc->loadXML($dom->saveXML($xml));

        $bib_record->load($record_doc);

        $records[] = $bib_record;

        if (isset($this->_cache)) {
            $cache_key = 'full-record-' . sha1($bib_record->id);;
            $this->_cache->save($cache_key, $bib_record, 1200);
        }
    }
}