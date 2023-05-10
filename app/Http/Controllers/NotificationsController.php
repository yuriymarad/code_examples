<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Notification\ReadByIdRequest;

class NotificationsController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(auth()->user()->notifications);
    }

    public function readAll(): JsonResponse
    {
        $user = auth()->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return response()->json($user->notifications);
    }

    public function readById(ReadByIdRequest $request): void
    {
        $notification = auth()->user()->notifications()->where('id', $request->id)->first();
        $notification->markAsRead();
    }
}
