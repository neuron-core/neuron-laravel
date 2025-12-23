# Utility Package for using Neuron in Laravel applications

This package provides some useful helpers and configuration options for Laravel applications.

<a name="requirements"></a>

## Requirements

- PHP >= 8.2
- Laravel >= 10.x

<a name="install"></a>

## Install

Install the latest version by:

```
composer require neuron-core/neuron-laravel
```

## Configuration file

If you want to customize the configuration file beyond the environment variables, you can copy the package configuration file
in your project `config/neuron.php` folder:

```
php artisan vendor:publish --tag=neuron-config
```

## AI Providers

The configuration file allows you to configure the default AI provider you want to use in your agents, and the 
connection parameters (AP key, model, etc.) for all the providers you want to use.

Neuron allows you to implement AI agents using many different providers, like Anthropic, Gemini, OpenAI, Ollama, Mistral and many more.
Learn more about supported providers in the Neuron AI documentation: **https://docs.neuron-ai.dev/the-basics/ai-provider**

You can configure the appropriate API key in your environment file:


```dotenv
# Support for: anthropic, gemini, openai, openai-responses, mistral, ollama, huggingface, deepseek
NEURON_PROVIDER=anthropic

ANTHROPIC_KEY=
GEMINI_KEY=
OPENAI_KEY=
MISTRAL_KEY=
OLLAMA_URL=
# And many others
```

## Migrations

The package ships with a ready-to-use migration for the `ElquentChatHistory` component. Here is the command to copy the migration
in your project `database/migrations/neuron` folder:

```
php artisan vendor:publish --tag=neuron-migrations
```

And then run the migrations:

```
php artisan migrate --path=/database/migrations/my-package
```

Read more about Eloquent Chat History in the Neuron AI documentation: **https://docs.neuron-ai.dev/the-basics/chat-history-and-memory#eloquentchathisotry**




