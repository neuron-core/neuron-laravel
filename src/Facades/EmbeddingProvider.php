<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;

/**
 * @mixin EmbeddingsProviderInterface
 */
class EmbeddingProvider extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \NeuronAI\Laravel\EmbeddingProviderManager::class;
    }
}
