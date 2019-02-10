<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserFollowed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Te follower implementation.
     *
     * @var \App\Models\User
     */
    protected $follower;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $follower
     * @return void
     */
    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->follower->toArray();
    }
}
