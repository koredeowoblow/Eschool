<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    $match = (string) $user->id === (string) $id;
    \Illuminate\Support\Facades\Log::info('Private Chat Auth:', [
        'user_id' => $user->id,
        'channel_id' => $id,
        'match' => $match
    ]);
    return $match;
});
