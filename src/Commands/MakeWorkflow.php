<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeWorkflow extends GeneratorCommand
{
    protected $name = 'neuron:workflow';

    protected $description = 'Create a new Workflow class.';

    protected $type = 'Neuron Workflow';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Workflow';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/workflow.stub';
    }
}
