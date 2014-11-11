<?php

namespace BCLib\PrimoServices\Availability;

use BCLib\PrimoServices\BibRecord;
use Guzzle\Http\Client;

class AlmaClient implements AvailibilityClient
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string Alma host name (e.g. 'alma.exlibris.com')
     */
    private $alma_host;

    /**
     * @var string Alma library code (e.g. '01BC_INST')
     */
    private $library;

    public function __construct(Client $client, $alma_host, $library)
    {
        $this->client = $client;
        $this->alma_host = $alma_host;
        $this->library = $library;

        $this->ava_map = array(
            'a' => 'institution',
            'b' => 'library',
            'c' => 'location',
            'd' => 'call_number',
            'e' => 'availability',
            'f' => 'number',
            'g' => 'number_unavailable',
            'j' => 'j',
            'k' => 'multi_volume',
            'p' => 'number_loans'
        );
    }

    /**
     * @param \BCLib\PrimoServices\BibRecord[] $results
     * @return \BCLib\PrimoServices\BibRecord[]
     */
    public function checkAvailability(array $results)
    {
        $ids = array();
        foreach ($results as $record) {
            $ids[$record->id] = $this->getIDS($record);
        }
        $foo = $this->client->get($this->buildUrl($ids))->send();
        $availability_results = $this->readAvailability(simplexml_load_string($foo->getBody(true)));

        $ids = array_keys($availability_results);

        $all_components = array();

        foreach ($results as $result) {
            foreach ($result->components as $component) {
                $component_key = preg_replace('/\D/', '', $component->source_record_id);
                $all_components[$component_key] = $component;
            }
        }

        foreach ($ids as $id) {
            $all_components[$id]->availability = $availability_results[$id];
        }

        return $results;
    }

    private function getIDS(BibRecord $record)
    {
        $ids = array();
        foreach ($record->components as $component) {
            if ($component->delivery_category == 'Alma-P') {
                $ids[] = $component->alma_id;
            }
        }
        return $ids;
    }

    private function buildUrl($ids)
    {
        $flat_ids = array();
        foreach ($ids as $id) {
            foreach ($id as $component) {
                $parts = explode(':', $component);
                if (isset($parts[1])) {
                    $flat_ids[] = $parts[1];
                }
            }
        }
        $url = "http://" . $this->alma_host . "/view/publish_avail?doc_num=" . join(
                ',',
                $flat_ids
            ) . "&library=" . $this->library;
        return $url;
    }

    private function readAvailability(\SimpleXMLElement $xml)
    {
        $total = array();

        foreach ($xml->{'OAI-PMH'} as $oai) {
            $key_parts = explode(':', (string) $oai->ListRecords->record->header->identifier);
            $record_xml = simplexml_load_string($oai->ListRecords->record->metadata->record->asXml());
            if (isset($key_parts)) {
                $total[$key_parts[1]] = $this->readRecord($record_xml);
            }
        }
        return $total;
    }

    private function readRecord(\SimpleXMLElement $record_xml)
    {
        $record_response = array();
        $record_xml->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
        $avas = $record_xml->xpath('//slim:datafield[@tag="AVA"]');
        foreach ($avas as $ava) {
            $availability = new Availability();
            foreach ($ava->subfield as $sub) {
                $property = $this->ava_map[(string) $sub['code']];
                $availability->$property = (string) $sub;
            }
            $record_response[] = $availability;
        }
        return $record_response;
    }
}