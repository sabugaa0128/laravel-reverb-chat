<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/**
 * Authorizing Channels
 * DOC: https://laravel.com/docs/11.x/broadcasting#example-application-authorizing-channels
 */
Broadcast::channel('chat-channel.{ids}', function ($user, $ids) {
    [$userId1, $userId2] = explode('_', $ids);
    return $user->id == $userId1 || $user->id == $userId2;
});
