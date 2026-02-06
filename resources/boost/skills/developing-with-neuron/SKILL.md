---
name: developing-with-neuron
description: Guide for developing with Neuron AI - PHP agentic framework for creating AI Agent, RAG, and agentic Workflow. Activate or use when working with Neuron AI features including text generation, structured output, embeddings, image generation, audio processing, streaming, tools/function calling, or any LLM provider integration (OpenAI, Anthropic, Gemini, Mistral, Groq, DeepSeek, OpenRouter, Ollama, VoyageAI, ElevenLabs, Huggingface, Cohere). Activate for any Neuron-related development tasks.
---

## Developing with Neuron AI

Neuron AI is a PHP Agentic framework for creating AI agents with features like chat history, tool integration, RAG (Retrieval Augmented Generation), structured output, and workflow orchestration. The codebase follows PSR-12 standards with strict typing and modern PHP 8.1+ features.

### Core Components

- `Agent` (NeurnoAI\Agent) - Provides chat, streaming, and structured output capabilities
- `RAG` (NeurnoAI\RAG\RAG) - Extends Agent with vector search and document retrieval capabilities
- `Workflow` (NeuronAI\Workflow\Workflow) - Provides event-driven node execution, persistence, streaming, human-in-the-loop interruptions

## Agent - NeuronAI\Agent

Create an AI agent with:

```
php artisan neuron:agent MyAgent
```

Here is a code example:

```php
namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        // return an instance of Anthropic, OpenAI, Gemini, Ollama, etc...
        return new Anthropic(
            key: config('neuron.anthropic.key'),
            model: config('neuron.anthropic.model'),
        );
    }
    
    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: ["You are a friendly AI Agent created with Neuron framework."],
        );
    }
    
    protected function tools(): array
    {
        return [
            // Add your tools here
        ];
    }
}
```

### Chat with the Agent

```php
use App\Neuron\MyAgent;
use NeuronAI\Chat\Messages\UserMessage;

/** @var \NeuronAI\Chat\Messages\AssistantMessage $response */
$response = MyAgent::make()->chat(new UserMessage("Hello!"));

echo $response->getContent();
```

## AI Providers

Neuron AI supports communication to multiple LLM services. All providers implement `NeuronAI\Providers\AIProviderInterface`.

Supported:
- NeuronAI\Providers\Anthropic\Anthropic
- NeuronAI\Providers\OpenAI\OpenAI
- NeuronAI\Providers\OpenAI\Responses\OpenAIResponses
- NeuronAI\Providers\Gemini\Gemini
- NeuronAI\Providers\Ollama\Ollama
- NeuronAI\Providers\HuggingFace\HuggingFace
- NeuronAI\Providers\Mistral\Mistral
- NeuronAI\Providers\XAI\Grok
- NeuronAI\Providers\Deepseek\Deepseek
- NeuronAI\Providers\AWS\BedrockRuntime

## Chat History

Neuron AI offers a pluggable memory system you can attach to the agent to manage the persistence of the chat history.

```php
namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;

class MyAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        ...
    }
    
    public function instructions(): string
    {
        ...
    }
    
    protected function tools(): array
    {
        ...
    }
    
    protected function chatHistory(): ChatHistoryInterface
    {
        return new InMemoryChatHistory();
    }
}
```

**Available Chat History Implementations**:

- InMemory: NeuronAI\Chat\History\InMemoryChatHistory
- File: NeuronAI\Chat\History\FileChatHistory
- SQL database: NeuronAI\Chat\History\SQLChatHistory
- Eloquent ORM: NeuronAI\Chat\History\EloquentChatHistory

### Eloquent Chat History

This package provides an `EloquentChatHistory` implementation that can be used to persist chat history in a database using Eloquent models.

First, you need to run the migration to create the necessary tables:

```
php artisan migrate --tag=neuron-migrations
```

