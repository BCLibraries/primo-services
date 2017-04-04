<?php

namespace BCLib\PrimoServices\Availability;

use BCLib\PrimoServices\BibRecord;
use Guzzle\Http\Client;

class AlmaClient implements AvailabilityClient
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
     * @var array
     */
    private $ava_map;

    public function __construct(Client $client, $alma_host, $library)
    {
        $this->client = $client;
        $this->alma_host = $alma_host;
        $this->library = $library;

        $this->ava_map = [
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
        ];
    }

    /**
     * @param \BCLib\PrimoServices\BibRecord[] $bib_records
     * @return \BCLib\PrimoServices\BibRecord[]
     * @throws \Guzzle\Http\Exception\RequestException
     */
    public function checkAvailability(array $bib_records)
    {
        $components = iterator_to_array($this->buildComponentsHash($bib_records));
        $xml = $this->fetchAvailability($components);
        foreach ($this->readAvailability($xml) as $key => $availability) {
            $components[$key]->availability = $availability;
        }
        return $bib_records;
    }

    /**
     * Yields tuples of Alma ID => availability information
     *
     * @param $availability_xml
     * @return \Generator
     */
    public function readAvailability($availability_xml)
    {
        foreach ($availability_xml->{'OAI-PMH'} as $oai) {
            $key_parts = explode(':', (string) $oai->ListRecords->record->header->identifier);
            $record_xml = simplexml_load_string($oai->ListRecords->record->metadata->record->asXml());
            if (null !== $key_parts && array_key_exists(1, $key_parts)) {
                yield $key_parts[1] => $this->readRecord($record_xml);
            }
        }

    }

    private function buildUrl($ids)
    {
        $query = http_build_query(
            [
                'doc_num' => implode(',', $ids),
                'library' => $this->library
            ]
        );
        return "http://{$this->alma_host}/view/publish_avail?$query";
    }

    /**
     * Read a set of AVA records
     *
     * @param \SimpleXMLElement $record_xml
     * @return Availability[]
     */
    public function readRecord(\SimpleXMLElement $record_xml)
    {
        $record_xml->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
        $avas = $record_xml->xpath('//slim:datafield[@tag="AVA"]');
        return array_map([$this, 'readAVA'], $avas);
    }


    /**
     * Read an availability response's AVA records
     *
     * @param \SimpleXMLElement $ava_xml
     * @return Availability
     */
    private function readAVA(\SimpleXMLElement $ava_xml)
    {
        $availability = new Availability();
        foreach ($ava_xml->subfield as $sub) {
            $code = (string) $sub['code'];
            if (isset($this->ava_map[$code])) {
                $property = $this->ava_map[$code];
                $availability->$property = (string) $sub[0];
            }
        }
        return $availability;
    }

    /**
     * Generates an associative array of alma_ids pointing to components
     *
     * @param BibRecord[] $bib_records
     * @return \Generator
     */
    public function buildComponentsHash(array $bib_records)
    {
        foreach ($bib_records as $result) {
            foreach ($result->components as $component) {
                $delivery_category = explode('$$', $component->delivery_category);
                if ($delivery_category[0] === 'Alma-P' && isset($component->alma_ids[$this->library])) {
                    $alma_id = $component->alma_ids[$this->library];
                    yield $alma_id => $component;
                }
            }
        }
    }

    /**
     * @param array $components
     * @return \SimpleXMLElement
     */
    private function fetchAvailability(array $components)
    {
        $url = $this->buildUrl(array_keys($components));
        echo $url;
        $response = $this->client->get($url)->send();
        return simplexml_load_string($response->getBody(true));
    }
}