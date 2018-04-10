<?php

namespace Ronanchilvers\Db;

use PDO;
use Ronanchilvers\Db\Model\Hydrator;
use Ronanchilvers\Db\Model\Metadata;
use RuntimeException;

/**
 * Class to build model queries and return model instances
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class QueryBuilder
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var Aura\SqlQuery\QueryInterface
     */
    protected $query;

    /**
     * @var Ronanchilvers\Db\Model\Metadata
     */
    protected $metadata;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(
        PDO $pdo,
        $metadata
    ) {
        $this->pdo = $pdo;
        $this->metadata = $metadata;
        $this->query = null;
    }

    /**
     * Magic call method to allow access to the stored query object
     *
     * @param string $method
     * @param array $args
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __call($method, $args)
    {
        if (method_exists($this->query, $method)) {
            call_user_func_array([$this->query, $method], $args);

            return $this;
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
     * Get all records
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function all()
    {
        return $this->select()->get();
    }

    /**
     * Start a query
     *
     * @return \ClanCats\Hydrahon\BaseQuery
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function select()
    {
        $builder = new \ClanCats\Hydrahon\Builder('mysql', function ($query, $string, $params) {
            return $this->processSelect(
                $string,
                $params
            );
        });

        $select = $builder->select();
        $select->table(
            $this->metadata->table()
        );

        return $select;
    }

    /**
     * Process a select query into a collection
     *
     * @param string $sql
     * @param array $params
     * @return Collection
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function processSelect($sql, $params)
    {
        $stmt = $this->pdo->prepare(
            $sql
        );
        if (false === $stmt->execute($params)) {
            throw new RuntimeException(
                implode(' : ', $stmt->errorInfo())
            );
        }
        $class = $this->metadata->class();
        $result = [];
        $hydrator = new Hydrator();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $model = new $class();
            $hydrator->hydrate($row, $model);
            $result[] = $model;
        }

        return $result;
    }
}
