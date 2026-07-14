<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSetting extends Model
{
    protected $table = 'ai_settings';

    protected $fillable = [
        'user_id',
        'temperature',
        'max_tokens',
        'system_prompt',
        'language',
        'answer_length',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
