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
    if ($user->id == $userId1 || $user->id == $userId2) {
        return ['id' => $user->id, 'name' => $user->name];
    }
}, ['guards' => ['web']]);

