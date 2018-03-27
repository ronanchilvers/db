<?php

namespace RonanChilvers\Db;

/**
 * Base interface for repositories
 *
 * Mappers are responsible for managing the interactions between entities and
 * their data store.
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
interface MapperInterface
{
    /**
     * Find an object by its primary key
     *
     * @param mixed $value
     * @return boolean|mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function findByPK($value);

    /**
     * Find an object using a raw SQL statement
     *
     * @param string $sql
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function find($sql);

    /**
     * Store an entity to the database
     *
     * @param mixed $entity
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function store($entity) : boolean;
}
