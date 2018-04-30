<?php

namespace Ronanchilvers\Db;

use PDO;
use Ronanchilvers\Db\Model\Metadata;
use Ronanchilvers\Db\Model\ObserverInterface;
use Ronanchilvers\Utility\Str;
use RuntimeException;

/**
 * Base model class for all models
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
abstract class Model
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
     * @var array
     */
    static protected $observers = [];

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

        throw new RuntimeException(
            sprintf(
                'Undefined method %s::%s()',
                get_called_class(),
                $method
            )
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
     * Register a callable as an observer for a particular model
     *
     * @param \Ronanchilvers\Db\Model\ObserverInterface $observer
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static public function observe(ObserverInterface $observer)
    {
        $class = get_called_class();
        if (!isset(static::$observers[$class])) {
            static::$observers[$class] = [];
        }
        static::$observers[$class][] = $observer;
    }

    /**
     * Notify observers of an event
     *
     * @param Model $model
     * @param string $event
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    static protected function notifyObservers(
        Model $model,
        string $event
    )
    {
        $class = get_called_class();
        if (!isset(static::$observers[$class])) {
            return;
        }
        if (!is_array(static::$observers[$class]) || 0 == count(static::$observers[$class])) {
            return;
        }
        foreach (static::$observers[$class] as $observer) {
            if (false === $observer->$event($model)) {
                return false;
            }
        }

        return true;
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

        throw new RuntimeException(
            sprintf(
                'Undefined method %s::%s()',
                get_called_class(),
                $method
            )
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
        if (false === static::notifyObservers($this, 'saving'))
        {
            return false;
        }
        if (true === isset($data[$pKey])) {
            if (false === static::notifyObservers($this, 'updating'))
            {
                return false;
            }
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

            $result = $query->execute();
            if (false === $result) {
                return false;
            }
            static::notifyObservers($this, 'updated');
            static::notifyObservers($this, 'saved');
            return true;
        } else {
            if (false === static::notifyObservers($this, 'creating'))
            {
                return false;
            }
            // Insert
            $query = $queryBuilder->insert();
            $query->values(
                $this->data
            );

            if (true !== $query->execute()) {
                return false;
            }
            $this->data[$pKey] = static::pdo()->lastInsertId();

            static::notifyObservers($this, 'created');
            static::notifyObservers($this, 'saved');
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
                sprintf('Unable to delete model without primary key %s', $pKey)
            );
        }
        if (false === static::notifyObservers($this, 'deleting')) {
            return false;
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
        static::notifyObservers($this, 'deleted');

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
