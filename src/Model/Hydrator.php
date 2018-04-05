<?php

namespace Ronanchilvers\Db\Model;

use Ronanchilvers\Db\Model;

/**
 * Hydrator for models
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Hydrator extends Model
{
    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct()
    {}

    /**
     * Hydrate a model from an array
     *
     * @param array $data
     * @param Ronanchilvers\Db\Model $model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function hydrate(array $data, Model $model)
    {
        foreach ($data as $key => $value) {
            $model->setData($key, $value);
        }
    }

    /**
     * Extract a model to an array
     *
     * @param Ronanchilvers\Db\Model $model
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function extract(Model $model)
    {
        $data = [];
        foreach (array_keys($model->fields) as $key) {
            $data[$key] = $model->getData($key);
        }

        return $data;
    }
}
