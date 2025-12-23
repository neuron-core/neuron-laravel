<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeNode extends GeneratorCommand
{
    protected $name = 'neuron:node';

    protected $description = 'Create a new Node class.';

    protected $type = 'Neuron Node';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Workflow';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/node.stub';
    }
}
