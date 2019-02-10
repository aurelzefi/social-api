<?php

namespace App\Http\Controllers;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(request()->user()->notifications);
    }

    /**
     * Display the specified notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json(request()->user()->notifications()->find($id));
    }

    /**
     * Read the notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        request()->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(request()->user()->notifications);
    }

    /**
     * Unread the notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread()
    {
        request()->user()->readNotifications()->update(['read_at' => null]);

        return response()->json(request()->user()->notifications);
    }
}
