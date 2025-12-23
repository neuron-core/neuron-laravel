# Utility Package for using Neuron in Laravel applications

This package provides some useful helpers and configuration options to get started with Neuron AI 
in a more familiar way for Laravel developers. The package is built to help developers during the 
initial setup and configuration of their projects, it's not meant to be a complete replacement for the 
Neuron AI components. Neuron doesn't need such invasive abstractions. They can only be an obstacle to learning and using the AI components 
or for modeling your agentic logic.

In this package we provide you with a ready-to-use configuration file, artisan command to quickly create a new AI agent, 
workflow, middleware, and other components. You can also find ready-to-run migration if you want to use the Eloquent Chat History component, 
and other useful helpers.

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

<a name="configuration"></a>

## Configuration file

If you want to customize the configuration file beyond the environment variables, you can copy the package configuration file
in your project `config/neuron.php` folder:

```
php artisan vendor:publish --tag=neuron-config
```

<a name="agent"></a>

## Create an Agent

To create a new AI agent, run the following command:

```
php artisan neuron:agent MyAgent
```

This will create a new agent class in your `app/Neuron/Agents` folder with the name `MyAgent.php` and a couple of 
basic methods inside.

<a name="providers"></a>

## AI Providers

To get an instance of AI provider to be used in your agents, you can use the `NeuronAI\Laravel\AIProvider` service class.
It allows you to get an instance of the provider based on the configuration file.

```php
use NeuronAI\Laravel\AIProvider;

// Get the default provider
$provider = AIProvider::driver();

// Get a specific provider instance
$provider = AIProvider::driver('anthropic');
```

The configuration file allows you to configure the default AI provider you want to use in your agents, and the 
connection parameters (API key, model, etc.) for all the providers you want to use.

Neuron allows you to implement AI agents using many different providers, like Anthropic, Gemini, OpenAI, Ollama, Mistral, and many more.
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

<a name="migrations"></a>

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
