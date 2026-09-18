<?php

declare(strict_types=1);

namespace NeuronAI\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStore extends Model
{
    protected $table = 'workflow_store';

    protected $fillable = [
        'partition', 'key', 'value'
    ];
}
