<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

/**
 * @mixin VectorStoreInterface
 */
class VectorStore extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \NeuronAI\Laravel\VectorStoreManager::class;
    }
}
