<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'avatar', 'name', 'email', 'password', 'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'pivot',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the followers for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followings', 'followee_id', 'follower_id')
                    ->orderBy('followings.created_at')
                    ->withTimestamps();
    }

    /**
     * Get the followees for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followees()
    {
        return $this->belongsToMany(User::class, 'followings', 'follower_id', 'followee_id')
                    ->orderBy('followings.created_at')
                    ->withTimestamps();
    }

    /**
     * Get the posts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the liked posts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(Post::class, 'likes')->orderBy('likes.created_at')->withTimestamps();
    }

    /**
     * Get the sent messages for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the received messages for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the unread messages for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadMessages()
    {
        return $this->receivedMessages()->whereNull('read_at');
    }

    /**
     * Determine if the user has followed the given followee.
     *
     * @param  \App\Models\User  $followee
     * @return bool
     */
    public function hasFollowed(User $followee)
    {
        return $this->followees()->where('followee_id', $followee->id)->exists();
    }

    /**
     * Determine if the user has liked the given post.
     *
     * @param  \App\Models\Post  $post
     * @return bool
     */
    public function hasLiked(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->exists();
    }

    /**
     * Find a user for the given api token.
     *
     * @param  string  $token
     * @return \App\Models\User
     */
    public function findForApiToken($token)
    {
        return $this->with('followees', 'likes', 'unreadMessages.files')
                    ->withCount('posts', 'followers', 'unreadNotifications')
                    ->where('api_token', $token)
                    ->first();
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'user.'.$this->attributes['id'];
    }
}
