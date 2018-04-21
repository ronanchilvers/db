<?php

namespace Ronanchilvers\Db\Test;

use Ronanchilvers\Db\Test\TestCase;
use PDO;

/**
 * Test suite for the model class
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
abstract class ModelTest extends TestCase
{
    /**
     * @var PDO
     */
    protected $mockPdo;

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setUp()
    {
        $this->mockPdo = $this
            ->getMockBuilder('PDO')
            ->disableOriginalConstructor()
            ->getMock()
            ;
        Model::setPdo($this->mockPdo);
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
}
