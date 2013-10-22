<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florinb
 * Date: 4/12/13
 * Time: 12:17 AM
 * To change this template use File | Settings | File Templates.
 */

namespace BCLib\PrimoServices;

/**
 * Class FacetValue
 * @package BCLib\PrimoServices
 *
 * @property string $value
 * @property string $display_name
 * @property string $count
 */
class FacetValue implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_value;
    private $_display_name;
    private $_count;

    private function _set_value($value)
    {
        $this->_value = $value;
        $this->_display_name = $value;
    }
}