You have to create a model that represents the table created, mapping a message in the database. Usually, it's `ChatMessage` model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'thread_id', 'role', 'content', 'meta'
    ];
    
    protected $casts = [
        'content' => 'array', 
        'meta' => 'array'
    ];
    
    /**
     * The conversation that owns the chat message.
     *
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'thread_id');
    }
}
```

The most important architectural decision is about the field `thread_id`. It's used to link chat messages to another entity, 
it could be directly related to a user, a company, or a project. If you want to allow an entity to have multiple conversations, you can use 
an intermediate model like `Conversation` that will hold the `thread_id` field. So an entity can have multiple conversations, and a conversation can handle the messages. 
You have to figure out how you want to design the system and what's the best approach for your use case.

## Structured Output

The schema that Neuron validates against is defined by PHP type hints. Basically, you have to define a class with 
strictly typed properties, using the `NeuronAI\StructuredOutput\SchemaProperty` attribute to define the property schema information.

```php
namespace App\Neuron\Output;

use NeuronAI\StructuredOutput\SchemaProperty;

class Person 
{
    #[SchemaProperty(description: 'The user name.', required: true)]
    public string $name;
    
    #[SchemaProperty(description: 'What the user love to eat.', required: false)]
    public string $preference;
}
```

You need to run the agent invoking the `structured` method that returns an object instance filled with appropriate data:

```php
use App\Neuron\Output\Person;
use NeuronAI\Chat\Messages\UserMessage;

// Talk to the agent requiring the structured output
$person = MyAgent::make()->structured(
    new UserMessage("I'm John and I like pizza!"),
    Person::class
);

echo $person->name.' like '.$person->preference;
// John like pizza
```

### Validation Rules

You can enforce validation rules on data extraction by using a set of validation attributes on the DTO class properties. 
Here is an example of the "NotBlank" validation rule:

```php
namespace App\Neuron\Output;

use NeuronAI\StructuredOutput\SchemaProperty;
use NeuronAI\StructuredOutput\Validation\Rules\NotBlank;

class Person 
{
    #[SchemaProperty(...)]
    #[NotBlank]
    public string $name;
}
```

**Available Validation Rules**:
#[NotBlank]
#[Length(min: 1, max: 10)]
#[WordsCount(min: 1, max: 10)]
#[Count(min: 1, max: 3)]
#[EqualTo(reference: 'Rome')] 
#[NotEqualTo(reference: 'Rome')]
#[GreaterThan(reference: 17)]
#[GreaterThanEqual(reference: 17)]
#[LowerThan(reference: 50)]
#[LowerThanEqual(reference: 50)]
#[OutOfRange(min: 18, max: 35)]
#[IsFalse]
#[IsTrue]
#[IsNotNull]
#[IsNull]
#[Json]
#[Url]
#[Email]
#[IpAddress]

### Array Validation
The property under validation must be an array that contains all of the given types of objects. **Notice that you also need 
to add the doc-block in order to make the agent able to instance the correct class**. Use the full class namespace in the doc-block.

```php
namespace App\Neuron\Output;

use NeuronAI\StructuredOutput\Validation\Rules\ArrayOf;

class Person
{
    /**
     * @var \App\Neuron\Output\Tag[]
     */
    #[ArrayOf(Tag::class)]
    public array $tags;
}
```

## Tool System

Extensible tool framework for agent capabilities. 
- Individual tools implement `NeuronAI\Tools\ToolInterface`
- Toolkits group related tools (NeuronAI\Tools\Toolkits\ToolkitInterface)
- Built-in toolkits: Calculator, MySQL, PostgreSQL, Tavily, Jina, Supadata 

### MCPConnector

`NeuronAI\MCP\MCPConnector` to connect tools to the agent provided by external MCP servers.

```php
use NeuronAI\MCP\McpConnector;

class MyAgent extends Agent 
{
    // Other agent methods (provider, instructions, chatHistory)...
    
