<?php

namespace BCLib\PrimoServices\Availability;

/**
 * Class Availability
 * @package BCLib\PrimoServices\Availability
 *
 * @property string $availability
 */
class Availability
{
    public $institution;
    public $library;
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
        if ($name = 'availability') {
            $this->setAvailability($value);
        }
    }

    private function setAvailability($value)
    {
        if (!in_array($value, array('available', 'unavailable', 'check_holdings'))) {
            throw new \Exception("Invalid availability status ($value)");
        }
        $this->availability = $value;
    }
}