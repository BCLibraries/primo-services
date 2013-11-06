<?php

namespace BCLib\PrimoServices;

class BibRecordTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var BibRecord
     */
    protected $_record;

    protected $_person_template;

    protected $_component_template;

    public function setUp()
    {
        $this->_person_template = \Mockery::mock('BCLib\PrimoServices\Person');
        $this->_component_template = \Mockery::mock('BCLib\PrimoServices\BibRecordComponent');
        $this->_record = new BibRecord($this->_person_template, $this->_component_template);
    }

    public function testLocalBriefSearchResult()
    {
        $sample_record = __DIR__ . '/../../helpers/single-record-brief-local-01.xml';
        $this->_record->load($this->_loadTestRecord($sample_record));

        $this->assertEquals('ALMA-BC21349370700001021', $this->_record->id);
        $this->assertEquals('Otters', $this->_record->title);
        $this->assertEquals('1993', $this->_record->date);
        $this->assertEquals('Whittet', $this->_record->publisher);
        $this->assertEquals('This is not a real abstract.', $this->_record->abstract);
        $this->assertEquals('book', $this->_record->type);
        $this->assertEquals('available', $this->_record->availability);
        $this->assertEquals('0905483901', $this->_record->isbn);
        $this->assertEquals('', $this->_record->issn);
        $this->assertEquals('28114226', $this->_record->oclcid);
        $this->assertEquals('Otters; Otters', $this->_record->display_subject);
        $this->assertEquals('127 p. : ill. ; 22 cm.', $this->_record->format);
        $this->assertEquals('This is a book about otters.', $this->_record->description);

        $contributors = array('with illustrations by Guy Troughton');
        $this->assertEquals($contributors, $this->_record->contributors);

        $subjects = array('Otters');
        $this->assertEquals($subjects, $this->_record->subjects);

        $genres = array();
        $this->assertEquals($genres, $this->_record->genres);

        $creator_facet = array(
            'Chanin, Paul'
        );
        $this->assertEquals($creator_facet, $this->_record->creator_facet);

        $collection_facet = array('ONL');
        $this->assertEquals($collection_facet, $this->_record->collection_facet);

        $languages = array('eng');
        $this->assertEquals($languages, $this->_record->languages);

        $component = $this->_record->components[0];
        $this->assertEquals('ALMA-BC', $component->source);
        $this->assertEquals('01BC_INST:21349370700001021', $component->alma_id);
        $this->assertEquals('Alma-P', $component->delivery_category);
        $this->assertEquals('21349370700001021', $component->source_record_id);
    }

    public function testPCIBriefSearchResult()
    {
        $sample_record = __DIR__ . '/../../helpers/single-record-brief-pci-01.xml';
        $this->_record->load($this->_loadTestRecord($sample_record));

        $this->assertEquals('TN_doaj32012733eca4b75eea7a729aec64d102', $this->_record->id);
        $this->assertEquals(
            'POPULISM IN LATIN AMERICA AND THE UNITED STATES. THE CASE OF THE TEA PARTY MOVEMENT',
            $this->_record->title
        );
        $this->assertEquals('2011', $this->_record->date);
        $this->assertEquals('Instituto Universitario ESEADE', $this->_record->publisher);
        $this->assertEquals(
            'One line abstract',
            $this->_record->abstract
        );
        $this->assertEquals('article', $this->_record->type);
        $this->assertEquals('', $this->_record->availability);
        $this->assertEquals('', $this->_record->isbn);
        $this->assertEquals('1851-1066', $this->_record->issn);
        $this->assertEquals('', $this->_record->oclcid);
        $this->assertEquals('Populism ; Tea Party ; Freedom ; United States', $this->_record->display_subject);
        $this->assertEquals('', $this->_record->format);
        $this->assertEquals('One line description', $this->_record->description);

        $contributors = array();
        $this->assertEquals($contributors, $this->_record->contributors);

        $subjects = array('Populism', 'Tea Party', 'Freedom', 'United States');
        $this->assertEquals($subjects, $this->_record->subjects);

        $genres = array();
        $this->assertEquals($genres, $this->_record->genres);

        $creator_facet = array('Darío Fernández-morera');
        $this->assertEquals($creator_facet, $this->_record->creator_facet);

        $collection_facet = array('Directory of Open Access Journals (DOAJ)');
        $this->assertEquals($collection_facet, $this->_record->collection_facet);

        $languages = array();
        $this->assertEquals($languages, $this->_record->languages);

        $component = $this->_record->components[0];
        $this->assertEquals('doaj', $component->source);
        $this->assertEquals('', $component->alma_id);
        $this->assertEquals('Remote Search Resource', $component->delivery_category);
        $this->assertEquals('32012733eca4b75eea7a729aec64d102', $component->source_record_id);
    }

    protected function _loadTestRecord($path_to_sample)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($path_to_sample));
        return $dom;
    }
}
 