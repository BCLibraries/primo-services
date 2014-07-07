<?php

namespace BCLib\PrimoServices;

class PNXTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PNXTranslator */
    protected $_translator;

    public function setUp()
    {
        $this->_translator = new PNXTranslator();
    }

    public function testRecordIsLoaded()
    {
        $docset = $this->_loadTestRecord(__DIR__ . '/../../helpers/brief-search-result-local-01.json');

        $result = $this->_translator->translateDocSet($docset);
        $this->assertEquals(9, sizeof($result));

    }

    public function testLocalRecordLoadsCorrectly()
    {
        $docset = $this->_loadTestRecord(__DIR__ . '/../../helpers/brief-search-result-local-01.json');
        $result = $this->_translator->translateDocSet($docset);

        $result_0 = $result[0];
        $this->assertEquals('Otters', $result_0->title);
        $this->assertEquals('Kaite Goldsworthy', $result_0->creator->display_name);
        $this->assertEquals('Goldsworthy', $result_0->creator->last_name);
        $this->assertEquals('Kaite', $result_0->creator->first_name);
        $this->assertEquals(array("Otters–Juvenile literature"), $result_0->subjects);
        $this->assertEquals(array('Juvenile literature'), $result_0->genres);

        $result_1 = $result[1];
        $this->assertEquals(array('Guy Troughton'), $result_1->contributors);
        $this->assertEquals(
            array(
                "http://images.amazon.com/images/P/0905483901.01._SSTHUM_.jpg",
                "http://lib.syndetics.com/index.aspx?isbn=0905483901/SC.JPG&client=bostonh"
            ),
            $result_1->cover_images
        );
        $this->assertEquals(array('eng'), $result_1->languages);

        $result_3 = $result[3];
        $this->assertEquals('ALMA-BC51410148670001021', $result_3->id);
        $this->assertEquals(
            array(
                "Sea otter–Conservation–Pacific Coast (U.S.)",
                "Sea otter–Reintroduction–Pacific Coast (U.S.)",
                "Wildlife reintroduction–Pacific Coast (U.S.)"
            ),
            $result_3->subjects
        );
        $this->assertEquals(
            "prepared by u s fish and wildlife service ventura fish and wildlife office",
            $result_3->sort_creator
        );
        $this->assertEquals('2005', $result_3->sort_date);
        $this->assertEquals(
            "draft supplemental environmental impact statement translocation of southern sea otters",
            $result_3->sort_title
        );
        $this->assertEquals(
            array("http://alma.exlibrisgroup.com/view/uresolver/01BC_INST/openurl?ctx_enc=info:ofi/enc:UTF-8&ctx_id=10_1&ctx_tim=2014-06-25T18%3A54%3A20IST&ctx_ver=Z39.88-2004&url_ctx_fmt=info:ofi/fmt:kev:mtx:ctx&url_ver=Z39.88-2004&rfr_id=info:sid/primo.exlibrisgroup.com&req_id=&rft_val_fmt=info:ofi/fmt:kev:mtx:book&rft.genre=unknown&rft.atitle=&rft.jtitle=&rft.btitle=Draft%20supplemental%20environmental%20impact%20statement%20translocation%20of%20southern%20sea%20otters&rft.aulast=&rft.auinit=&rft.auinit1=&rft.auinitm=&rft.ausuffix=&rft.au=&rft.aucorp=&rft.volume=&rft.issue=&rft.part=&rft.quarter=&rft.ssn=&rft.spage=&rft.epage=&rft.pages=&rft.artnum=&rft.issn=&rft.eissn=&rft.isbn=&rft.sici=&rft.coden=&rft_id=info:doi/&rft.object_id=&rft_dat=<ALMA-BC>51410148670001021</ALMA-BC><grp_id>18072838</grp_id><oa></oa>&rft.eisbn=&rft.edition=&rft.pub=&rft.place=Ventura%2C%20Calif.&rft.series={{seriestitle}}&rft.stitle=&svc_dat=viewit&req.skin=BC%20skin"),
            $result_3->openurl
        );

        $result_4 = $result[4];
        $this->assertEquals(
            array(
                "What are sea otters? -- Body parts -- What sea otters do -- Under the sea -- Glossary -- Read more -- Internet sites -- Index.",
                "\"Summary: Simple text and photographs describe sea otters, their body parts, and what they do\"--Provided by publisher.",
                "Includes bibliographical references (p. 23) and index."
            ),
            $result_4->description
        );
        $this->assertEquals(array("9781429600347", "1429600349"), $result_4->isbn);
        $this->assertEquals(array('77485984'), $result_4->oclcid);
        $this->assertEquals('24 p. : col. ill. ; 24 x 29 cm.', $result_4->format);

        $result_5 = $result[5];
        $this->assertEquals(
            "Angry at being left behind when her father and brother go off to muster sheep, Alexa decides to search in the wild for the otters previously seen only by a mystical Maori tribesman.",
            $result_5->abstract
        );
    }

    protected function _loadTestRecord($path_to_sample)
    {
        $json = json_decode(file_get_contents($path_to_sample));
        return $json->{'sear:SEGMENTS'}->{'sear:JAGROOT'}->{'sear:RESULT'}->{'sear:DOCSET'};
    }
}
 