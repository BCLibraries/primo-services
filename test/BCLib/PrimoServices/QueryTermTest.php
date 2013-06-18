<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florinb
 * Date: 4/9/13
 * Time: 4:28 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BCLib\PrimoServices;


class QueryTermTest extends \PHPUnit_Framework_TestCase
{
    /** @var QueryTerm */
    private $_query_term;

    public function setUp()
    {
        $this->_query_term = new QueryTerm();
    }

    public function testAddingGenericQueryTermSucceeds()
    {
        $expected = 'title,contains,Otters';
        $this->_query_term->set('title', 'contains', 'Otters');
        $this->assertEquals($expected, $this->_query_term->queryString());
    }

    /**
     * @expectedException \Exception
     */
    public function testBadPrecisionThrowsException()
    {
        $this->_query_term->set('title', 'not-a-precision', 'Otters');
    }

    public function testValidPrecisionTermsWork()
    {
        $this->_query_term->set('title', 'contains', 'Otters');
        $this->_query_term->set('title', 'exact', 'otters');
        $this->assertTrue(true);
    }

    public function testAddingKeywordSucceeds()
    {
        $expected = 'any,contains,Otters';
        $this->_query_term->keyword('Otters');
        $this->assertEquals($expected, $this->_query_term->queryString());
    }

    public function testAddingTitleSucceeds()
    {
        $expected = 'title,contains,Otters';
        $this->_query_term->title('Otters');
        $this->assertEquals($expected, $this->_query_term->queryString());
    }

    public function testAddingCreatorSucceeds()
    {
        $expected = 'creator,contains,Aesop';
        $this->_query_term->creator('Aesop');
        $this->assertEquals($expected, $this->_query_term->queryString());
    }
}
