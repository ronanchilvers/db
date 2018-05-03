<?php

namespace Ronanchilvers\Db\Model;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Ronanchilvers\Db\Model;
use Ronanchilvers\Db\Model\Metadata;
use Ronanchilvers\Utility\Str;

/**
 * Code generator for models
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Generator
{
    /**
     * Generate a model class and store it in a given location
     *
     * @param \Ronanchilvers\Db\Model\Metadata $metadata
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function generate(Metadata $metaData)
    {
        // Create the namespace
        $namespace = new PhpNamespace('');
        $namespace->addUse(Model::class);

        // Create the base class definition
        $class = $namespace->addClass($metaData->class());
        $class->setExtends('Model');

        // Create accessors
        $columns = $metaData->columns();
        foreach ($columns as $column => $params) {
            $baseName = Str::pascal($metaData->unprefix($column));
            $getter = $class
                ->addMethod('get' . $baseName)
                ->setVisibility('public')
                ->setBody(sprintf("return $this->getData('')", $column);
        }

        return (string) $namespace;
    }
}
