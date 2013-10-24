<?php

namespace BCLib\PrimoServices;

/**
 * Class BibRecord
 * @package BCLib\PrimoServices
 *
 * @property \SimpleXMLElement    $xml
 * @property string               $id
 * @property string               $title
 * @property Person               $creator
 * @property Person[]             $contributors
 * @property string               $date
 * @property string               $publisher
 * @property string               $abstract
 * @property string               $frbr_group_id
 * @property string               $type
 * @property string               $url
 * @property string               $availability
 * @property object               $cover_images
 * @property string               $isbn
 * @property string               $issn
 * @property string               $oclcid
 * @property string               $reserves_info
 * @property string[]             $subjects
 * @property string               $display_subject
 * @property string[]             $genres
 * @property string[]             $creator_facet
 * @property string[]             $collection_facet
 * @property string[]             $languages
 * @property string               $table_of_contents
 * @property string               $format
 * @property string               $description
 * @property string               $permalink
 * @property string               $find_it_url
 * @property string               $available_online_url
 * @property string               $link_to_worldcat
 * @property BibRecordComponent[] $components
 */
class BibRecord implements \JsonSerializable
{
    use Accessor, EncodeJson;

    /**
     * @var \SimpleXMLElement
     */
    private $_xml;

    private $_xml_literal;

    private $_id;
    private $_title;
    private $_creator;
    private $_contributors = array();
    private $_components = array();
    private $_date;
    private $_publisher;
    private $_abstract;
    private $_frbr_group_id;
    private $_type;
    private $_url;
    private $_availability;
    private $_isbn;
    private $_issn;
    private $_oclcid;
    private $_reserves_info;
    private $_subjects = array();
    private $_display_subject;
    private $_genres = array();
    private $_creator_facet = array();
    private $_collection_facet = array();
    private $_languages;
    private $_table_of_contents;
    private $_format;
    private $_description;
    private $_permalink;
    private $_holdings = array();
    private $_find_it_url;
    private $_available_online_url;
    private $_link_to_worldcat;

    private $person_template;
    private $bib_record_template;

    public function __construct(Person $person_template, BibRecordComponent $bib_record_component_template)
    {
        $this->person_template = $person_template;
        $this->bib_record_template = $bib_record_component_template;
    }

    public function addContributor(Person $contributor)
    {
        $this->_contributors[] = $contributor;
    }

    public function addSubject($subject)
    {
        $this->_subjects[] = $subject;
    }

    public function addGenre($genre)
    {
        $this->_genres[] = $genre;
    }

    public function addLanguages($language)
    {
        $this->_languages[] = $language;
    }

    public function addHoldings(Holding $holding)
    {
        $this->_holdings[] = $holding;
    }

    public function addComponent(BibRecordComponent $component)
    {
        $this->_components[] = $component;
    }

    private function _set_creator(Person $creator)
    {
        $this->_creator = $creator;
    }

    public function load(\SimpleXMLElement $xml)
    {
        $this->_id = (string) $xml->control->recordid;
        $this->_title = (string) $xml->display->title;
        $this->_date = (string) $xml->display->creationdate;
        $this->_publisher = (string) $xml->display->publisher;
        $this->_abstract = (string) $xml->addata->abstract;
        $this->_availability = (string) $xml->display->availpnx;
        $this->_issn = (string) $xml->search->issn;
        $this->_isbn = (string) $xml->search->isbn;
        $this->_oclcid = (string) $xml->addata->oclcid;
        $this->_type = (string) $xml->display->type;
        $this->_reserves_info = (string) $xml->addata->lad05;
        $this->_display_subject = (string) $xml->display->subject;
        $this->_format = (string) $xml->display->format;

        $this->contributors = $this->_extractContributors($xml);
        $this->subjects = $this->_extractFieldArray($xml, 'facets', 'topic');
        $this->genres = $this->_extractFieldArray($xml, 'facets', 'genre');
        $this->languages = $this->_extractFieldArray($xml, 'facets', 'language');
        $this->creator_facet = $this->_extractFieldArray($xml, 'facets', 'creatorcontrib');
        $this->collection_facet = $this->_extractFieldArray($xml, 'facets', 'lfc01');
        $this->components = $this->_extractComponents($xml);

        $this->creator = clone $this->person_template;
        $this->creator->display_name = (string) $xml->display->creator;
        $this->creator->first_name = (string) $xml->addata->aufirst;
        $this->creator->last_name = (string) $xml->addata->aulast;

        $this->table_of_contents = $this->_extractTableOfContents($xml);
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
            $component = clone $this->bib_record_template;

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

        if (strpos($this->id, 'dedup') > -1) {
            foreach ($element_xml as $element) {
                $element_parts = preg_split('/\$\$\w/', (string) $element);
                $result[$element_parts[2]] = $element_parts[1];
            }
        } else {
            $id = $element_xml->getName() == 'almaid' ? (string) $element_xml : $this->id;
            $result[$id] = (string) $element_xml;
        }

        return $result;
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
            $person = clone $this->person_template;
            $person->display_name = (string) $contributor;
            $contrib_list[] = $person;
        }
        return $contrib_list;
    }
}