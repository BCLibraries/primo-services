<?php

namespace BCLib\PrimoServices\Availability;

use BCLib\PrimoServices\PrimoException;

/**
 * Class Availability
 * @package BCLib\PrimoServices\Availability
 *
 * @property string $availability
 */
class Availability implements \JsonSerializable
{
    public $institution;
    public $library;
    public $library_display;
    public $location;
    public $call_number;
    private $availability;
    public $number;
    public $number_unavailable;
    public $j;
    public $multi_volume;
    public $number_loans;


    public function __set($name, $value)
    {
        if ($name === 'availability') {
            $this->setAvailability($value);
        } else {
            throw new PrimoException("$name is not a property of Availability");
        }
    }

    public function __get($name)
    {
        if ($name === 'availability') {
            return $this->availability;
        } else {
            throw new PrimoException("$name is not a property of Availability");
        }
    }

    private function setAvailability($value)
    {
        if (!in_array($value, array('available', 'unavailable', 'check_holdings'), true)) {
            throw new PrimoException("Invalid availability status ($value)");
        }
        $this->availability = $value;
    }

    public function jsonSerialize()
    {
        return array(
            'institution'        => $this->institution,
            'library'            => $this->library,
            'location'           => $this->location,
            'call_number'        => $this->call_number,
            'availability'       => $this->availability,
            'number'             => $this->number,
            'number_unavailable' => $this->number_unavailable,
            'j'                  => $this->j,
            'multi_volume'       => $this->multi_volume,
            'number_loans'       => $this->number_loans,
        );
    }
}