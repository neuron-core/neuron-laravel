<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Tests;

use NeuronAI\Agent\Agent;
use NeuronAI\Agent\Nodes\ChatNode;
use NeuronAI\Laravel\Neuron;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Workflow\Events\Event;
use NeuronAI\Workflow\Middleware\WorkflowMiddleware;
use NeuronAI\Workflow\NodeInterface;
use NeuronAI\Workflow\WorkflowState;
use ReflectionClass;

/**
 * Minimal, dependency-free middleware used only to assert wiring.
 */
class MarkerMiddleware implements WorkflowMiddleware
{
    public function __construct(public readonly string $marker)
    {
    }

    public function before(NodeInterface $node, Event $event, WorkflowState $state): void
    {
    }

    public function after(NodeInterface $node, Event $result, WorkflowState $state): void
    {
    }
}

class NeuronTest extends BasicTestCase
{
    private function neuron(): Neuron
    {
        return new Neuron(new OpenAI('xxx', 'gpt-4'));
    }

    /**
     * Invoke the private makeAgent() to inspect the Agent the service builds.
     */
    private function makeAgent(Neuron $neuron): Agent
    {
        $method = (new ReflectionClass($neuron))->getMethod('makeAgent');

        return $method->invoke($neuron);
    }

    /**
     * Tools wired into the Agent built for the given Neuron instance.
     *
     * @return array<ToolInterface>
     */
    private function agentTools(Neuron $neuron): array
    {
        return $this->makeAgent($neuron)->getTools();
    }

    /**
     * Node-specific middleware wired into the Agent built for the given instance.
     *
     * @return array<class-string<NodeInterface>, WorkflowMiddleware[]>
     */
    private function nodeMiddleware(Agent $agent): array
    {
        $property = (new ReflectionClass($agent))->getProperty('nodeMiddleware');
        $property->setAccessible(true);

        /** @var array<class-string<NodeInterface>, WorkflowMiddleware[]> $value */
        $value = $property->getValue($agent);

        return $value;
    }

    public function test_tools_returns_a_clone_and_leaves_the_original_untouched(): void
    {
        $neuron = $this->neuron();
        $tool = new Tool('search');

        $withTools = $neuron->tools($tool);

        $this->assertNotSame($neuron, $withTools);
        $this->assertSame(
            ['search'],
            array_map(fn (ToolInterface $t): string => $t->getName(), $this->agentTools($withTools)),
        );
        // Original instance keeps its previous (empty) tools.
        $this->assertSame(
            [],
            array_map(fn (ToolInterface $t): string => $t->getName(), $this->agentTools($neuron)),
        );
    }

    public function test_middleware_returns_a_clone_and_leaves_the_original_untouched(): void
    {
        $neuron = $this->neuron();

        $withMiddleware = $neuron->middleware(ChatNode::class, new MarkerMiddleware('m1'));

        $this->assertNotSame($neuron, $withMiddleware);
        $this->assertSame([], $this->nodeMiddleware($this->makeAgent($neuron)));
    }

    public function test_middleware_accumulates_across_chained_calls(): void
    {
        $neuron = $this->neuron()
            ->middleware(ChatNode::class, new MarkerMiddleware('m1'))
            ->middleware(ChatNode::class, new MarkerMiddleware('m2'));

        $forNode = $this->nodeMiddleware($this->makeAgent($neuron))[ChatNode::class] ?? [];

        $this->assertSame(
            ['m1', 'm2'],
            array_map(fn (WorkflowMiddleware $m): string => $this->marker($m), $forNode),
        );
    }

    public function test_tools_are_injected_into_the_agent(): void
    {
        $neuron = $this->neuron()->tools([new Tool('search'), new Tool('save')]);

        $this->assertSame(
            ['search', 'save'],
            array_map(fn (ToolInterface $t): string => $t->getName(), $this->agentTools($neuron)),
        );
    }

    public function test_middleware_is_injected_into_the_agent_for_the_target_node(): void
    {
        $neuron = $this->neuron()->middleware(ChatNode::class, new MarkerMiddleware('m1'));

        $forNode = $this->nodeMiddleware($this->makeAgent($neuron))[ChatNode::class] ?? [];

        $this->assertCount(1, $forNode);
        $this->assertInstanceOf(MarkerMiddleware::class, $forNode[0]);
        $this->assertSame('m1', $forNode[0]->marker);
    }

    private function marker(WorkflowMiddleware $middleware): string
    {
        return $middleware instanceof MarkerMiddleware ? $middleware->marker : '';
    }
}
