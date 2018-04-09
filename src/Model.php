<?php

namespace Ronanchilvers\Db;

use Aura\SqlSchema\ColumnFactory;
use PDO;
use Ronanchilvers\Db\Model\Metadata;
use Ronanchilvers\Db\Schema\SchemaFactory;
use Ronanchilvers\Utility\Str;
use RuntimeException;

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
     * @var array
     */
    static protected $modelFields = [];

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
     * Boot a given model class
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static protected function columns(MetaData $metaData)
    {
        $class = $metaData->class();
        if (isset(static::$modelFields[$class])) {
            return static::$modelFields[$class];
        }
        $schemaFactory = new SchemaFactory();
        $schema = $schemaFactory->factory(
            static::pdo()
        );
        $dbColumns = $schema->fetchTableCols(
            $metaData->table()
        );
        $columns = [];
        foreach ($dbColumns as $col) {
            $columns[$col->name] = [
                'type'   => $col->type,
                'length' => $col->size,
            ];
        }
        static::$modelFields[$class] = $columns;

        return $columns;
    }

    /**
     * @var string
     */
    protected $metaDataClass = Metadata::class;

    /**
     * @var Ronanchilvers\Db\Model\Metadata
     */
    protected $metadata = null;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var boolean
     */
    protected $disableAutoGetSet = false;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct()
    {
        $this->boot();
    }

    public function __get($var)
    {
        return $this->getData($var);
    }

    public function __set($var, $value)
    {
        $this->setData($var, $value);
    }

    public function __call($method, $args)
    {
        if (false === $this->disableAutoGetSet && (0 === strpos($method, 'get') || 0 === strpos($method, 'set'))) {
            $attribute = Str::snake(mb_substr($method, 3));
            switch (substr($method, 0, 3)) {
                case 'set':
                    $this->setData($attribute, $args[0]);
                    return;

                case 'get':
                    return $this->getData($attribute);
            }
        }

        trigger_error(
            sprintf(
                'Call to undefined method %s::%s()',
                get_called_class(),
                $method
            ),
            E_USER_ERROR
        );
    }

    /**
     * Get a meta data instance for this model
     *
     * @return Ronanchilvers\Db\Model\Metadata
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function metaData()
    {
        $class = $this->metaDataClass;
        if (!$this->metadata instanceof $class) {
            $this->metadata = new $class($this);
        }

        return $this->metadata;
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
            $this->metaData()
        );
    }

    /**
     * Boot this model if its not already booted
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function boot()
    {
        if (false === $this->disableAutoGetSet) {
            $this->fields = static::columns(
                $this->metaData()
            );
        }
    }

    /**
     * Set a data attribute on this model
     *
     * @param string $key
     * @param mixed $value
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setData($key, $value)
    {
        $key = $this->metaData()->prefix($key);
        if (!isset($this->fields[$key])) {
            throw new RuntimeException(
                sprintf('Unknown field %s', $key)
            );
        }
        $this->data[$key] = $value;
    }

    /**
     * Get a data attribute for this model
     *
     * @param string $key
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function getData($key)
    {
        $key = $this->metaData()->prefix($key);
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }
}
