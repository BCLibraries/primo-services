<?php


namespace BCLib\PrimoServices\Availability;

class AlmaClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var  AlmaClient */
    protected $client;

    public function setUp()
    {
        $guzzle_mock = $this->getMock('Guzzle\Http\Client');
        $this->client = new AlmaClient($guzzle_mock, 'http://library.example.edu', 'EXLIB');
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

    private function loadTestRecord($record_name)
    {
        $path_to_sample = __DIR__ . "/../../../helpers/alma/$record_name";
        $xml_string = file_get_contents($path_to_sample);
        return simplexml_load_string($xml_string);
    }
}