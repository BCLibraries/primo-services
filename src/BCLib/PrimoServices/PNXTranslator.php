<?php

namespace BCLib\PrimoServices;

use Doctrine\Common\Cache\Cache;

class PNXTranslator
{

    private $_bib_record_factory;
    private $_holding_factory;
    private $_person_factory;
    private $_bib_record_component_factory;
    private $_record;

    /** @var \Doctrine\Common\Cache\Cache * */
    private $_cache;

    public function __construct($bib_record_factory, $holding_factory,
                                $person_factory, $bib_record_component_factory,
                                Cache $cache)
    {
        $this->_bib_record_factory = $bib_record_factory;
        $this->_holding_factory = $holding_factory;
        $this->_person_factory = $person_factory;
        $this->_bib_record_component_factory = $bib_record_component_factory;
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
        return \array_map(array($this, '_extractDoc'), $docs_xml);
    }

    private function _extractDoc(\SimpleXMLElement $record_xml)
    {
        /** @var $record BibRecord */
        $this->_record = $record = $this->_bib_record_factory->__invoke();

        $this->_record = $record;
        $this->_record->id = (string) $record_xml->control->recordid;
        $this->_record->title = (string) $record_xml->display->title;
        $this->_record->date = (string) $record_xml->display->creationdate;
        $this->_record->publisher = (string) $record_xml->display->publisher;
        $this->_record->abstract = (string) $record_xml->addata->abstract;
        $this->_record->type = (string) $record_xml->display->type;
        $this->_record->availability = (string) $record_xml->display->availpnx;
        $this->_record->isbn = (string) $record_xml->search->isbn;
        $this->_record->issn = (string) $record_xml->search->issn;
        $this->_record->oclcid = (string) $record_xml->addata->oclcid;
        $this->_record->reserves_info = (string) $record_xml->addata->lad05;
        $this->_record->display_subject = (string) $record_xml->display->subject;
        $this->_record->format = (string) $record_xml->display->format;
        $this->_record->oclcid = (string) $record_xml->addata->oclcid;
        $this->_record->link_to_worldcat = $this->_linkToWorldCat($this->_record->oclcid);

        $this->_record->subjects = $this->_extractFieldArray($record_xml, 'facets', 'topic');
        $this->_record->genres = $this->_extractFieldArray($record_xml, 'facets', 'genre');
        $this->_record->languages = $this->_extractFieldArray($record_xml, 'facets', 'language');
        $this->_record->components = $this->_extractComponents($record_xml);

        $this->_cache->save($this->_record->id, $this->_record, 1200);

        return $this->_record;
    }

    private function _extractFieldArray(\SimpleXMLElement $xml, $section, $field)
    {
        $result = [];
        foreach ($xml->$section->$field as $item)
        {
            $result[] = (string) $item;
        }
        return $result;
    }

    private function _extractComponents($record_xml)
    {
        /** @var $components BibRecordComponent[] */
        $components = [];

        $helper = [];

        $helper['sourceid'] = $this->_extractMultiPartField($record_xml->control->sourceid);
        $helper['originalsourceid'] = $this->_extractMultiPartField($record_xml->control->originalsourceid);
        $helper['sourcerecordid'] = $this->_extractMultiPartField($record_xml->control->sourcerecordid);
        $helper['institution'] = $this->_extractMultiPartField($record_xml->delivery->institution);
        $helper['delcategory'] = $this->_extractMultiPartField($record_xml->delivery->delcategory);
        $helper['alma_id'] = $this->_extractMultiPartField($record_xml->control->almaid);

        /** @var $component BibRecordComponent */
        foreach ($helper['delcategory'] as $id => $delcategory)
        {
            $component = $this->_bib_record_component_factory->__invoke();

            $component->delivery_category = $delcategory;
            $component->source_record_id = $helper['sourcerecordid'][$id];

            $alma_id_key = str_replace('ALMA-BC','01BC_INST:', $id);
            $component->alma_id = $helper['alma_id'][$alma_id_key];
            $components[] = $component;
        }

        return $components;
    }

    private function _extractMultiPartField(\SimpleXMLElement $element_xml)
    {
        $result = [];

        if (strpos($this->_record->id, 'dedup') > -1)
        {
            foreach ($element_xml as $element)
            {
                $element_parts = preg_split('/\$\$\w/', (string) $element);
                $result[$element_parts[2]] = $element_parts[1];
            }
        }
        else
        {
            $result[$record->id] = (string) $element_xml;
        }

        return $result;
    }

    private function _linkToWorldCat($oclc_id)
    {
        return $oclc_id ? 'http://bc.worldcat.org/search?q=no:' . $oclc_id : '';
    }

}