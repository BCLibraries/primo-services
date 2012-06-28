<?php

namespace BCLib\XServices\Primo;

class BriefSearch extends PrimoRequest
{

    private $_bulk_size;
    private $_start_index;

    public function __construct(BriefSearchTranslator $translator, $host='agama.bc.edu', $port = '1701')
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/brief',$host, $port);
        return $this;
    }

    public function setFrbrGroupFacet($frbr_group_id)
    {
        $this->_setFacet('frbrgroupid', $frbr_group_id);
        return $this;
    }

    public function setResourceTypeFacet($resource_type)
    {
        $this->_setFacet('rtype', $resource_type);
        return $this;
    }

    public function setCreationDateFacet($start_year, $end_year)
    {
        $date_string = '[' . $start_year . '+TO+' . $end_year . ']';
        $this->_setFacet('creationdate', $date_string);
        return $this;
    }

    public function setCreatorFacet($creator)
    {
        $this->_setFacet('creator', $creator);
        return $this;
    }

    public function setTopicFacet($topic)
    {
        $this->_setFacet('topic', $topic);
        return $this;
    }

    public function setDomainFacet($domain)
    {
        $this->_setFacet('domain', $domain);
        return $this;
    }

    public function setAvailabilityFacet($availability)
    {
        $this->_setFacet('tlevel', $availability);
        return $this;
    }

    public function setLCCFacet($lcc)
    {
        $this->_setFacet('lcc', $lcc);
        return $this;
    }

    public function setLanguageFacet($language)
    {
        $this->_setFacet('lang', $language);
        return $this;
    }

    public function setKeyword($keyword)
    {
        $this->_addQuery('any', 'contains', $keyword);
        return $this;
    }
    
    public function setSubject($subject)
    {
        $this->_addQuery('sub', 'contains', $subject);
        return $this;
    }
    
    public function setISBN($isbn)
    {
        $this->_addQuery('isbn','exact',$isbn);
        return $this;
    }
    
    public function setPaging($bulk_size, $start_index)
    {
        $this->_addArgument('bulkSize', $bulk_size);
        $this->_addArgument('indx', $start_index);
        $this->_bulk_size = $bulk_size;
        $this->_start_index = $start_index;
        return $this;
    }

    public function setSection($section)
    {
        $this->_addQuery('lsr15','exact',$section);
    }

    /**
     * Generic facet setter
     * 
     * To be used until setting all facets can be encapsulated within the BriefSearch object. May
     * be privatized at any time, use outside the class at your own risk.
     * 
     * @param string $facet_name
     * @param string $term 
     */
    public function _setFacet($facet_name, $term)
    {
        $this->_addQuery('facet_' . $facet_name, 'exact', $term);
    }

}