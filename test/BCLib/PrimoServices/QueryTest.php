<?php

namespace BCLib\PrimoServices;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query */
    protected $_query;

    public function setUp()
    {
        $this->_query = new Query('primo2.staging.hosted.exlibrisgroup.com', 'BCL');
    }

    public function testGeneratesCorrectURLWithoutAnyTerms()
    {
        $expected = 'http://primo2.staging.hosted.exlibrisgroup.com/PrimoWebServices/xservice/search/brief?institution=BCL&indx=0&bulkSize=10';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithOneQueryTerm()
    {
        $expected = 'http://primo2.staging.hosted.exlibrisgroup.com/PrimoWebServices/xservice/search/brief?institution=BCL&indx=0&bulkSize=10&query=any%2Ccontains%2Cotters';
        $query_term = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $query_term->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,otters'));
        $this->_query->addTerm($query_term);
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithMultipleQueryTerms()
    {

        $expected = 'http://primo2.staging.hosted.exlibrisgroup.com/PrimoWebServices/xservice/search/brief?institution=BCL&indx=0&bulkSize=10&query=any%2Ccontains%2Cotters&query=any%2Ccontains%2Cbears&query=any%2Ccontains%2Crabbits';

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

}