<?php

declare(strict_types=1);

namespace NeuronAI\Laravel;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;

class Neuron
{
    public function __construct(
        protected AIProviderInterface $provider,
        protected ?string $instructions = null,
    ) {}

    private function makeAgent(): Agent
    {
        $agent = Agent::make();
        $agent->setAiProvider($this->provider);
        if ($this->instructions !== null) {
            $agent->setInstructions($this->instructions);
        }
        return $agent;
    }

    public function chat(mixed $message): mixed
    {
        return $this->makeAgent()->chat($message);
    }

    public function stream(mixed $message): mixed
    {
        return $this->makeAgent()->stream($message);
    }

    public function structured(mixed $message, string $outputClass): mixed
    {
        return $this->makeAgent()->structured($message, $outputClass);
    }
}
