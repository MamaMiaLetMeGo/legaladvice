<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'status',
        'ip_address',
        'last_message_at',
        'session_id',
        'is_guest'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_guest' => 'boolean'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
}
