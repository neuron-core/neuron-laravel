<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Tests;

class ConfigurationTest extends BasicTestCase
{
    public function testMaxItems(): void
    {
        $this->assertNull(config('neuron.provider.default'));
    }

    public function testKey(): void
    {
        $this->assertArrayHasKey('anthropic', config('neuron.provider'));
        $this->assertArrayHasKey('openai', config('neuron.provider'));
        $this->assertArrayHasKey('openai-responses', config('neuron.provider'));
        $this->assertArrayHasKey('gemini', config('neuron.provider'));
        $this->assertArrayHasKey('ollama', config('neuron.provider'));
        $this->assertArrayHasKey('mistral', config('neuron.provider'));
        $this->assertArrayHasKey('deepseek', config('neuron.provider'));
        $this->assertArrayHasKey('huggingface', config('neuron.provider'));
    }
}
