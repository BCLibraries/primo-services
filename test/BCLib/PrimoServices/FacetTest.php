<?php

namespace BCLib\PrimoServices;

class FacetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Facet
     */
    protected $object;

    /**
     * @var array
     */
    protected $_values;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_values = [];

        $this->_values[0] = new \stdClass();
        $this->_values[0]->count = 5;
        $this->_values[0]->value = 'ant';
        $this->_values[0]->display_name = 'Ant';

        $this->_values[1] = new \stdClass();
        $this->_values[1]->count = 3;
        $this->_values[1]->value = 'aardvark';
        $this->_values[1]->display_name = 'Earth Pig';

        $this->_values[2] = new \stdClass();
        $this->_values[2]->count = 6;
        $this->_values[2]->value = 'beaver';
        $this->_values[2]->display_name = 'Beaver';

        $this->_values[3] = new \stdClass();
        $this->_values[3]->count = 1;
        $this->_values[3]->value = 'Camel';
        $this->_values[3]->display_name = 'camel';

        $this->object = new Facet;
        $this->object->values = $this->_values;
    }

    protected function tearDown()
    {
    }

    /**
     * @covers BCLib\LaravelHelpers\Facet::sortByFrequency()
     */
    public function testSortByFrequencyWorks()
    {
        $expected = [
            $this->_values[2],
            $this->_values[0],
            $this->_values[1],
            $this->_values[3]
        ];
        $this->object->sortByFrequency();
        $this->assertEquals($expected, $this->object->values);
    }

    /**
     * @covers BCLib\LaravelHelpers\Facet::sortAlphabetically()
     */
    public function testSortAlphabeticallyWorks()
    {
        $expected = [
            $this->_values[0],
            $this->_values[2],
            $this->_values[3],
            $this->_values[1]
        ];
        $this->object->sortAlphabetically();
        $this->assertEquals($expected, $this->object->values);
    }

    /**
     * @covers BCLib\LaravelHelpers\Facet::limit()
     */
    public function testLimitWorks()
    {
        $expected = [
            $this->_values[0],
            $this->_values[1],
            $this->_values[2]
        ];
        $this->object->limit(3);
        $this->assertEquals($expected, $this->object->values);
    }

    /**
     * @covers BCLib\LaravelHelpers\Facet::remap()
     */
    public function testRemapWorks()
    {
        $mapping_array = [
            'ant' => 'Foo',
            'aardvark' => 'Bar',
            'beaver' => 'Baz',
            'Camel' => 'FooBarBaz'
        ];

        $this->_values[0]->display_name = 'Foo';
        $this->_values[1]->display_name = 'Bar';
        $this->_values[2]->display_name = 'Baz';
        $this->_values[3]->display_name = 'FooBarBaz';
        $expected = $this->_values;
        $this->object->remap($mapping_array);
        $this->assertEquals($expected, $this->object->values);
    }

}
