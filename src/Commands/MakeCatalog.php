<?php

namespace Vuravel\Catalog\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCatalog extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vuravel:catalog {name} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new vuravel catalog class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Catalog';

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        return str_replace('{name}', $this->argument('name'),
                str_replace('{Model}', ucfirst($this->argument('model')),
                str_replace('{model}', strtolower($this->argument('model')), $stub)));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__ . '/stubs/vuravel-catalog.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Catalogs';
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The class name of the catalog.'],
            ['model', InputArgument::REQUIRED, 'The model name of the catalog.'],
        ];
    }

}
