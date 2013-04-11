<?php

namespace BCLib\PrimoServices;

class PrimoServices extends \Pimple
{
    public function __construct()
    {
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
    }

    public function ask()
    {
        return $this['bib_record'];
    }
}