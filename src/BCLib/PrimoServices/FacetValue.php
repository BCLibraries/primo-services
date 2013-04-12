<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florinb
 * Date: 4/12/13
 * Time: 12:17 AM
 * To change this template use File | Settings | File Templates.
 */

namespace BCLib\PrimoServices;


class FacetValue implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_value;
    private $_count;
}