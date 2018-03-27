<?php

namespace RonanChilvers\Db\Entity\MetaData;

use RonanChilvers\Db\Entity\MetaData\FactoryInterface;

class Factory implements FactoryInterface
{
    /**
     * @var RonanChilvers\DB\Entity\MetaData\LoaderInterface
     */
    protected $loader;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function get($entityClass) : MetaDataInterface
    {
    }
}
