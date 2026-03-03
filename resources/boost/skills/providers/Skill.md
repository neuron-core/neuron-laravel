---
name: Neuron AI Providers
description: Use when switching between AI providers, configuring provider options, or implementing custom AI provider integrations.
---

# Neuron AI Providers

Unified interface for multiple AI providers (Anthropic, OpenAI, Gemini, Ollama, etc.).

## Anthropic Provider

```php
use NeuronAI\Providers\Anthropic\Anthropic;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new Anthropic(
            key: $apiKey,
            model: 'claude-3-5-sonnet-20241022',
            version: '2023-06-01',
            max_tokens: 8192,
            parameters: [
                'temperature' => 0.7,
                'top_p' => 0.9,
            ],
        );
    }
}
```

## OpenAI Provider

```php
use NeuronAI\Providers\OpenAI\OpenAI;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: $apiKey,
            model: 'gpt-4-turbo',
            parameters: [
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'top_p' => 0.9,
            ],
            strict_response: false,  // Strict mode for structured outputs
        );
    }
}
```

## OpenAI Responses API

```php
use NeuronAI\Providers\OpenAI\Responses\OpenAIResponses;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAIResponses(
            key: $apiKey,
            model: 'gpt-4-turbo',
            parameters: [
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'top_p' => 0.9,
            ],
            strict_response: false,  // Strict mode for structured outputs
        );
    }
}
```

## Ollama Provider

```php
use NeuronAI\Providers\Ollama\Ollama;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new Ollama(
            url: 'http://localhost:11434/api',
            model: 'llama3',
            parameters: [
                'temperature' => 0.7,
                'top_p' => 0.9,
            ],
        );
    }
}
```

## Common Parameters

All providers support these common parameters via the `parameters` array:

- `temperature` - 0-2, higher = more creative (default varies by provider)
- `top_p` - Nucleus sampling (default: 0.9 or 1.0)
- `max_tokens` - Maximum tokens in response
- `stop` - Stop sequences

## Supported Providers

- Anthropic (Claude models)
- OpenAI 
- OpenAI Responses API
- OpenAI on Azure
- Gemini (Google models)
- Gemini with Vertex
- Ollama (Local models)
- HuggingFace
- Mistral
- Cohere
- Grok (xAI)
- Deepseek
- AWS Bedrock

## Switching Providers

Easy to switch without changing application code:

```php
$agent = Agent::make()->setAiProvider(new Anthropic($key, 'claude-3-5-sonnet'));

// Later, switch to OpenAI
$agent->setAiProvider(new OpenAI($key, 'gpt-4-turbo'));
```
