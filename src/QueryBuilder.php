<?php

namespace Ronanchilvers\Db;

use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\QueryFactory;
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
     * @var QueryFactory
     */
    protected $queryFactory;

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
        $pdo,
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
     * Get a select query
     *
     * @return Aura\SqlQuery\Common\SelectInterface
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function select()
    {
        if (is_null($this->query)) {
            $this->query = $this->queryFactory()
                ->newSelect()
                ->cols(['*'])
                ->from(
                    $this->metadata->table()
                )
                ;
        }

        return $this;
    }

    /**
     * Get all records
     *
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function execute()
    {
        switch (1) {

            case $this->query instanceof SelectInterface:
                return $this->processSelect($this->query);

            default:
                throw new \Exception('Unsupported query type');

        }
    }

    /**
     * Process a select query into a collection
     *
     * @param Aura\SqlQuery\Common\SelectInterface
     * @return Collection
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function processSelect(SelectInterface $select)
    {
        $stmt = $this->pdo->prepare(
            $select->getStatement()
        );
        if (false === $stmt->execute($select->getBindValues())) {
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

    /**
     *
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function queryFactory()
    {
        if (!$this->queryFactory instanceof QueryFactory) {
            $this->queryFactory = new QueryFactory(
                $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)
            );
        }

        return $this->queryFactory;
    }
}
