<?php

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAgent extends GeneratorCommand
{
    protected $name = 'neuron:agent';

    protected $description = 'Create a new Agent class.';

    protected $type = 'Agent';

    protected function getStub(): string
    {
        return __DIR__.'/stubs/agent.stub';
    }
}
