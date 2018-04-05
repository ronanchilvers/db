<?php

namespace Ronanchilvers\Db\Test\Schema;

use Aura\SqlSchema\MysqlSchema;
use PDO;
use Ronanchilvers\Db\Schema\SchemaFactory;
use Ronanchilvers\Db\Test\TestCase;

/**
 * Test cases for the aura.sqlschema factory
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class SchemaFactoryTest extends TestCase
{
    /**
     * Get a new instance to test
     *
     * @return \Ronanchilvers\Db\Schema\SchemaFactory
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function newInstance()
    {
        return new SchemaFactory();
    }

    /**
     * Test that the schema factory returns the correct schema type
     *
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testFactoryReturnsCorrectSchemaType()
    {
        $mockPDO = $this->mockPDO();
        $mockPDO->expects($this->once())
                ->method('getAttribute')
                ->with(PDO::ATTR_DRIVER_NAME)
                ->willReturn('MySQL');
        $instance = $this->newInstance();

        $this->assertInstanceof(MysqlSchema::class, $instance->factory($mockPDO));
    }
}
