<?php

namespace BCLib\PrimoServices;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $display_name
 */
class Person implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_first_name;
    private $_last_name;
    private $_display_name;
}