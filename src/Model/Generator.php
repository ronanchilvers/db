<?php

namespace Ronanchilvers\Db\Model;

use DateTime;
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
     * @param string $namespace
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function generate(Metadata $metaData, $namespace)
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        // Create the namespace
        $namespace = new PhpNamespace('');
        $namespace->addUse(Model::class);

        // Create the base class definition
        $class = $namespace->addClass($metaData->class());
        $class
            ->setExtends('Model')
            ->addComment('Auto-generated by ronanchilvers/db')
            ->addComment($now)
            ;

        // Create accessors
        $columns = $metaData->columns();
        foreach ($columns as $column => $params) {
            $baseName = Str::pascal($metaData->unprefix($column));
            $getter = $class
                ->addMethod('get' . $baseName)
                ->setVisibility('public')
                ->setBody(sprintf("return \$this->getData('%s')", $column))
                ->addComment('Getter for column : ' . $column)
                ->addComment('')
                ->addComment('@return mixed')
                ;
        }

        return str_replace("\t", "    ", (string) $namespace);
    }
}