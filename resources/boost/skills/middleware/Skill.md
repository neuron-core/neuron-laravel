---
name: Neuron AI Middleware
description: Use when you want intercept workflow nodes execution. Use for things like tool approval, guardrails, context summarization.
---

# Neuron AI Middleware

Middleware wraps node execution with before/after hooks.

## Middleware Interface

```php
use NeuronAI\Workflow\Middleware\WorkflowMiddleware;
use NeuronAI\Workflow\NodeInterface;
use NeuronAI\Workflow\Events\Event;
use NeuronAI\Workflow\WorkflowState;

interface WorkflowMiddleware
{
    public function before(NodeInterface $node, Event $event, WorkflowState $state): void;
    public function after(NodeInterface $node, Event $event, Event|Generator $result, WorkflowState $state): void;
}
```

## Execution Flow

```
Event → Middleware1::before() → Middleware2::before() → Node
     → Middleware1::after() → Middleware2::after() → Next Event
```

## Registration

```php
use NeuronAI\Agent\Agent;
use NeuronAI\Agent\Nodes\ToolNode;

// Node-specific middleware
$agent = Agent::make()
    ->addMiddleware(ToolNode::class, new LoggingMiddleware());

// Multiple middleware on one node
$agent->addMiddleware(ToolNode::class, [
    new LoggingMiddleware(),
    new ValidationMiddleware(),
]);

// Global middleware (runs on all nodes)
$agent->addGlobalMiddleware(new PerformanceMiddleware());
```

## Built-in Middleware

### ToolApproval

Human-in-the-loop approval for tool execution:

```php
use NeuronAI\Agent\Middleware\ToolApproval;

// All tools require approval (default)
$agent->addMiddleware(ToolNode::class, new ToolApproval());

// Specific tools require approval
$agent->addMiddleware(ToolNode::class, new ToolApproval([
    'delete_file',           // By name
    TransferMoneyTool::class,     // By class name
]));

// Conditional approval based on arguments
$agent->addMiddleware(ToolNode::class, new ToolApproval([
    TransferMoneyTool::class => fn(array $args) => $args['amount'] > 1000,
    'delete_file' => fn(array $args) => str_contains($args['path'], '/etc/'),
]));

// Mix of unconditional and conditional
$agent->addMiddleware(ToolNode::class, new ToolApproval([
    'delete_file',
    TransferMoneyTool::class => fn(array $args) => $args['amount'] > 1000,
]));
```

### ToolApproval Constructor

```php
new ToolApproval(
    tools: []  // Empty means all tools require approval
);
```

**Tools parameter options:**
- Empty array `[]` - All tools require approval (default)
- Numeric key + string - Tool name or class string always requires approval
- String key + callable - Tool requires approval when callback returns `true`

**Action Decisions:**
- `ActionDecision::Pending` - Waiting for human decision
- `ActionDecision::Approved` - Tool will execute
- `ActionDecision::Rejected` - Tool callback replaced with rejection message
- `ActionDecision::Edit` - Tool inputs modified by human

### TodoPlanning

Inject task planning capabilities:

```php
use NeuronAI\Agent\Middleware\TodoPlanning;

$agent->addMiddleware(ChatNode::class, new TodoPlanning());
```

### Summarization

Add conversation summarization:

```php
use NeuronAI\Agent\Middleware\Summarization;

$agent->addMiddleware(ChatNode::class, new Summarization());
```

## Custom Middleware

Create custom middleware by implementing `WorkflowMiddleware`:

```php
use NeuronAI\Workflow\Middleware\WorkflowMiddleware;
use NeuronAI\Workflow\NodeInterface;
use NeuronAI\Workflow\Events\Event;
use NeuronAI\Workflow\WorkflowState;

class LoggingMiddleware implements WorkflowMiddleware
{
    public function before(NodeInterface $node, Event $event, WorkflowState $state): void
    {
        echo "Starting node: " . $node::class . "\n";
    }

    public function after(NodeInterface $node, Event $event, Event|Generator $result, WorkflowState $state): void
    {
        echo "Finished node: " . $node::class . "\n";
    }
}
```

## Accessing Resume Requests

Middleware can access resume request when workflows are resumed:

```php
use NeuronAI\Workflow\Interrupt\InterruptRequest;

class ApprovalMiddleware implements WorkflowMiddleware
{
    public function before(NodeInterface $node, Event $event, WorkflowState $state): void
    {
        if ($node->isResuming()) {
            $request = $node->getResumeRequest(); // ?InterruptRequest
            // Process human decisions
        }
    }

    public function after(NodeInterface $node, Event $event, Event|Generator $result, WorkflowState $state): void
    {
        // Post-execution logic
    }
}
```
