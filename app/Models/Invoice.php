<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
      'uuid',
      'user_id',
      'title',
      'description',
      'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderByDesc('created_at');
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class)->orderByDesc('created_at');
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
