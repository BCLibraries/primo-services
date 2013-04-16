<?php

namespace BCLib\PrimoServices;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query */
    protected $_query;

    public function setUp()
    {
        $this->_query = new Query('BCL');
    }

    public function testGeneratesCorrectURLWithoutAnyTerms()
    {
        $expected = 'institution=BCL&indx=0&bulkSize=10';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithOneQueryTerm()
    {
        $expected = 'institution=BCL&indx=0&bulkSize=10&query=any%2Ccontains%2Cotters';
        $query_term = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $query_term->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,otters'));
        $this->_query->addTerm($query_term);
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithMultipleQueryTerms()
    {

        $expected = 'institution=BCL&indx=0&bulkSize=10&query=any%2Ccontains%2Cotters&query=any%2Ccontains%2Cbears&query=any%2Ccontains%2Crabbits';

        $term_1 = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $term_2 = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $term_3 = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $term_1->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,otters'));
        $term_2->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,bears'));
        $term_3->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,rabbits'));
        $this->_query->addTerm($term_1);
        $this->_query->addTerm($term_2);
        $this->_query->addTerm($term_3);
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testSortFieldAddsCorrectly()
    {
        $expected = 'institution=BCL&indx=0&bulkSize=10&sortField=stitle';
        $this->_query->sortField('title');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=0&bulkSize=10&sortField=scdate';
        $this->_query->sortField('date');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=0&bulkSize=10&sortField=screator';
        $this->_query->sortField('author');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=0&bulkSize=10&sortField=popularity';
        $this->_query->sortField('popularity');
        $this->assertEquals($expected, (string) $this->_query);
    }

    /**
     * @expectedException \Exception
     */
    public function testBadSortFieldThrowsException()
    {
        $this->_query->sortField('not-a-sort');
    }

    public function testOnCampusAddedCorrectly()
    {
        $expected = 'institution=BCL&indx=0&bulkSize=10&onCampus=true';
        $this->_query->onCampus();
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=0&bulkSize=10&onCampus=false';
        $this->_query->onCampus(false);
        $this->assertEquals($expected, (string) $this->_query);
    }

    /**
     * @expectedException \Exception
     */
    public function testBadOnCampusInputThrowsException()
    {
        $this->_query->onCampus('true');
    }
}