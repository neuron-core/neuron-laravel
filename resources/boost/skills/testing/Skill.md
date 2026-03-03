---
name: Neuron AI Testing
description: Use when writing unit tests, mocking LLM responses, testing workflows, or verifying agent behavior.
---

# Neuron AI Testing

Testing utilities and patterns for Neuron AI applications.

## FakeAIProvider

Mock LLM responses for deterministic tests:

```php
use NeuronAI\Testing\FakeAIProvider;
use NeuronAI\Chat\Messages\AssistantMessage;
use NeuronAI\Agent\Agent;

$fakeProvider = new FakeAIProvider(
    new AssistantMessage('Test response')
);

$agent = Agent::make()->setAiProvider($fakeProvider);
$result = $agent->chat($message)->run();

$message = $result->getLastMessage();
$this->assertEquals('Test response', $message->getContent());
```

## Adding Multiple Responses

Responses are used sequentially:

```php
$fakeProvider = new FakeAIProvider(
    new AssistantMessage('First response'),
    new AssistantMessage('Second response'),
    new AssistantMessage('Third response')
);

// First call returns 'First response'
$result1 = $agent->chat($msg1)->run();

// Second call returns 'Second response'
$result2 = $agent->chat($msg2)->run();
```

Or add responses dynamically:

```php
$fakeProvider = new FakeAIProvider();
$fakeProvider->addResponses(
    new AssistantMessage('Response 1'),
    new AssistantMessage('Response 2')
);
```

## Streaming Tests

```php
$fakeProvider = new FakeAIProvider(
    new AssistantMessage('Hello world!')
);

// Set custom chunk size (default is 5)
$fakeProvider->setStreamChunkSize(3);

foreach ($agent->stream($message)->events() as $event) {
    // Yields: 'Hel', 'lo ', 'wor', 'ld!', then final message
}
```

## Structured Output Tests

```php
use NeuronAI\Chat\Messages\AssistantMessage;

$fakeProvider = new FakeAIProvider(
    new AssistantMessage('{"name":"John","age":30}')
);

$person = $agent->structured($message, Person::class);
$this->assertEquals('John', $person->name);
```

## Tool Call Testing

Configure tools and verify they were called:

```php
$fakeProvider = new FakeAIProvider(
    new AssistantMessage('Result after tool call')
);

$agent = Agent::make()
    ->setAiProvider($fakeProvider)
    ->addTool(CalculatorToolkit::make());

$result = $agent->chat($message)->run();

// Verify tools were configured
$fakeProvider->assertToolsConfigured(['calculator']);
```

## Assertions

FakeAIProvider provides assertion methods:

```php
// Assert total call count
$fakeProvider->assertCallCount(2);

// Assert specific method was called X times
$fakeProvider->assertMethodCallCount('chat', 2);
$fakeProvider->assertMethodCallCount('stream', 1);

// Assert no calls were made
$fakeProvider->assertNothingSent();

// Assert a call matching a callback was made
$fakeProvider->assertSent(function (RequestRecord $record) {
    return $record->method === 'chat' && count($record->messages) > 0;
});

// Assert system prompt was set
$fakeProvider->assertSystemPrompt('You are a helpful assistant.');

// Assert tools were configured
$fakeProvider->assertToolsConfigured(['calculator', 'search']);
```

## Recording Requests

Access recorded requests for custom assertions:

```php
$fakeProvider = new FakeAIProvider(
    new AssistantMessage('Response')
);

$agent->chat($message)->run();

$records = $fakeProvider->getRecorded();
$this->assertCount(1, $records);

$firstCall = $records[0];
$this->assertEquals('chat', $firstCall->method);
$this->assertCount(1, $firstCall->messages);
```

## RequestRecord Properties

```php
class RequestRecord
{
    public string $method;          // 'chat', 'stream', or 'structured'
    public array $messages;         // Messages sent to provider
    public ?string $systemPrompt;   // System prompt used
    public array $tools;           // Tools configured
    public ?string $structuredClass; // For structured output calls
    public ?array $structuredSchema; // JSON schema for structured output
}
```

## Test Base Class

Create reusable test setup:

```php
use NeuronAI\Agent\Agent;
use NeuronAI\Testing\FakeAIProvider;
use PHPUnit\Framework\TestCase;

abstract class AgentTestCase extends TestCase
{
    protected function createAgent(?FakeAIProvider $provider = null): Agent
    {
        return Agent::make()
            ->setAiProvider($provider ?? new FakeAIProvider());
    }

    protected function assertProviderCalled(int $times, FakeAIProvider $provider): void
    {
        $provider->assertCallCount($times);
    }
}
```

## FakeVectorStore

Mock vector operations:

```php
use NeuronAI\Testing\FakeVectorStore;
use NeuronAI\RAG\Document;

$vectorStore = new FakeVectorStore();
$document = new Document('Test content');

$vectorStore->addDocument($document);

$results = $vectorStore->similaritySearch([/* embedding */]);
$this->assertNotEmpty($results);
```

## FakeEmbeddingsProvider

Mock embedding generation:

```php
use NeuronAI\Testing\FakeEmbeddingsProvider;

$embeddings = new FakeEmbeddingsProvider([0.1, 0.2, 0.3]);
$vector = $embeddings->embedText('test');

$this->assertEquals([0.1, 0.2, 0.3], $vector);
```

## Key File Locations

- `src/Testing/FakeAIProvider.php` - Mock LLM provider with assertions
- `src/Testing/FakeVectorStore.php` - Mock vector store
- `src/Testing/FakeEmbeddingsProvider.php` - Mock embeddings provider
- `src/Testing/RequestRecord.php` - Request recording class
- `src/Testing/FakeMessageMapper.php` - Mock message mapper
- `src/Testing/FakeToolMapper.php` - Mock tool mapper

## Best Practices

- Use fake providers for unit tests (deterministic, fast)
- Use real providers only for integration tests
- Reset fake provider state between tests
- Test edge cases (empty input, null values, errors)
- Use assertions to verify expected behavior
- Record and inspect requests for debugging
