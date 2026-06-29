<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
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
