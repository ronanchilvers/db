<?php

namespace Ronanchilvers\Db\Model;

use Ronanchilvers\Db\Model;

/**
 * Class responsible for providing model meta data such as table names
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Metadata
{
    /**
     * @var Ronanchilvers\Db\Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $fieldPrefix = null;

    /**
     * Class constructor
     *
     * @param Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model class for this configuration
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function class()
    {
        return get_class($this->model);
    }

    /**
     * Get the table name for the model
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function table()
    {
        if (is_null($this->table)) {
            $this->table = $this->transformTableName(
                get_class($this->model)
            );
        }

        return $this->table;
    }

    /**
     * Prefix a string with the configured field prefix
     *
     * @param  string $string
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function prefix($string)
    {
        $prefix = $this->fieldPrefix;
        if (0 === strpos($string, $prefix)) {
            return $string;
        }

        return "{$prefix}_{$string}";
    }

    /**
     * Un-prefix a string with the configured field prefix
     *
     * @param string $string
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function unprefix($string)
    {
        $prefix = $this->fieldPrefix;
        if (0 === strpos($string, $prefix)) {
            return substr($string, 0, strlen($prefix) + 1);
        }

        return $string;
    }

    /**
     * Transform a string into a table name
     *
     * @param string $string
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function transformTableName($string)
    {
        $string = strtolower($string);
        $string = preg_replace('#[^0-9A-z-_]+#', '', $string);
        $string = preg_replace('#[\s]+#', '_', $string);
        $string .= 's';

        return $string;
    }
}
