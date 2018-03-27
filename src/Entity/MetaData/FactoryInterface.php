<?php

namespace RonanChilvers\Db\Entity\MetaData;

use RonanChilvers\Db\Entity\MetaDataInterface;

/**
 * Factory for meta data objects that provide information about entities
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface FactoryInterface
{
    /**
     * Get a meta data object for a given entity class
     *
     * @param string $entityClass
     * @return RonanChilvers\DB\Entity\MetaDataInterface
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($entityClass) : MetaDataInterface;
}
