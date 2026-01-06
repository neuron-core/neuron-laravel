<?php

declare(strict_types=1);

use NeuronAI\Providers\HuggingFace\InferenceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | Configure the default provider to use for AI generation.
    |
    */

    'provider' => [
        'default' => env('NEURON_AI_PROVIDER'),

        'anthropic' => [
            'key' => env('ANTHROPIC_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-7-sonnet-latest'),
            'parameters' => [],
        ],

        'openai' => [
            'key' => env('OPENAI_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
            'parameters' => [],
        ],

        'openai-responses' => [
            'key' => env('OPENAI_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
            'parameters' => [],
        ],

        'gemini' => [
            'key' => env('GEMINI_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-3-pro-preview'),
            'parameters' => [],
        ],

        'ollama' => [
            'url' => env('OLLAMA_URL', 'http://localhost:11434/api'),
            'model' => env('OLLAMA_MODEL', 'ministral-3:latest'),
            'parameters' => [],
        ],

        'mistral' => [
            'key' => env('MISTRAL_KEY'),
            'model' => env('MISTRAL_MODEL', 'mistral-7b-instruct-v0.2'),
            'parameters' => [],
        ],

        'deepseek' => [
            'key' => env('DEEPSEEK_KEY'),
            'model' => env('DEEPSEEK_MODEL', 'DeepSeek-V3'),
            'parameters' => [],
        ],

        'huggingface' => [
            'key' => env('HUGGINGFACE_KEY'),
            'model' => env('HUGGINGFACE_MODEL', 'meta-llama/Llama-2-7b-hf'),
            'inferenceProvider' => InferenceProvider::HF_INFERENCE,
            'parameters' => [],
        ],

        /*'cohere' => [
            'key' => env('COHERE_KEY'),
            'model' => env('COHERE_MODEL', 'command-a-reasoning-08-2025'),
            'parameters' => [],
        ],*/
    ],

    'embedding' => [
        'default' => env('NEURON_EMBEDDING_PROVIDER'),

        'openai' => [
            'key' => env('OPENAI_KEY'),
            'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-ada-002'),
            'dimensions' => 1024,
        ],

        'gemini' => [
            'key' => env('GEMINI_KEY'),
            'model' => env('GEMINI_EMBEDDING_MODEL', 'gemini-pro-embed-v1'),
            'config' => [],
        ],

        'ollama' => [
            'url' => env('OLLAMA_URL', 'http://localhost:11434/api'),
            'model' => env('OLLAMA_EMBEDDING_MODEL', 'openai-embedding-ada-002'),
            'parameters' => [],
        ],

        'mistral' => [
            'baseUri' => env('MISTRAL_BASE_URI', 'https://api.mistral.ai/v1/embeddings'),
            'key' => env('MISTRAL_KEY'),
            'model' => env('MISTRAL_EMBEDDING_MODEL', 'mistral-7b-embed-v1'),
            'dimensions' => 1024,
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | System prompt
    |--------------------------------------------------------------------------
    |
    | You can configure a system prompt to be used by default across multiple AI Agents.
    |
    */

    'system_prompt' => [
        'background' => 'You are a helpful AI assistant built with Neuron AI framework.',
        'steps' => [],
        'output' => [],
    ],
];
