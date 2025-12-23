<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTool extends GeneratorCommand
{
    protected $name = 'neuron:tool';

    protected $description = 'Create a new Tool class.';

    protected $type = 'Neuron Tool';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Agents\\Tools';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/tool.stub';
    }
}
