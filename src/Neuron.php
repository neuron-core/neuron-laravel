<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use NeuronAI\Agent\Agent;
use NeuronAI\Agent\AgentHandler;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

class Neuron
{
    /** @var array<ToolInterface|ToolkitInterface> */
    protected array $tools = [];

    public function __construct(
        protected AIProviderInterface $provider,
        protected ?string $instructions = null,
    ) {}

    /**
     * @param ToolInterface|ToolkitInterface|array<ToolInterface|ToolkitInterface> $tools
     */
    public function tools(ToolInterface|ToolkitInterface|array $tools): static
    {
        $clone = clone $this;
        $clone->tools = is_array($tools) ? $tools : [$tools];
        return $clone;
    }

    private function makeAgent(): Agent
    {
        $agent = Agent::make();
        $agent->setAiProvider($this->provider);
        if ($this->instructions !== null) {
            $agent->setInstructions($this->instructions);
        }
        if ($this->tools !== []) {
            $agent->setTools($this->tools);
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
