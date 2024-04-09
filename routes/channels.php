<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Chat;

/**
 * Authorizing Channels
 * DOC: https://laravel.com/docs/11.x/broadcasting#example-application-authorizing-channels
 */
Broadcast::channel('chat-channel.{ids}', function (User $user, $ids) {
    [$userId1, $userId2] = explode('_', $ids);
    if ( auth()->check() && ($user->id == $userId1 || $user->id == $userId2) ) {
        return [
            'id' => $user->id,
            'name' => $user->name
        ];
    }
    return false;
}, ['guards' => ['web']]);


// List online users.
Broadcast::channel('users-online', function (User $user) {
    if (auth()->check()) {
        return [
            'id' => $user->id,
            'name' => $user->name
        ];
    }
    return false;
}, ['guards' => ['web']]);

