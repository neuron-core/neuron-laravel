<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use Illuminate\Support\Manager;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Deepseek\Deepseek;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\HuggingFace\HuggingFace;
use NeuronAI\Providers\Mistral\Mistral;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Providers\OpenAI\Responses\OpenAIResponses;

/**
 * @method static AIProviderInterface driver(string $driver = null)
 */
class AIProvider extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('neuron.provider.default');
    }

    public function createAnthropicDriver(): AIProviderInterface
    {
        return new Anthropic(...$this->config['neuron.provider.anthropic']);
    }

    public function createOpenaiDriver(): AIProviderInterface
    {
        return new OpenAI(...$this->config['neuron.provider.openai']);
    }

    public function createOpenaiResponsesDriver(): AIProviderInterface
    {
        return new OpenAIResponses(...$this->config['neuron.provider.openai-responses']);
    }

    public function createGeminiDriver(): AIProviderInterface
    {
        return new Gemini(...$this->config['neuron.provider.gemini']);
    }

    public function createOllamaDriver(): AIProviderInterface
    {
        return new Ollama(...$this->config['neuron.provider.ollama']);
    }

    public function createMistralDriver(): AIProviderInterface
    {
        return new Mistral(...$this->config['neuron.provider.mistral']);
    }

    public function createDeepseekDriver(): AIProviderInterface
    {
        return new Deepseek(...$this->config['neuron.provider.deepseek']);
    }

    public function createHuggingfaceDriver(): AIProviderInterface
    {
        return new HuggingFace(...$this->config['neuron.provider.huggingface']);
    }
}
