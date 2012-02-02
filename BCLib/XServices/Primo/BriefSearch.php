<?php

namespace BCLib\XServices\Primo;

class BriefSearch extends PrimoRequest
{

    public function __construct(BriefSearchTranslator $translator)
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/brief');
    }

    public function setFrbrGroupFacet($frbr_group_id)
    {
        $this->_setFacet('frbrgroupid', $frbr_group_id);
    }

    public function setResourceTypeFacet($resource_type)
    {
        $this->_setFacet('rtype', $resource_type);
    }

    public function setCreationDateFacet($start_year, $end_year)
    {
        $date_string = '[' . $start_year . '+TO+' . $end_year . ']';
        $this->_setFacet('creationdate', $date_string);
    }

    public function setCreatorFacet($creator)
    {
        $this->_setFacet('creator', $creator);
    }

    public function setTopicFacet($topic)
    {
        $this->_setFacet('topic', $topic);
    }

    public function setDomainFacet($domain)
    {
        $this->_setFacet('domain', $domain);
    }

    public function setAvailabilityFacet($availability)
    {
        $this->_setFacet('tlevel', $availability);
    }

    public function setLCCFacet($lcc)
    {
        $this->_setFacet('lcc', $lcc);
    }

    public function setLanguageFacet($language)
    {
        $this->_setFacet('lang', $language);
    }

    public function setPaging($bulk_size, $start_index)
    {
        $this->_addArgument('bulkSize', $bulk_size);
        $this->_addArgument('indx', $start_index);
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