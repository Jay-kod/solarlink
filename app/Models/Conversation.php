<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'created_by'];

    /**
     * Get the user who created this conversation.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users/participants in this conversation.
     * Admins are excluded from this pivot list to remain silent observers.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the messages for this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the latest message for this conversation.
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
