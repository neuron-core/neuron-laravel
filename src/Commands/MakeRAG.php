<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRAG extends GeneratorCommand
{
    protected $name = 'neuron:rag';

    protected $description = 'Create a new RAG class.';

    protected $type = 'Neuron RAG';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Agents';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/rag.stub';
    }
}
