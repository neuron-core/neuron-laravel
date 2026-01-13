<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use Illuminate\Support\ServiceProvider;
use NeuronAI\Laravel\Commands\MakeAgent;
use NeuronAI\Laravel\Commands\MakeMiddleware;
use NeuronAI\Laravel\Commands\MakeNode;
use NeuronAI\Laravel\Commands\MakeRAG;
use NeuronAI\Laravel\Commands\MakeTool;
use NeuronAI\Laravel\Commands\MakeWorkflow;

class NeuronAIServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     */
    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/neuron.php' => config_path('neuron.php')], 'neuron-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations/neuron'),
        ], 'neuron-migrations');

        $this->commands([
            MakeAgent::class,
            MakeNode::class,
            MakeRAG::class,
            MakeTool::class,
            MakeWorkflow::class,
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/neuron.php', 'neuron');

        $this->app->singleton(AIProviderManager::class);
        $this->app->singleton(EmbeddingProviderManager::class);
    }
}
