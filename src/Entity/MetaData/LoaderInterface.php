<?php

namespace RonanChilvers\Db\Entity\MetaData;

/**
 * Interface for meta data loader objects
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface LoaderInterface
{
    /**
     * Get the meta data array for this loader
     *
     * @return array|boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getMetaArray() : array;
}
