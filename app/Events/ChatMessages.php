<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessages implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     * DOC: https://laravel.com/docs/11.x/broadcasting#the-shouldbroadcast-interface
     *
     * Get the channel the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat-channel.' . $this->user->id);
    }

    /**
     * If are multiple channesls use arra
     */
    /*public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }*/

    /**
     * The Broadcast Name
     * DOC: https://laravel.com/docs/11.x/broadcasting#broadcast-name
     */
    public function broadcastAs()
    {
        return 'chat.messages';
    }

}