    protected function tools(): array
    {
        return [
            ...McpConnector::make([
                'url' => 'https://mcp.example.com',
                'token' => 'BEARER_TOKEN',
                'timeout' => 30,
                'headers' => [
                    //'x-custom-header' => 'value'
                ]
            ])->tools(),
        ];
    }
}
```

## RAG - NeuronAI\RAG\RAG

Neuron has a dedicated component for RAG (Retrieval Augmented Generation) to implement documents retrieval from a 
vector store and augment them with contextual information. It extends the Agent component with a couple of methods to 
specify the connected vector store and the embeddings provider.

```php
namespace App\Neuron;

use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\FileVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

class MyChatBot extends RAG
{
    // Other agent methods (provider, instructions, tools, chatHistory)...
    
    protected function embeddings(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(
            key: 'OPENAI_API_KEY',
            model: 'OPENAI_MODEL'
        );
    }
    
    protected function vectorStore(): VectorStoreInterface
    {
        return new FileVectorStore(
            directory: __DIR__,
            name: 'demo'
        );
    }
}
```

**Available Vector Stores**:
- NeuronAI\RAG\VectorStore\MemoryVectorStore
- NeuronAI\RAG\VectorStore\FileVectorStore
- NeuronAI\RAG\VectorStore\PineconeVectorStore
- NeuronAI\RAG\VectorStore\ChromaVectorStore
- NeuronAI\RAG\VectorStore\QdrantVectorStore
- NeuronAI\RAG\VectorStore\MeilisearchVectorStore
- NeuronAI\RAG\VectorStore\TypesenseVectorStore
- NeuronAI\RAG\VectorStore\ElasticsearchVectorStore

**Available Embeddings Providers**:
- NeuronAI\RAG\Embeddings\AWSBedrockEmbeddingsProvider
- NeuronAI\RAG\Embeddings\GeminiEmbeddingsProvider
- NeuronAI\RAG\Embeddings\MistralEmbeddingsProvider
- NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider
- NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider
- NeuronAI\RAG\Embeddings\OpeAILikeEmbeddingsProvider
- NeuronAI\RAG\Embeddings\VoyageEmbeddingsProvider

## Workflow - NeuronAI\Workflow\Workflow

Neuron AI provides a powerful workflow orchestration system that allows you to build complex agentic workflow with a simple 
Event-Driven Architecture:

1. Each node receives a typed Event
2. Node processes the event and returns a new Event (or `NeuronAI\Workflow\Events\StopEvent` to complete)
3. The workflow routes the returned event to the appropriate next node
4. This continues until a StopEvent is returned

This design promotes loose coupling and makes workflows highly composable and testable.

Key methods:
- `start(): NeuronAI\Workflow\WorkflowHandler` - Initialize or resume workflow
- `addNode(NodeInterface $node): Workflow` - Register a node
- `addNodes(array $nodes): Workflow` - Register multiple nodes
- `setPersistence(PersistenceInterface $persistence, string $workflowId)` - Configure persistence
- `export(): string` - Export workflow structure to diagram format like Marmeid

### Node-Event Mapping

Nodes are automatically mapped to events through reflection:

```php
class InitialNode extends Node
{
    // The first __invoke argument determines which event this node handles
    public function __invoke(StartEvent $event, WorkflowState $state): ProcessEvent
    {
        // The return event determines the next node to execute
        return new ProcessEvent($validatedData);
    }
}
```

The workflow introspects the node class structure to build an event→node routing table.

### Create and run a workflow

1) Create nodes with:

```
php artisan neuron:node InitialNode
php artisan neuron:node NodeTwo
```

Nodes are simple classes, so you can eventually add constructor arguments if needed.

2) Create a workflow with:

```
php artisan neuron:workflow MyWorkflow
```

3) Add nodes to the workflow in the `nodes()` method:

```php
namespace App\Neuron\Workflows;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\Workflow;

class MyWorkflow extends Workflow
{
    /**
     * @return Node[]
     */
    protected function nodes(): array
    {
        return [
            new InitialNode(),
            new NodeTwo(),
        ];
    }
}
```

4) Execute the workflow and get the result:

```php
use NeuronAI\Workflow\Workflow;

$handler = Workflow::make()->start();

$result = $handler->getResult();
```
