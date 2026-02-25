<?php

namespace NeuronAI\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'thread_id', 'role', 'content', 'meta'
    ];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array'
    ];

    /*
     * Below there are example relationships based on the entity you want to attach the chat to.
     */

    /*public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'thread_id');
    }*/

    /*public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'thread_id');
    }*/
}
