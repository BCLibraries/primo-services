<?php

namespace BCLib\PrimoServices;

class PNXTranslator
{
    /**
     * @var string
     */
    private $_version;

    /**
     * @var string
     */
    private $_sear;

    public function __construct($version = "4.7")
    {
        $this->_version = $version;
        $this->_sear = ($version === '4.8' || $version === '4.7') ? "sear:" : '';
    }

    /**
     * Translate a set of PNX docs into bib records
     *
     * @param array $docset the "sear:DOCSET" array from a search/view result
     *
     * @return BibRecord[]
     */
    public function translateDocSet($docset)
    {
        $return = Array();

        if (is_array($docset->{$this->_sear . 'DOC'})) {
            foreach ($docset->{$this->_sear . 'DOC'} as $doc) {
                $return[] = $this->translateDoc($doc);
            }
        } else {
            $return[] = $this->translateDoc($docset->{$this->_sear . 'DOC'});
        }

        return $return;
    }

    /**
     * Translate a single PNX doc into a bib record
     *
     * @param \stdClass $doc a "sear:DOC" object from a search/view result
     *
     * @return BibRecord[]
     */
    public function translateDoc(\stdClass $doc)
    {
        $bib = new BibRecord($doc);

        $record = $doc->PrimoNMBib->record;
        $control = $record->control;
        $display = $record->display;
        $search = $record->search;
        $addata = $record->addata;
        $facets = $record->facets;
        $sort = $record->sort;
        $sear_links = $doc->{$this->_sear . 'LINKS'};

        $bib->id = $this->extractField($control, 'recordid');
        $bib->title = $this->extractField($display, 'title');
        $bib->date = $this->extractField($display, 'creationdate');
        $bib->publisher = $this->extractField($addata, 'pub');
        $bib->abstract = $this->extractField($addata, 'abstract');
        $bib->type = $this->extractField($display, 'type');
        $bib->isbn = $this->extractArray($search, 'isbn');
        $bib->issn = $this->extractArray($search, 'issn');
        $bib->oclcid = $this->extractArray($addata, 'oclcid');
        $bib->display_subject = $this->extractField($display, 'subject');
        $bib->format = $this->extractField($display, 'format');
        $bib->description = $this->extractArray($display, 'description');
        $bib->subjects = $this->extractArray($facets, 'topic');
        $bib->genres = $this->extractArray($facets, 'genre');
        $bib->languages = $this->extractArray($facets, 'language');
        $bib->contributors = $this->extractArray($display, 'contributor');
        $bib->cover_images = $this->extractArray($doc->{$this->_sear . 'LINKS'}, $this->_sear . 'thumbnail');

        $bib->creator = new Person();
        $bib->creator->display_name = $this->extractField($display, 'creator');
        $bib->creator->first_name = $this->extractField($addata, 'aufirst');
        $bib->creator->last_name = $this->extractField($addata, 'aulast');

        $bib->creator_facet = $this->extractArray($facets, 'creatorcontrib');
        $bib->collection_facet = $this->extractArray($facets, 'collection');
        $bib->resourcetype_facet = $this->extractArray($facets, 'rsrctype');

        $bib->link_to_source = $this->extractArray($sear_links, $this->_sear . 'linktosrc');

        $bib->sort_creator = $this->extractField($sort, 'author');
        $bib->sort_date = $this->extractField($sort, 'creationdate');
        $bib->sort_title = $this->extractField($sort, 'title');

        $bib->fulltext = $this->extractField($record->delivery, 'fulltext');

        // move to item level info
        $bib->openurl = $this->extractArray($sear_links, $this->_sear . 'openurl');
        $bib->openurl_fulltext = $this->extractArray($sear_links, $this->_sear . 'openurlfulltext');

        $holdings_translator = new BibComponentTranslator();

        $bib->components = $holdings_translator->translate($doc);

        $bib->getit = $this->extractGetIts($doc->{$this->_sear . 'GETIT'});

        $this->extractPNXGroups($record, $bib);

        return $bib;
    }

    private function extractGetIts($sear_getit)
    {
        if (!is_array($sear_getit)) {
            $sear_getit = array($sear_getit);
        }

        $result = \array_map(array($this, 'extractGetIt'), $sear_getit);

        return $result;
    }

    private function extractGetIt($sear_getit)
    {
        $getit = new GetIt();
        $getit->getit_1 = $sear_getit->{'@GetIt1'};
        $getit->getit_2 = $sear_getit->{'@GetIt2'};
        $getit->category = $sear_getit->{'@deliveryCategory'};
        return $getit;
    }

    private function extractPNXGroups(\stdClass $pnx_record, BibRecord $record)
    {
        $groups = array();
        foreach ($pnx_record as $group_name => $group) {
            if (!is_null($group)) {
                $this->extractGroupFields($group, $group_name, $record);
            }

        }
        return $groups;
    }

    private function extractGroupFields(\stdClass $pnx_group, $group_name, BibRecord $record)
    {
        $fields = array();
        foreach ($pnx_group as $field_name => $field) {
            $record->addField($group_name, $field_name, $field);
        }
        return $fields;
    }

    private function extractField(\stdClass $group, $field)
    {
        return isset($group->$field) ? $group->$field : null;
    }

    private function extractArray(\stdClass $group, $field)
    {
        $value = isset($group->$field) ? $group->$field : array();
        return (is_array($value)) ? $value : array($value);
    }
}