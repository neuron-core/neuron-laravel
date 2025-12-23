<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeMiddleware extends GeneratorCommand
{
    protected $name = 'neuron:middleware';

    protected $description = 'Create a new Middleware class.';

    protected $type = 'Neuron Middleware';

    /**
     * @param string $rootNamespace
     * @phpstan-ignore-next-line
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Neuron\\Middleware';
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/middleware.stub';
    }
}
