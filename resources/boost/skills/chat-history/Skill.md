---
name: Neuron AI Chat History
description: Use when implementing conversation memory, managing chat history storage, or configuring persistent message history.
---

# Neuron AI Chat History

Manage conversation memory with multiple storage backends.

## Storage Options

## FileChatHistory Configuration

FileChatHistory stores conversations as JSON files in a directory:

```php
use NeuronAI\Chat\History\FileChatHistory;

class MyAgent extends Agent
{
    ...
    
    protected function chatHistory(): ChatHistoryInterface
    {
        return new FileChatHistory(
            directory: '/var/storage/chats',  // Directory for files (created if not exists)
            key: 'unique_identifier',        // Unique key for this conversation
            contextWindow: 100000,              // Token limit for history trimming
            prefix: 'neuron_',                 // File name prefix
            ext: '.chat'                       // File extension
        );
    }
}

// File path will be: /var/storage/chats/neuron_unique_identifier.chat
```

## SQLChatHistory Setup

Store conversation history in a SQL database.

```php
use NeuronAI\Chat\History\SQLChatHistory;

class MyAgent extends Agent
{
    ...
    
    protected function chatHistory(): ChatHistoryInterface
    {
        return new SQLChatHistory(
            thread_id: 'THREAD_ID',
            pdo: new \PDO("mysql:host=localhost;dbname=DB_NAME;charset=utf8mb4", "DB_USER", "DB_PASS"),
            table: 'chat_hisotry',
            contextWindow: 100000
        );
    }
}
```

Requires a database table with the following schema:

```sql
CREATE TABLE chat_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    thread_id VARCHAR(255) NOT NULL,
    messages LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_thread_id (thread_id),
    INDEX idx_thread_id (thread_id)
);
```

Supports MySQL, PostgreSQL, and SQLite via PDO.

## EloquentChatHistory Setup

Use Eloquent ORM in Laravel applications as the interaction layer with the database.

```php
use NeuronAI\Chat\History\EloquentChatHistory;

class MyAgent extends Agent
{
    ...
    
    protected function chatHistory(): ChatHistoryInterface
    {
        return new EloquentChatHistory(
            thread_id: 'THREAD_ID',
            modelClass: ChatMessage::class,
            contextWindow: 100000
        );
    }
}
```

Requires an Eloquent model with these columns:

```php
<?php

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'thread_id',
        'role',
        'content',
        'meta',
    ];
}
```

Table schema:

```sql
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    thread_id VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    content LONGTEXT,
    meta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_thread_id (thread_id)
);
```

## Manual Management

```php
use NeuronAI\Chat\History\InMemoryChatHistory;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\AssistantMessage;

$history = new InMemoryChatHistory();

// Add messages
$history->addMessage(new UserMessage('Hello'));
$history->addMessage(new AssistantMessage('Hi there!'));

// Get all messages
$allMessages = $history->getMessages();

// Get the last message
$lastMessage = $history->getLastMessage();

// Get total token usage
$totalTokens = $history->calculateTotalUsage();

// Clear all messages
$history->flushAll();
```

## Content Block Support

All content blocks (Text, Image, File, Audio, Video, Reasoning) are preserved automatically:

```php
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\ContentBlocks\TextContent;
use NeuronAI\Chat\Messages\ContentBlocks\ImageContent;
use NeuronAI\Chat\Enums\SourceType;

$message = new UserMessage([
    new TextContent('Analyze this chart:'),
    new ImageContent(
        content: 'https://example.com/chart.jpg',
        sourceType: SourceType::URL,
        mediaType: 'image/jpeg'
    ),
    new TextContent('What trends do you see?')
]);

$history = new FileChatHistory('/storage', 'analysis');
$history->addMessage($message);  // Blocks preserved on retrieval

// Later, retrieve and blocks are deserialized correctly
$retrieved = $history->getMessages();
```

## Implement a custom Chat History

```php
class CustomChatHistory extends AbstractChatHistory
{
    /**
     * @param Message[] $messages
     */
    protected function setMessages(array $messages): void
    {
        // Handle saving the entire history at once every time the history is updated.
    }

    protected function onNewMessage(Message $message): void
    {
        // Handle single message addition
    }

    protected function onTrimHistory(int $index): void
    {
        // When the trim is triggered, 
        // the messages in the position from zero to $index must be removed.
    }

    protected function clear(): void
    {
        // Remove all messages.
    }
}
```

## Best Practices

- Use `InMemoryChatHistory` for testing only (no persistence)
- Set reasonable `contextWindow` (default 100000 tokens) to avoid token bloat
- Use unique keys for each conversation/thread in persistent storage
- For long-term knowledge storage, consider RAG instead of long chat history
- Use summarization middleware for long conversations (see middleware skill)
- File-based history uses `LOCK_EX` for thread safety when possible
