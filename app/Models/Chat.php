<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $timestamps = true;

    protected $table = 'chat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'user_id',
        'recipient_id'
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
