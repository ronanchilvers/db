<?php

namespace RonanChilvers\Db\Entity\MetaData\Loader;

use RonanChilvers\Db\Entity\MetaData\LoaderInterface;
use DirectoryIterator;

/**
 * Loader for entity meta data in json files
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class JsonLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Class constructor
     *
     * @param string $path
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getMetaArray() : array
    {
        if (!is_dir($this->path) || !is_readable($this->path)) {
            return false;
        }
        $iterator = new DirectoryIterator($this->path);
        while ($iterator->valid()) {
            if (!$iterator->isFile()) {
                continue;
            }
            if (!'json' == $iterator->getExtension()) {
                continue;
            }
            // @TODO Remove var_dump
            var_dump($iterator->getFilename());
        }
    }
}
