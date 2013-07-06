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
        Person $person_template,
        BibRecordComponent $bib_record_component_template,
        Cache $cache
    ) {
        $this->_bib_record_template = $bib_record_template;
        $this->_person_template = $person_template;
        $this->_bib_record_component_template = $bib_record_component_template;
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
        /** @var $record BibRecord */
        $this->_record = clone $this->_bib_record_template;

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

        $this->_record->contributors = $this->_extractContributors($record_xml);
        $this->_record->subjects = $this->_extractFieldArray($record_xml, 'facets', 'topic');
        $this->_record->genres = $this->_extractFieldArray($record_xml, 'facets', 'genre');
        $this->_record->languages = $this->_extractFieldArray($record_xml, 'facets', 'language');
        $this->_record->creator_facet = $this->_extractFieldArray($record_xml, 'facets', 'creatorcontrib');
        $this->_record->collection_facet = $this->_extractFieldArray($record_xml, 'facets', 'lfc01');
        $this->_record->components = $this->_extractComponents($record_xml);

        $this->_record->creator = clone $this->_person_template;
        $this->_record->creator->display_name = (string) $record_xml->display->creator;
        $this->_record->creator->first_name = (string) $record_xml->addata->aufirst;
        $this->_record->creator->last_name = (string) $record_xml->addata->aulast;

        $this->_record->table_of_contents = $this->_extractTableOfContents($record_xml);

        if ($this->_record->isbn) {
            $this->_record->cover_images = $this->_extractCoverImage($this->_record->isbn);
        }

        $cache_key = 'full-record-' . sha1($this->_record->id);;

        $this->_cache->save($cache_key, $this->_record, 1200);

        return $this->_record;
    }

    private function _extractFieldArray(\SimpleXMLElement $xml, $section, $field)
    {
        $result = [];
        foreach ($xml->$section->$field as $item) {
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
        foreach ($helper['delcategory'] as $id => $delcategory) {
            $component = clone $this->_bib_record_component_template;

            $component->delivery_category = $delcategory;
            $component->source_record_id = $helper['sourcerecordid'][$id];
            $component->source = $helper['sourceid'][$id];

            if ((string) $component->source == 'ALMA-BC') {
                $alma_id_key = str_replace('ALMA-BC', '01BC_INST:', $id);
                $component->alma_id = $helper['alma_id'][$alma_id_key];
            }
            $components[] = $component;
        }

        return $components;
    }

    private function _extractMultiPartField(\SimpleXMLElement $element_xml)
    {
        $result = [];

        if (strpos($this->_record->id, 'dedup') > -1) {
            foreach ($element_xml as $element) {
                $element_parts = preg_split('/\$\$\w/', (string) $element);
                $result[$element_parts[2]] = $element_parts[1];
            }
        } else {
            $id = $element_xml->getName() == 'almaid' ? (string) $element_xml : $this->_record->id;
            $result[$id] = (string) $element_xml;
        }

        return $result;
    }

    private function _linkToWorldCat($oclc_id)
    {
        return $oclc_id ? 'http://bc.worldcat.org/search?q=no:' . $oclc_id : '';
    }

    private function _extractCoverImage($isbn)
    {
        $cover_images = null;
        if ($isbn) {
            $cover_images = new \stdClass();
            $image_base_url = 'http://lib.syndetics.com/index.aspx?client=bostonh&isbn=';
            $cover_images->small = $image_base_url . $isbn . '/' . 'sc' . '.JPG';
            $cover_images->medium = $image_base_url . $isbn . '/' . 'mc' . '.JPG';
            $cover_images->large = $image_base_url . $isbn . '/' . 'lc' . '.JPG';
        }

        return $cover_images;
    }

    private function _extractTableOfContents(\SimpleXMLElement $record_xml)
    {
        if ((string) $record_xml->display->lds13) {
            return preg_split('/\s*\-\-\s*/', $record_xml->display->lds13);
        } else {
            return [];
        }
    }

    private function _extractContributors(\SimpleXMLElement $record_xml)
    {
        $contrib_list = [];
        foreach ($record_xml->display->contributor as $contributor) {
            $person = clone $this->_person_template;
            $person->display_name = (string) $contributor;
            $contrib_list[] = $person;
        }
        return $contrib_list;
    }
}