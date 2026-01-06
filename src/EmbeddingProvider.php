<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use Illuminate\Support\Manager;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\GeminiEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OpenAILikeEmbeddings;

/**
 * @method static EmbeddingsProviderInterface driver(string $driver = null)
 */
class EmbeddingProvider extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('neuron.embedding.default');
    }

    public function createOpenaiDriver(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(...$this->config['neuron.embedding.openai']);
    }

    public function createGeminiDriver(): EmbeddingsProviderInterface
    {
        return new GeminiEmbeddingsProvider(...$this->config['neuron.embedding.gemini']);
    }

    public function createOllamaDriver(): EmbeddingsProviderInterface
    {
        return new OllamaEmbeddingsProvider(...$this->config['neuron.embedding.ollama']);
    }

    public function createMistralDriver(): EmbeddingsProviderInterface
    {
        return new OpenAILikeEmbeddings(...$this->config['neuron.embedding.mistral']);
    }
}
