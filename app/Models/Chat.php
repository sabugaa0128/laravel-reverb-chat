<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $timestamps = true;

    protected $table = 'chats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'recipient_id',
        'message',
        'status'
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate the private channel name between two users
     */
    public static function generateChannelName($userId1, $userId2) {
        $ids = [$userId1, $userId2];

        // To avoid ambiguity, you might want to sort the user IDs so that the lower ID always comes first. This ensures that both users listen and broadcast to the same channel name regardless of who initiates the chat.
        sort($ids);
        return 'chat-channel.' . implode('_', $ids);
    }
}
