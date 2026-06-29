<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use NeuronAI\Agent\Agent;
use NeuronAI\Agent\AgentHandler;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;
use NeuronAI\Workflow\Middleware\WorkflowMiddleware;
use NeuronAI\Workflow\NodeInterface;

use function tap;

class Neuron
{
    /** @var array<ToolInterface|ToolkitInterface> */
    protected array $tools = [];

    /**
     * Pending node-middleware registrations, each a pair of
     * [node class(es), middleware instance(s)].
     *
     * @var array<int, array{string|array<string>, WorkflowMiddleware|array<WorkflowMiddleware>}>
     */
    protected array $middleware = [];

    public function __construct(
        protected AIProviderInterface $provider,
        protected ?string $instructions = null,
    ) {}

    /**
     * @param ToolInterface|ToolkitInterface|array<ToolInterface|ToolkitInterface> $tools
     */
    public function tools(ToolInterface|ToolkitInterface|array $tools): static
    {
        return tap(clone $this, fn (self $clone) => $clone->tools = is_array($tools) ? $tools : [$tools]);
    }

    /**
     * @param class-string<NodeInterface>|array<class-string<NodeInterface>> $node
     * @param WorkflowMiddleware|array<WorkflowMiddleware> $middleware
     */
    public function middleware(string|array $node, WorkflowMiddleware|array $middleware): static
    {
        return tap(clone $this, fn (self $clone) => $clone->middleware[] = [$node, $middleware]);
    }

    private function makeAgent(): Agent
    {
        $agent = Agent::make();
        $agent->setAiProvider($this->provider);
        if ($this->instructions !== null) {
            $agent->setInstructions($this->instructions);
        }
        if ($this->tools !== []) {
            $agent->addTool($this->tools);
        }
        foreach ($this->middleware as [$node, $middleware]) {
            $agent->addMiddleware($node, $middleware);
        }
        return $agent;
    }

    public function chat(mixed $message): AgentHandler
    {
        return $this->makeAgent()->chat($message);
    }

    public function stream(mixed $message): AgentHandler
    {
        return $this->makeAgent()->stream($message);
    }

    /**
     * @throws \Throwable
     * @throws AgentException
     */
    public function structured(mixed $message, string $outputClass): mixed
    {
        return $this->makeAgent()->structured($message, $outputClass);
    }
}
