<?php

namespace Ronanchilvers\Db\Form;

use Psr\Http\Message\ServerRequestInterface;
use Ronanchilvers\Db\Model;
use Valitron\Validator;

/**
 * Abstract base class for form objects
 *
 * This abstract expects to work with a data array of key/value pairs
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
 class AbstractForm implements FormInterface
 {
    /**
     * @var Ronanchilvers\Db\Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Class constructor
     *
     * @param array $data
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->errors = [];
    }

    /**
     * Magic getter to proxy to the model
     *
     * @param string $attribute
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __get($attribute)
    {
        if (isset($this->data[$attribute])) {
            return $this->data[$attribute];
        }

        return $this->model->$attribute;
    }

    /**
     * Magic isset to check for properties
     *
     * @param string $attribute
     * @return bool
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __isset($attribute)
    {
        if (isset($this->data[$attribute])) {
            return true;
        }

        return $this->model->__isset($attribute);
    }

    /**
     * Set the data for this form
     *
     * @param array $data
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function setData($data)
    {
        $properties = $this->model->getPropertyNames();
        $properties = array_combine($properties, $properties);
        $data = array_intersect_key(
            $data,
            $properties
        );
        $this->data = $data;
    }

    /**
     * Configure validation rules
     *
     * This method MUST return an array of rules
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function configure(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function isValid(): bool
    {
        $rules = $this->configure();
        if (empty($rules)) {
            return true;
        }

        // Validate the data
        $validator = new Validator($this->data);
        $validator->rules($rules);
        if ($validator->validate()) {
            return true;
        }
        $this->errors = $validator->errors();

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @return bool
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function isError($attribute)
    {
        return isset($this->errors[$attribute]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function error($attribute)
    {
        if (!isset($this->errors[$attribute])) {
            return '';
        }
        return implode(', ', $this->errors[$attribute]);
    }

    /**
     * {@inheritdoc}
     *
     * @return Ronanchilvers\Db\Model
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function model()
    {
        if (!empty($this->data)) {
            $this->model->setFromArray($this->data);
        }
        return $this->model;
    }
 }
