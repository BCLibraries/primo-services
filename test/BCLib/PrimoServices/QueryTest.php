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
        $expected = 'institution=BCL&indx=1&bulkSize=10';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithOneQueryTerm()
    {
        $expected = 'institution=BCL&indx=1&bulkSize=10&query=any%2Ccontains%2Cotters';
        $query_term = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $query_term->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,otters'));
        $this->_query->addTerm($query_term);
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testGeneratesCorrectURLWithMultipleQueryTerms()
    {

        $expected = 'institution=BCL&indx=1&bulkSize=10&query=any%2Ccontains%2Cotters&query=any%2Ccontains%2Cbears&query=any%2Ccontains%2Crabbits';

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
        $expected = 'institution=BCL&indx=1&bulkSize=10&sortField=stitle';
        $this->_query->sortField('title');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=1&bulkSize=10&sortField=scdate';
        $this->_query->sortField('date');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=1&bulkSize=10&sortField=screator';
        $this->_query->sortField('author');
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=1&bulkSize=10&sortField=popularity';
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
        $expected = 'institution=BCL&indx=1&bulkSize=10&onCampus=true';
        $this->_query->onCampus();
        $this->assertEquals($expected, (string) $this->_query);

        $expected = 'institution=BCL&indx=1&bulkSize=10&onCampus=false';
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

    public function testArticleSearchSetCorrectly()
    {
        $this->_query->articles();
        $expected = 'institution=BCL&indx=1&bulkSize=10&loc=adaptor%2Cprimo_central_multiple_fe';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testLocalScopeSetCorrectly()
    {
        $this->_query->local('BCL');
        $expected = 'institution=BCL&indx=1&bulkSize=10&loc=local%2Cscope%3A%28BCL%29';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testStartIndexSet()
    {
        $this->_query->start(12);
        $expected = 'institution=BCL&indx=12&bulkSize=10';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testBulkSizeSet()
    {
        $this->_query->bulkSize(30);
        $expected = 'institution=BCL&indx=1&bulkSize=30';
        $this->assertEquals($expected, (string) $this->_query);
    }

    public function testInterfaceIsFluent()
    {
        $this->assertSame($this->_query->articles(), $this->_query);
        $this->assertSame($this->_query->start(12), $this->_query);
        $this->assertSame($this->_query->bulkSize(42), $this->_query);
        $this->assertSame($this->_query->sortField('title'), $this->_query);
        $this->assertSame($this->_query->onCampus(), $this->_query);

        $query_term = $this->getMock('\BCLib\PrimoServices\QueryTerm');
        $query_term->expects($this->once())
            ->method('queryString')
            ->will($this->returnValue('any,contains,otters'));
        $this->assertSame($this->_query->addTerm($query_term), $this->_query);
    }
}