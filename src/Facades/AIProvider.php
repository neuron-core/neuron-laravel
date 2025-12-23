<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use NeuronAI\Providers\AIProviderInterface;

/**
 * @mixin AIProviderInterface
 */
class AIProvider extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \NeuronAI\Laravel\AIProvider::class;
    }
}
