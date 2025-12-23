<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Tests;

use Illuminate\Foundation\Application;
use NeuronAI\Laravel\AIProvider;
use NeuronAI\Laravel\NeuronAIServiceProvider;
use Orchestra\Testbench\TestCase;

class BasicTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [NeuronAIServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  Application  $app
     */
    protected function getPackageAliases(mixed $app): array
    {
        return [
            'AIProvider' => AIProvider::class,
        ];
    }
}
