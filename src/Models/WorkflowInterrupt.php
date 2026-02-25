<?php

namespace NeuronAI\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowInterrupt extends Model
{
    protected $fillable = ['workflow_id', 'interrupt'];
}
