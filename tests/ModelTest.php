<?php

namespace Ronanchilvers\Db\Test;

use PDO;
use PHPUnit\Framework\Error\Error;
use Ronanchilvers\Db\Model;
use Ronanchilvers\Db\Model\Metadata;
use Ronanchilvers\Db\QueryBuilder;
use Ronanchilvers\Db\Test\TestCase;
use RuntimeException;

/**
 * Test suite for the model class
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class ModelTest extends TestCase
{
    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setUp()
    {
        Model::setPdo($this->mockPDO());
    }

    /**
     * Get a new test instance
     *
     * @
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function newInstance()
    {
        return new class () extends Model {};
    }

    /**
     * Test that model can return a metadata instance
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testCanGetMetadataInstance()
    {
        $instance = $this->newInstance();
        $result = $instance->metaData();

        $this->assertInstanceof(Metadata::class, $result);
    }

    /**
     * Test that model can return a new query builder
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testCanGetQueryBuilder()
    {
        $instance = $this->newInstance();
        $result = $instance->newQueryBuilder();

        $this->assertInstanceof(QueryBuilder::class, $result);
    }

    /**
     * Test that a column value can be set for a valid column
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testColumnCanBeSetForInvalidColumn()
    {
        $mockMetadata = $this->createMock(Metadata::class);
        $mockMetadata
            ->expects($this->exactly(2))
            ->method('prefix')
            ->with('field_1')
            ->willReturn('field_1')
            ;
        $mockMetadata
            ->expects($this->once())
            ->method('hasColumn')
            ->with('field_1')
            ->willReturn(true);
        $mockMetadata
            ->expects($this->once())
            ->method('primaryKey')
            ->willReturn('id');
        $builder = $this->getMockBuilder(Model::class);
        $builder->setMethods(['metaData']);
        $instance = $builder->getMock();
        $instance
            ->expects($this->exactly(4))
            ->method('metaData')
            ->willReturn($mockMetadata)
            ;

        $instance->setField_1('foobar');
        $this->assertEquals('foobar', $instance->getField_1());
    }

    /**
     * Test that setting an invalid column throws exception
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testSettingInvalidColumnThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $mockMetadata = $this->createMock(Metadata::class);
        $mockMetadata
            ->expects($this->once())
            ->method('prefix')
            ->with('field_1')
            ->willReturn('field_1')
            ;
        $mockMetadata
            ->expects($this->once())
            ->method('hasColumn')
            ->with('field_1')
            ->willReturn(false);
        $mockMetadata
            ->expects($this->never())
            ->method('primaryKey');
        $builder = $this->getMockBuilder(Model::class);
        $builder->setMethods(['metaData']);
        $instance = $builder->getMock();
        $instance
            ->expects($this->exactly(2))
            ->method('metaData')
            ->willReturn($mockMetadata)
            ;

        $instance->setField_1('foobar');
    }

    /**
     * Test that setting the primary key throws an exception
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testSettingPrimaryKeyThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $mockMetadata = $this->createMock(Metadata::class);
        $mockMetadata
            ->expects($this->once())
            ->method('prefix')
            ->with('id')
            ->willReturn('id')
            ;
        $mockMetadata
            ->expects($this->once())
            ->method('hasColumn')
            ->with('id')
            ->willReturn(true);
        $mockMetadata
            ->expects($this->once())
            ->method('primaryKey')
            ->willReturn('id')
            ;
        $builder = $this->getMockBuilder(Model::class);
        $builder->setMethods(['metaData']);
        $instance = $builder->getMock();
        $instance
            ->expects($this->exactly(3))
            ->method('metaData')
            ->willReturn($mockMetadata)
            ;

        $instance->setId(1);
    }

    /**
     * Test that getting an invalid column returns null
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testSettingInvalidColumnReturnsNull()
    {
        $instance = $this->newInstance();

        $this->assertNull($instance->getFoobar());
    }

    /**
     * Test that magic call triggers an error for non getter / setter methods
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testMagicCallTriggersErrorForUnknownMethods()
    {
        // $this->expectException(RuntimeException::class);
        $instance = $this->newInstance();

        $instance->foobar();
    }
}
