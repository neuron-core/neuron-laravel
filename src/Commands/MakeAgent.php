<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAgent extends GeneratorCommand
{
    protected $name = 'neuron:agent';

    protected $description = 'Create a new Agent class.';

    protected $type = 'Neuron Agent';

    /**
     * @param string $rootNamespace
     * @phpstan-ignore-next-line
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Agents';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/agent.stub';
    }
}
