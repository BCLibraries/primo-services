<?php


namespace BCLib\PrimoServices\Availability;

use BCLib\PrimoServices\BibRecord;
use Http\Mock\Client as MockClient;

class AlmaClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var  AlmaClient */
    protected $client;

    public function setUp()
    {
        $httpMock = new MockClient();
        $this->client = new AlmaClient($httpMock, 'http://library.example.edu', 'EXLIB');
    }

    public function testParseSingleSimpleRecordWorks()
    {
        $expected = new Availability();
        $expected->institution = '01BC_INST';
        $expected->library = 'ONL';
        $expected->library_display = "O'Neill";
        $expected->location = 'Stacks (STACK)';
        $expected->call_number = 'PR6069 .I413 O78 2004';
        $expected->availability = 'available';
        $expected->number = '1';
        $expected->number_unavailable = '0';
        $expected->j = 'STACK';
        $expected->multi_volume = '0';
        $expected->number_loans = '1';

        $result = $this->client->readRecord($this->loadTestRecord('availability-02.xml'));
        $this->assertEquals([$expected], $result);
    }

    public function testBuildComponentsHashWorks()
    {
        $bib_records = [new \stdClass(), new \stdClass(), new \stdClass()];

        $component1_info = [
            [
                'del_cat' => 'Alma-P$$OALMA-BC21360494740001021',
                'id'      => 'COMPONENT 1'
            ],
            [
                'del_cat' => 'Alma-E$$OALMA-BC51435784820001021',
                'id'      => 'COMPONENT 2'
            ]
        ];
        $bib_records[0]->components = array_map([$this, 'buildComponent'], $component1_info);

        $component2_info = [
            [
                'del_cat' => 'Alma-E',
                'id'      => 'COMPONENT 3'
            ]
        ];
        $bib_records[1]->components = array_map([$this, 'buildComponent'], $component2_info);

        $component3_info = [
            [
                'del_cat' => 'Alma-P',
                'id'      => 'COMPONENT 4'
            ]
        ];
        $bib_records[2]->components = array_map([$this, 'buildComponent'], $component3_info);

        $expected = [
            'COMPONENT 1' => new \stdClass(),
            'COMPONENT 4' => new \stdClass()
        ];

        $expected['COMPONENT 1']->delivery_category = 'Alma-P$$OALMA-BC21360494740001021';
        $expected['COMPONENT 1']->alma_ids = ['EXLIB' => 'COMPONENT 1'];

        $expected['COMPONENT 4']->delivery_category = 'Alma-P';
        $expected['COMPONENT 4']->alma_ids = ['EXLIB' => 'COMPONENT 4'];

        $bib_hash = iterator_to_array($this->client->buildComponentsHash($bib_records));
        $this->assertEquals($expected, $bib_hash);
    }

    private function buildComponent(array $component_info)
    {
        $component = new \stdClass();
        $component->delivery_category = $component_info['del_cat'];
        $component->alma_ids['EXLIB'] = $component_info['id'];
        return $component;
    }

    private function loadTestRecord($record_name)
    {
        $path_to_sample = __DIR__ . "/../../../helpers/alma/$record_name";
        $xml_string = file_get_contents($path_to_sample);
        return simplexml_load_string($xml_string);
    }
}