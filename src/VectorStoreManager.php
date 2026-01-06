<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use Illuminate\Support\Manager;
use NeuronAI\RAG\VectorStore\ChromaVectorStore;
use NeuronAI\RAG\VectorStore\FileVectorStore;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;
use NeuronAI\RAG\VectorStore\PineconeVectorStore;
use NeuronAI\RAG\VectorStore\QdrantVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

/**
 * @method static VectorStoreInterface driver(string $driver = null)
 */
class VectorStoreManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('neuron.embedding.default');
    }

    public function createFileDriver(): VectorStoreInterface
    {
        return new FileVectorStore(...$this->config['neuron.store.file']);
    }

    public function createPineconeDriver(): VectorStoreInterface
    {
        return new PineconeVectorStore(...$this->config['neuron.store.pinceone']);
    }

    public function createQdrantDriver(): VectorStoreInterface
    {
        return new QdrantVectorStore(...$this->config['neuron.store.qdrant']);
    }

    public function createMeilisearchDriver(): VectorStoreInterface
    {
        return new MeilisearchVectorStore(...$this->config['neuron.store.meilisearch']);
    }

    public function createChromaDriver(): VectorStoreInterface
    {
        return new ChromaVectorStore(...$this->config['neuron.store.chroma']);
    }
}
