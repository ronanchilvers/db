<?php
namespace Ronanchilvers\Db\Console\Command;

use Ronanchilvers\Db\Model;
use Ronanchilvers\Db\Model\Generator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate')
            ->setDescription('Generate model classes')
            ->addArgument(
                'output_dir',
                InputArgument::REQUIRED,
                'The directory in which to store the generate code'
            )
            ->addArgument(
                'src_dir',
                InputArgument::OPTIONAL,
                'The directory in which to search for model classes',
                'src'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dirs = [];
        foreach (['output_dir', 'src_dir'] as $arg) {
            $dir = $input->getArgument($arg);
            if (is_dir($dir)) {
                $dirs[$arg] = realpath($dir);
                continue;
            }
            $dir = implode(
                DIRECTORY_SEPARATOR,
                [
                    getcwd(),
                    $dir
                ]
            );
            if (is_dir($dir)) {
                $dirs[$arg] = realpath($dir);
                continue;
            }
            throw new RuntimeException("Invalid or missing {$arg} directory");
        }

        // Include all files
        $finder = new Finder();
        foreach ($finder->files()->in($dirs['src_dir']) as $file) {
            include $file;
        }
        // Find classes that extend \Ronanchilvers\Db\Model
        $generator = new Generator();
        foreach (get_declared_classes() as $class) {
            if (in_array(Model::class, class_parents($class))) {
                $model = new $class();
                $classString = $generator->generate($model->metaData());
                // @TODO Remove var_dump
                var_dump($classString); exit();
            }
        }

    }
}
