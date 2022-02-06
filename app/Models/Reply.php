<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'message_id',
        'sender_id'
    ];

    protected $hidden = ['sender_id'];

    protected $with = ['sender'];

    public function message(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
      return $this->belongsTo(Message::class);
    }

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'sender_id');
    }
}
