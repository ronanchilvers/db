<?php

namespace RonanChilvers\DB\Entity\MetaData;

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
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getMetaArray() : array;
}
