<?php

namespace BCLib\LaravelHelpers;

/**
 * Class Input
 *
 * Replaces Laravel's input helper.
 *
 * @package BCLib\LaravelHelpers
 */
class Input
{
    private static $_contents = null;

    public static function get($name)
    {
        Input::_lazyLoad();
        return Input::$_contents[$name];
    }

    public static function has($name)
    {
        Input::_lazyLoad();
        return isset(Input::$_contents[$name]);
    }

    private static function _lazyLoad()
    {
        if (is_null(Input::$_contents)) {
            Input::_loadContents();
        }
    }

    private static function _loadContents()
    {
        $query = explode('&', $_SERVER['QUERY_STRING']);
        Input::$_contents = array();

        foreach ($query as $param) {
            list($name, $value) = explode('=', $param);
            Input::$_contents[urldecode($name)][] = urldecode($value);
        }
    }
}