---
name: Neuron AI Workflow
description: Use when building event-driven workflows, creating custom nodes, implementing middleware, handling interruptions, or working with workflow state management.
---

# Neuron AI Workflow

Workflow is the foundation of Neuron AI - an event-driven orchestration system that powers both Agent and RAG components.

## Creating a Workflow

```php
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;

// Create workflow with optional state, persistence, and resume token
$state = new WorkflowState(['initial' => 'data']);
$workflow = Workflow::make(state: $state);

// Or with persistence for resumable workflows
$persistence = new FilePersistence('/tmp/workflows');
$workflow = Workflow::make(
    persistence: $persistence,
    resumeToken: 'workflow_id_123',
    state: $state
);
```

## Node Implementation

```php
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class MyProcessingNode extends Node
{
    // First parameter type determines which event this handles
    public function __invoke(MyInputEvent $event, WorkflowState $state): StopEvent
    {
        $result = $this->process($event->getData());
        $state->set('result', $result);
        return new StopEvent($result);
    }
}
```

## Workflow Execution

```php
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\StopEvent;

// Add nodes and execute
$workflow = Workflow::make()
    ->addNodes([new ValidationNode(), new ProcessingNode()]);

$handler = $workflow->init();
$finalState = $handler->run();
```

## Streaming Support

```php
class StreamingNode extends Node
{
    public function __invoke(ProcessEvent $event, WorkflowState $state): \Generator
    {
        yield new ProgressChunk('Starting...');
        // Process...
        yield new ProgressChunk('Done!');
        return new StopEvent();
    }
}

$handler = $workflow->init();
foreach ($handler->events() as $event) {
    // Handle streaming events
}
$finalState = $handler->run();
```

## Middleware Registration

```php
use NeuronAI\Agent\Nodes\ToolNode;
use NeuronAI\Workflow\Middleware\WorkflowMiddleware;

// Node-specific middleware
$workflow->middleware(ToolNode::class, new LoggingMiddleware());

// Multiple middleware on one node
$workflow->middleware(ProcessingNode::class, [
    new ValidationMiddleware(),
    new LoggingMiddleware(),
]);

// Global middleware (runs on all nodes)
$workflow->globalMiddleware(new PerformanceMiddleware());
```

## Interruption Support

```php
class ApprovalNode extends Node
{
    public function __invoke(DataEvent $event, WorkflowState $state): StopEvent
    {
        // Check if resuming from interruption
        if ($this->isResuming()) {
            $request = $this->getResumeRequest();
            // Handle user decisions
            return new StopEvent();
        }

        // Interrupt for approval
        $this->interrupt(new ApprovalRequest([
            new Action('approve', 'Approve', 'Confirm the action'),
        ], 'Action requires approval'));
    }
}
```

## Checkpoints

Cache expensive computations across interruptions:

```php
public function __invoke(DataEvent $event, WorkflowState $state): StopEvent
{
    // This runs only once, cached across resumptions
    $data = $this->checkpoint('fetch_data', fn() => $this->fetchExpensiveData());

    $this->interruptIf($needsApproval, $request);

    // When resumed, $data is retrieved from checkpoint
    return new StopEvent($this->process($data));
}
```

## Common Patterns

**Event-Driven**: Nodes emit events that route to appropriate next nodes
**Middleware**: Wrap nodes with cross-cutting concerns (logging, validation)
**Interruption**: Use `interrupt()` for human-in-the-loop workflows
**Checkpoints**: Cache expensive operations for resumable workflows
**Persistence**: Use `PersistenceInterface` for long-running workflows

## Export Workflow

```php
use NeuronAI\Workflow\Exporter\MermaidExporter;

$workflow->setExporter(new MermaidExporter());
$diagram = $workflow->export();
echo $diagram;  // Outputs Mermaid diagram
```
