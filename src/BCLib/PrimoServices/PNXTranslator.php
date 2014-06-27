<?php

namespace BCLib\PrimoServices;

class PNXTranslator
{
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

        if (is_array($docset->{'sear:DOC'})) {
            foreach ($docset->{'sear:DOC'} as $doc) {
                $return[] = $this->translateDoc($doc);
            }
        } else {
            $return[] = $this->translateDoc($docset->{'sear:DOC'});
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
        $bib = new BibRecord();

        $record = $doc->PrimoNMBib->record;
        $control = $record->control;
        $display = $record->display;
        $search = $record->search;
        $addata = $record->addata;
        $facets = $record->facets;
        $sort = $record->sort;
        $sear_links = $doc->{'sear:LINKS'};

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
        $bib->cover_images = $this->extractArray($doc->{'sear:LINKS'}, 'sear:thumbnail');

        $bib->creator_facet = $this->extractArray($facets, 'creatorcontrib');
        $bib->collection_facet = $this->extractArray($facets, 'collection');

        $bib->link_to_source = $this->extractArray($sear_links, 'sear:linktosrc');

        $bib->sort_creator = $this->extractField($sort, 'author');
        $bib->sort_date = $this->extractField($sort, 'date');
        $bib->sort_title = $this->extractField($sort, 'title');

        $bib->fulltext = $this->extractField($record->delivery, 'fulltext');

        // move to item level info
        //$bib->openurl = $this->extractField($sear_links, 'sear:openurl');
        //$bib->openurl_fulltext = $this->extractField($sear_links, 'sear:openurlfulltext');

        $holdings_translator = new BibComponentTranslator();

        $bib->components = $holdings_translator->translate($doc);

        return $bib;
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