<?php

namespace BCLib\PrimoServices;

class PrimoServices extends \Pimple
{
    private $_host;

    public function __construct($host)
    {
        $this->_host = $host;

        parent::__construct();

        $this['holding'] = function ()
        {
            return new PhysicalHolding();
        };

        $this['holding_factory'] = $this->protect(function ()
        {
            return $this['holding'];
        });

        $this['person'] = function ()
        {
            return new Person();
        };

        $this['person_factory'] = $this->protect(function ()
        {
            return $this['person'];
        });

        $this['bib_record_component'] = function ()
        {
            return new BibRecordComponent();
        };

        $this['bib_record_component_factory'] = $this->protect(function ()
        {
            return $this['bib_record_component'];
        });

        $this['bib_record'] = function ()
        {
            return new BibRecord();
        };

        $this['bib_record_factory'] = $this->protect(function ()
        {
            return $this['bib_record'];
        });

        $this['translator'] = function ()
        {
            return new PNXTranslator($this['bib_record_factory'],
                $this['holding_factory'],
                $this['person_factory'],
                $this['bib_record_component_factory']);
        };

        $this['query'] = function ()
        {
            return new Query($this->_institution);
        };

        $this['query_term'] = function ()
        {
            return new QueryTerm();
        };
    }

    public function ask(Query $query)
    {
        $url = 'http://' . $this->_host . '/PrimoWebServices/xservice/search/brief?' . $query;
        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $url,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $result = curl_exec($curl);

        /** @var $translator PNXTranslator */
        $translator = $this['translator'];
        return $translator->translate(simplexml_load_string($result));
    }
}