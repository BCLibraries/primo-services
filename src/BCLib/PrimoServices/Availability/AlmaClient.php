<?php

namespace BCLib\PrimoServices\Availability;

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

    /**
     * @var Availability[]
     */
    private $all_components;

    /**
     * @var array
     */
    private $ava_map;

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
            'p' => 'number_loans',
            'q' => 'library_display'
        );
    }

    /**
     * @param \BCLib\PrimoServices\BibRecord[] $results
     * @return \BCLib\PrimoServices\BibRecord[]
     */
    public function checkAvailability(array $results)
    {
        $this->buildComponentsHash($results);
        $this->readAvailability();
        return $results;
    }

    private function buildUrl($ids)
    {
        $query = http_build_query(
            array(
                'doc_num' => implode(',', $ids),
                'library' => $this->library
            )
        );
        return "http://{$this->alma_host}/view/publish_avail?$query";
    }

    private function readAvailability()
    {
        $response = $this->client->get($this->buildUrl(array_keys($this->all_components)))->send();
        $xml = simplexml_load_string($response->getBody(true));

        foreach ($xml->{'OAI-PMH'} as $oai) {
            $key_parts = explode(':', (string) $oai->ListRecords->record->header->identifier);
            $record_xml = simplexml_load_string($oai->ListRecords->record->metadata->record->asXml());
            if (null !== $key_parts && array_key_exists(1, $key_parts)) {
                $this->all_components[$key_parts[1]]->availability = $this->readRecord($record_xml);
            }
        }

    }

    private function readRecord(\SimpleXMLElement $record_xml)
    {
        $record_response = array();
        $record_xml->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
        $avas = $record_xml->xpath('//slim:datafield[@tag="AVA"]');
        foreach ($avas as $ava) {
            $availability = new Availability();
            foreach ($ava->subfield as $sub) {
                if ($sub['code'] !== '0') {
                    if (isset($this->ava_map[(string) $sub['code']])) {
                        $property = $this->ava_map[(string) $sub['code']];
                        $availability->$property = (string) $sub;
                    }
                }
            }
            $record_response[] = $availability;
        }
        return $record_response;
    }

    private function buildComponentsHash(array $results)
    {
        $this->all_components = array();

        foreach ($results as $result) {
            foreach ($result->components as $component) {
                $delivery_category = explode('$$', $component->delivery_category);
                if ($delivery_category[0] === 'Alma-P' && isset($component->alma_ids[$this->library])) {
                    $alma_id = $component->alma_ids[$this->library];
                    $this->all_components[$alma_id] = $component;
                }
            }
        }
    }
}