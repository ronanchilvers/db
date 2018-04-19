<?php

namespace Ronanchilvers\Db;

use PDO;
use Ronanchilvers\Db\Model\Metadata;
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
    static protected $primaryKeys = [];

    /**
     * @var array
     */
    static protected $modelFields = [];

    /**
     * Magic call for static methods
     *
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static public function __callStatic($method, $args)
    {
        $builder = (new static)->newQueryBuilder();
        if (method_exists($builder, $method)) {
            return call_user_func_array([$builder, $method], $args);
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
    {}

    public function __call($method, $args)
    {
        if (false === $this->disableAutoGetSet && (0 === strpos($method, 'get') || 0 === strpos($method, 'set'))) {
            $attribute = Str::snake(mb_substr($method, 3));
            switch (substr($method, 0, 3)) {
                case 'set':
                    return $this->setData($attribute, $args[0]);

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
            $this->metadata = new $class(
                static::pdo(),
                $this
            );
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
     * Save this model
     *
     * This method either inserts or updates the model row based on the presence
     * of an ID. It will return false if the save fails.
     *
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function save()
    {
        $metaData     = $this->metaData();
        $pKey         = $metaData->primaryKey();
        $data         = $this->data;
        $queryBuilder = $this->newQueryBuilder();
        if (true === isset($data[$pKey])) {
            // Update
            $query = $queryBuilder->update();
            $id = $data[$pKey];
            unset($data[$pKey]);
            $query
                ->set(
                    $this->data
                )
                ->where(
                    $pKey,
                    '=',
                    $id
                );

            return $query->execute();
        } else {
            // Insert
            $query = $queryBuilder->insert();
            $query->values(
                $this->data
            );

            if (true !== $query->execute()) {
                return false;
            }
            $this->data[$pKey] = static::pdo()->lastInsertId();

            return true;
        }

        return false;
    }

    /**
     * Delete this model record
     *
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function delete()
    {
        $metaData     = $this->metaData();
        $pKey         = $metaData->primaryKey();
        if (!isset($this->data[$pKey]) || empty($this->data[$pKey])) {
            throw new RuntimeException(
                sprintf('Unable to delete model without primary key', $key)
            );
        }
        $query = $this->newQueryBuilder()
            ->delete()
            ->where(
                $pKey,
                '=',
                $this->data[$pKey]
            )
            ;
        if (false === $query->execute()) {
            return false;
        }
        unset($this->data[$pKey]);

        return true;
    }

    /**
     * Set a data attribute on this model
     *
     * @param string $key
     * @param mixed $value
     * @return static
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function setData($key, $value)
    {
        $key = $this->metaData()->prefix($key);
        if (!$this->metaData()->hasColumn($key)) {
            throw new RuntimeException(
                sprintf('Unknown field %s', $key)
            );
        }
        if ($this->metaData()->primaryKey() == $key) {
            throw new RuntimeException(
                sprintf('Invalid attempt to overwrite primary key column %s', $key)
            );
        }
        $this->data[$key] = $value;

        return $this;
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
