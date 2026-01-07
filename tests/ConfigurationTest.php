<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Tests;

class ConfigurationTest extends BasicTestCase
{
    public function testDefault(): void
    {
        $this->assertNull(config('neuron.provider.default'));
    }

    public function testKey(): void
    {
        // Providers
        $this->assertArrayHasKey('anthropic', config('neuron.provider'));
        $this->assertArrayHasKey('openai', config('neuron.provider'));
        $this->assertArrayHasKey('openai-responses', config('neuron.provider'));
        $this->assertArrayHasKey('gemini', config('neuron.provider'));
        $this->assertArrayHasKey('ollama', config('neuron.provider'));
        $this->assertArrayHasKey('mistral', config('neuron.provider'));
        $this->assertArrayHasKey('deepseek', config('neuron.provider'));
        $this->assertArrayHasKey('huggingface', config('neuron.provider'));

        // Embeddings
        $this->assertArrayHasKey('openai', config('neuron.embedding'));
        $this->assertArrayHasKey('gemini', config('neuron.embedding'));
        $this->assertArrayHasKey('voyage', config('neuron.embedding'));
        $this->assertArrayHasKey('mistral', config('neuron.embedding'));
        $this->assertArrayHasKey('ollama', config('neuron.embedding'));

        // Stores
        $this->assertArrayHasKey('file', config('neuron.store'));
        $this->assertArrayHasKey('pinecone', config('neuron.store'));
        $this->assertArrayHasKey('qdrant', config('neuron.store'));
        $this->assertArrayHasKey('meilisearch', config('neuron.store'));
        $this->assertArrayHasKey('chroma', config('neuron.store'));
    }
}
