<?php

namespace Ronanchilvers\Db;

use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\FetchableInterface;
use ClanCats\Hydrahon\Query\Sql\Insert;
use ClanCats\Hydrahon\Query\Sql\Select;
use ClanCats\Hydrahon\Query\Sql\Update;
use PDO;
use Ronanchilvers\Db\Model\Hydrator;
use Ronanchilvers\Db\Model\Metadata;
use Ronanchilvers\Utility\Collection;
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
     * Create a select object
     *
     * @return \ClanCats\Hydrahon\Query\Sql\Select
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function select()
    {
        $builder = $this->newBuilder();

        $select = $builder->select();
        $select->table(
            $this->metadata->table()
        );

        return $select;
    }

    /**
     * Create an insert query
     *
     * @return \ClanCats\Hydrahon\Query\Sql\Insert
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function insert()
    {
        $builder = $this->newBuilder();

        return $builder
            ->table($this->metadata->table())
            ->insert();
    }

    /**
     * Create an update query
     *
     * @return \ClanCats\Hydrahon\Query\Sql\Update
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function update()
    {
        $builder = $this->newBuilder();

        return $builder
            ->table($this->metadata->table())
            ->update();
    }

    /**
     * Get a delete query
     *
     * @return ClanCats\Hydrahon\Query\Sql\Delete
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function delete()
    {
        return $this
            ->newBuilder()
            ->table($this->metadata->table())
            ->delete();
    }

    /**
     * Create a hydrahon query builder
     *
     * @return \ClanCats\Hydrahon\Builder
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function newBuilder()
    {
        // @todo Don't hardcode mysql
        return new \ClanCats\Hydrahon\Builder(
            'mysql',
            $this->generateCallback()
        );
    }

    /**
     * Generate a PDO callback
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function generateCallback()
    {
        return function ($query, $sql, $params) {
            $stmt = $this->pdo->prepare(
                $sql
            );
            $result = $stmt->execute($params);
            if (false === $result) {
                throw new RuntimeException(
                    implode(' : ', $stmt->errorInfo())
                );
            }
            if (!$query instanceof FetchableInterface) {
                return $result;
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
        };
    }
}
