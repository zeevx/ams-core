<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
      'subject',
      'body',
      'user_id',
      'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $hidden = [
        'category_id',
        'user_id'
    ];

    protected $with = [
        'category'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(NoticeCategory::class);
    }
}
