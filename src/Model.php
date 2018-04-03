<?php

namespace Ronanchilvers\Db;

use PDO;
use Ronanchilvers\Db\Model\Metadata;

/**
 * Base model class for all models
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Model
{
    /**
     * An instance of PDO to use for database interactions
     *
     * @var \PDO
     */
    static private $pdo;

    /**
     * Set the PDO instance to use for models
     *
     * @param \PDO $pdo
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static public function setPdo(PDO $pdo)
    {
        static::$pdo = $pdo;
    }

    /**
     * Get the configured PDO instance
     *
     * @return \PDO
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static protected function pdo()
    {
        return self::$pdo;
    }

    /**
     *
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static public function select()
    {
        return (new static)
            ->newQueryBuilder()
            ->select();
    }

    /**
     * @var string
     */
    protected $metaDataClass = Metadata::class;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Get a meta data instance for this model
     *
     * @return Ronanchilvers\Db\Model\Metadata
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function newMetaData()
    {
        $class = $this->metaDataClass;

        return new $class($this);
    }

    /**
     * Get a new query builder for this model
     *
     * @return Ronanchilvers\Db\QueryBuilder
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function newQueryBuilder()
    {
        return new QueryBuilder(
            static::pdo(),
            $this->newMetaData()
        );
    }
}
