<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'subject',
        'body',
        'user_id',
        'category_id',
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
        return $this->belongsTo(RequestCategory::class);
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RequestReply::class);
    }

    private static function generateUUID(): string
    {
        $uuid = Str::random(6);
        if (self::where('uuid',$uuid)->first()){
            self::generateUUID();
        }
        return $uuid;
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->uuid = self::generateUUID();
        });
    }
}
