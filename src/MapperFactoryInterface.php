<?php

namespace RonanChilvers\DB;

use RonanChilvers\DB\MapperInterface;

/**
 * Factory for mapper objects
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface MapperFactoryInterface
{
    /**
     * Get a mapper instance for a given entity class
     *
     * @param string $entityClass
     * @return false|RonanChilvers\DB\MapperInterface
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get(string $entityClass) : MapperInterface;
}
