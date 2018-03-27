<?php

namespace RonanChilvers\Db\Entity;

/**
 * Meta Data objects provide meta data for a given class to data mappers
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface MetaDataInterface
{
    /**
     * Get the primary key column name
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getPrimaryKeyColumnName() : string;
}
