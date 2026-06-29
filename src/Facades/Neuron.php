<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \NeuronAI\Laravel\Neuron tools(\NeuronAI\Tools\ToolInterface|\NeuronAI\Tools\Toolkits\ToolkitInterface|array $tools)
 * @method static mixed chat(mixed $message)
 * @method static mixed stream(mixed $message)
 * @method static mixed structured(mixed $message, string $outputClass)
 */
class Neuron extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \NeuronAI\Laravel\Neuron::class;
    }
}
