<?php

namespace NeuronAI\Laravel\Tests;

use NeuronAI\Laravel\Facades\AIProvider;
use NeuronAI\Laravel\Facades\EmbeddingProvider;
use NeuronAI\Laravel\Facades\VectorStore;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\VectorStore\PineconeVectorStore;

class FacadeTest extends BasicTestCase
{
    public function testProvider(): void
    {
        $this->assertInstanceOf(OpenAI::class, AIProvider::driver('openai'));
    }

    public function testEmbedding(): void
    {
        $this->assertInstanceOf(OpenAIEmbeddingsProvider::class, EmbeddingProvider::driver('openai'));
    }

    public function testVectorStore(): void
    {
        $this->assertInstanceOf(PineconeVectorStore::class, VectorStore::driver('pinecone'));
    }
}
