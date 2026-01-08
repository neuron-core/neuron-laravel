<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use Illuminate\Support\Manager;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\GeminiEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OpenAILikeEmbeddings;
use NeuronAI\RAG\Embeddings\VoyageEmbeddingsProvider;

/**
 * @method static EmbeddingsProviderInterface driver(string $driver = null)
 */
class EmbeddingProviderManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('neuron.embedding.default');
    }

    public function createOpenaiDriver(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(...config('neuron.embedding.openai'));
    }

    public function createGeminiDriver(): EmbeddingsProviderInterface
    {
        return new GeminiEmbeddingsProvider(...config('neuron.embedding.gemini'));
    }

    public function createOllamaDriver(): EmbeddingsProviderInterface
    {
        return new OllamaEmbeddingsProvider(...config('neuron.embedding.ollama'));
    }

    public function createVoyageDriver(): EmbeddingsProviderInterface
    {
        return new VoyageEmbeddingsProvider(...config('neuron.embedding.voyage'));
    }

    public function createMistralDriver(): EmbeddingsProviderInterface
    {
        return new OpenAILikeEmbeddings(...config('neuron.embedding.mistral'));
    }
}
