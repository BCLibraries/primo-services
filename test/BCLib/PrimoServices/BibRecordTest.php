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
        $this->assertEquals('http://alma.exlibrisgroup.com/view/uresolver/01BC_INST/openurl?ctx_enc=info:ofi/enc:UTF-8&ctx_id=10_1&ctx_tim=2013-11-05T22%3A40%3A22IST&ctx_ver=Z39.88-2004&url_ctx_fmt=info:ofi/fmt:kev:mtx:ctx&url_ver=Z39.88-2004&rfr_id=info:sid/primo.exlibrisgroup.com&req_id=&rft_val_fmt=info:ofi/fmt:kev:mtx:book&rft.genre=book&rft.atitle=&rft.jtitle=&rft.btitle=Otters&rft.aulast=Chanin&rft.auinit=&rft.auinit1=&rft.auinitm=&rft.ausuffix=&rft.au=Chanin%2C%20Paul&rft.aucorp=&rft.volume=&rft.issue=&rft.part=&rft.quarter=&rft.ssn=&rft.spage=&rft.epage=&rft.pages=&rft.artnum=&rft.issn=&rft.eissn=&rft.isbn=0905483901&rft.sici=&rft.coden=&rft_id=info:doi/&rft.object_id=&rft_dat=<ALMA-BC>21349370700001021</ALMA-BC>&rft.eisbn=&rft.edition=&rft.pub=&rft.place=London&rft.series={{seriestitle}}&rft.stitle=&svc_dat=viewit&req.skin=BC%20skin', $this->_record->openurl);
        $this->assertEquals('http://alma.exlibrisgroup.com/view/uresolver/01BC_INST/openurl?ctx_enc=info:ofi/enc:UTF-8&ctx_id=10_1&ctx_tim=2013-11-05T22%3A40%3A22IST&ctx_ver=Z39.88-2004&url_ctx_fmt=info:ofi/fmt:kev:mtx:ctx&url_ver=Z39.88-2004&rfr_id=info:sid/primo.exlibrisgroup.com&req_id=&rft_val_fmt=info:ofi/fmt:kev:mtx:book&rft.genre=book&rft.atitle=&rft.jtitle=&rft.btitle=Otters&rft.aulast=Chanin&rft.auinit=&rft.auinit1=&rft.auinitm=&rft.ausuffix=&rft.au=Chanin%2C%20Paul&rft.aucorp=&rft.volume=&rft.issue=&rft.part=&rft.quarter=&rft.ssn=&rft.spage=&rft.epage=&rft.pages=&rft.artnum=&rft.issn=&rft.eissn=&rft.isbn=0905483901&rft.sici=&rft.coden=&rft_id=info:doi/&rft.object_id=&rft_dat=<ALMA-BC>21349370700001021</ALMA-BC>&rft.eisbn=&rft.edition=&rft.pub=&rft.place=London&rft.series={{seriestitle}}&rft.stitle=&rft.bici=&rft_id=info:bibcode/&rft_id=info:hdl/&rft_id=info:lccn/gb%2093023867&rft_id=info:oclcnum/28114226&rft_id=info:pmid/&rft_id=info:eric/((addata/eric}}&rft_data=ie=01BC_INST:21349370700001021,language=&svc_dat=viewit&req.skin=BC%20skin', $this->_record->openurl_fulltext);

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

        $cover_images = array(
            'http://images.amazon.com/images/P/0905483901.01._SSTHUM_.jpg',
            'http://lib.syndetics.com/index.aspx?isbn=0905483901/SC.JPG&client=bostoncollege'
        );
        $this->assertEquals($cover_images, $this->_record->cover_images);

        $component = $this->_record->components[0];
        $this->assertEquals('ALMA-BC', $component->source);
        $this->assertEquals('01BC_INST:21349370700001021', $component->alma_id);
        $this->assertEquals('Alma-P', $component->delivery_category);
        $this->assertEquals('21349370700001021', $component->source_record_id);

        $call_numbers = array('QL737.C25 C44 1993');
        $this->assertEquals($call_numbers, $this->_record->field('//prim:display/prim:lds10'));

        $getit = array(
            'http://alma.exlibrisgroup.com/view/uresolver/01BC_INST/openurl?ctx_enc=info:ofi/enc:UTF-8&ctx_id=10_1&ctx_tim=2013-11-05T22%3A40%3A22IST&ctx_ver=Z39.88-2004&url_ctx_fmt=info:ofi/fmt:kev:mtx:ctx&url_ver=Z39.88-2004&rfr_id=info:sid/primo.exlibrisgroup.com-ALMA-BC&req_id=&rft_dat=ie=01BC_INST:21421261320001021,language=,view=&svc_dat=getit&user_ip=',
            ''
        );
        $this->assertEquals($getit, $this->_record->getit);
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

        $this->assertEquals(
            'http://www.eseade.edu.ar/riim/libertas/riim-n-55-octubre-2011.html',
            $this->_record->link_to_source
        );

        $this->assertEquals('errorPage', $this->_record->openurl);
        $this->assertEquals('errorPage', $this->_record->openurl_fulltext);
    }

    protected function _loadTestRecord($path_to_sample)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($path_to_sample));
        return $dom;
    }
}
 