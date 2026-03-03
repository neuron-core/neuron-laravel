---
name: Neuron AI RAG
description: Use when implementing retrieval-augmented generation, vector stores, document indexing, or semantic search capabilities.
---

# Neuron AI RAG

RAG extends Agent with semantic search and document retrieval capabilities.

## Basic Setup

```php
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\FileVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;

class MyChatBot extends RAG
{
    protected function provider(): AIProviderInterface
    {
        return new Anthropic(
            key: 'ANTHROPIC_API_KEY',
            model: 'ANTHROPIC_MODEL',
        );
    }

    protected function instructions(): string
    {
        return 'You are a helpful assistant.';
    }

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

    protected function tools(): array
    {
        return [
            // Tool::make(),
        ];
    }

    protected function postProcessors(): array
    {
        return [
            new JinaRerankerPostProcessor(
                key: 'JINA_API_KEY',
                model: 'JINA_MODEL',
                topN: 5
            ),
        ];
    }

    protected function retrieval(): \NeuronAI\RAG\Retrieval\RetrievalInterface
    {
        return new \NeuronAI\RAG\Retrieval\SimilarityRetrieval(
            vectorStore: $this->resolveVectorStore(),
            embeddingProvider: $this->resolveEmbeddingsProvider(),
        )
    }
}

$result = $rag->chat(new UserMessage('What is Neuron AI?'));
```

## Vector Stores

### MemoryVectorStore (In-Memory)

```php
use NeuronAI\RAG\VectorStore\MemoryVectorStore;

$vectorStore = new MemoryVectorStore(topK: 4);
```

### PineconeVectorStore

```php
use NeuronAI\RAG\VectorStore\PineconeVectorStore;

$vectorStore = new PineconeVectorStore(
    key: $apiKey,
    indexUrl: 'https://your-index.pinecone.io',
    topK: 5,
    version: '2025-04',
    namespace: '__default__',  // Optional namespace
    httpClient: null  // Optional custom HTTP client
);
```

### QdrantVectorStore

```php
use NeuronAI\RAG\VectorStore\QdrantVectorStore;

$vectorStore = new QdrantVectorStore(
    collectionUrl: 'http://localhost:6333/collections/my-collection/',
    key: null,  // Optional API key
    topK: 5,
    dimension: 1024,  // Vector dimension
    httpClient: null  // Optional custom HTTP client
);
```

### Other Vector Stores

Available: `ChromaVectorStore`, `ElasticsearchVectorStore`, `OpenSearchVectorStore`, `TypesenseVectorStore`, `MeiliSearchVectorStore`, `FileVectorStore`

## Embedding Providers

### OpenAI Embeddings

```php
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;

$embeddings = new OpenAIEmbeddingsProvider(
    key: $apiKey,
    model: 'text-embedding-3-small',
    dimensions: 1024,  // Optional: override default dimensions
    httpClient: null  // Optional custom HTTP client
);
```

### Ollama Embeddings

```php
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;

$embeddings = new OllamaEmbeddingsProvider(
    model: 'nomic-embed-text',
    url: 'http://localhost:11434/api',
    parameters: [],  // Optional: extra request parameters
    httpClient: null  // Optional custom HTTP client
);
```

### Other Embedding Providers

Available: `GeminiEmbeddingsProvider`, `MistralEmbeddingsProvider`, `VoyageEmbeddingsProvider`, `AwsBedrockEmbeddingsProvider`

## Configuration Options

```php
use NeuronAI\RAG\Retrieval;

$retrieval = new Retrieval(
    vectorStore: $vectorStore,
    embeddingProvider: $embeddingProvider,
    topK: 5,              // Retrieve 5 most relevant docs
    minSimilarity: 0.7,     // Filter by similarity score
);

$rag->setRetrieval($retrieval);
```

## Pre-Processors

Transform queries before retrieval:

```php
use NeuronAI\RAG\PreProcessor\QueryExpansionPreProcessor;

$rag->setPreProcessors([
    new QueryExpansionPreProcessor()
]);
```

## Post-Processors

Re-rank or filter retrieved documents:

```php
use NeuronAI\RAG\PostProcessor\FixedThresholdPostProcessor;

$rag->setPostProcessors([
    new FixedThresholdPostProcessor(0.8)
]);
```

## Document Management

```php
use NeuronAI\RAG\Document;

// Create custom document
$document = new Document('Your content here');
$document->setSourceType('pdf')
    ->setSourceName('document.pdf')
    ->addMetadata('author', 'John Doe');

// Add documents with chunking
$rag->addDocuments([$doc1, $doc2, ...], chunkSize: 50);

// Reindex by source (delete old, add new)
$rag->reindexBySource($documents, chunkSize: 50);
```

## Architecture

```
User Query
    → Query Transform (Pre-Processors)
    → Vector Search (VectorStore)
    → Document Retrieval
    → Rerank/Filter (Post-Processors)
    → Inject Context (InstructionsNode)
    → Agent (ChatNode/ToolNode)
    → Response
```
