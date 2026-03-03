---
name: Neuron AI Agent
description: Use when creating agents for chat, streaming responses, structured output extraction, or combining tools with LLM capabilities.
---

# Neuron AI Agent

Agent provides chat, streaming, and structured output capabilities built on top of Workflow.

## Creating an Agent

Create custom agents by extending the framework Agent class:

```php
use NeuronAI\Agent\Agent;

class WeatherAgent extends Agent
{
    protected function instructions(): string
    {
        return 'You are a weather assistant. Provide accurate weather information.';
    }

    protected function tools(): array
    {
        return [
            WeatherTool::make(),
        ];
    }

    protected function chatHistory(): ChatHistoryInterface
    {
        return new FileChatHistory(...);
    }

    protected function middleware(): array
    {
        return [
            ToolNode::class => [new ToolApproval()],
        ];
    }
}

$output = WeatherAgent::make()->chat(
    new UserMessage('What is the weather in Paris?')
)->getMessage();

echo $output->getContent();
```

## AgentHandler

All agent methods return an `AgentHandler` for workflow control:

```php
$handler = $agent->chat($message);

// Stream events in real-time
foreach ($handler->events() as $event) {
    // Handle events
}

// Get the final assistant message
$result = $state->getMessage();
```

## Fluent definition

```php
use NeuronAI\Agent\Agent;
use NeuronAI\Providers\Anthropic\Anthropic;

// Basic agent
$agent = Agent::make();

// With provider
$provider = new Anthropic($apiKey, 'claude-3-5-sonnet-20241022');
$agent = Agent::make()->setAiProvider($provider);
```

## Execution Modes

### Chat Mode

```php
use NeuronAI\Agent\Agent;
use NeuronAI\Chat\Messages\UserMessage;

// Single message
$message = WeatherAgent::make()
    ->chat(new UserMessage('Hello'))
    ->getMessage();

// Multiple messages
$message = WeatherAgent::make()
    ->chat([
        new UserMessage('Hello'),
        new AssistantMessage('Hi there!'),
        new UserMessage('How are you?')
    ])
    ->getMessage();
```

### Streaming Mode

```php
use NeuronAI\Chat\Messages\UserMessage;

$handler = WeatherAgent::make()->stream(new UserMessage('Tell me a story'));

foreach ($handler->events() as $event) {
    // Handle streaming chunks
    echo $event->getContent();
}

// After streaming, get the final message
$result = $handler->getMessage();
```

### Structured Output

```php
use NeuronAI\Agent\Agent;

class Person {
    #[SchemaProperty(description: 'Full name of the user.', required: true)]
    public string $name;

    #[SchemaProperty(description: 'The current age of the user.', required: true)]
    public int $age;
}

$person = WeatherAgent::make()->structured(
    messages: new UserMessage('John is 30 years old.'),
    class: Person::class,
    maxRetries: 3
);

// Returns Person object with populated properties
echo $person->name; // "John"
echo $person->age;  // 30
```

## Message Content Blocks

Agents support rich content with typed content blocks:

```php
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\ContentBlocks\TextContent;
use NeuronAI\Chat\Messages\ContentBlocks\ImageContent;
use NeuronAI\Chat\Enums\SourceType;

$message = new UserMessage([
    new TextContent('Analyze this image:'),
    new ImageContent(
        content: 'https://example.com/image.jpg',
        sourceType: SourceType::URL,
        mediaType: 'image/jpeg'
    )
]);

$agent->chat($message);
```

## Common Patterns

**Tool Execution**: Agent automatically routes to `ToolNode` when provider returns tool calls
**Content Blocks**: Support for text, reasoning, images, files, audio, video in messages
**Custom Agents**: Extend `Agent` with `provider()`, `instructions()`, `tools()`, `chatHistory()` methods
**History**: Persist conversations across sessions with `ChatHistoryInterface`
