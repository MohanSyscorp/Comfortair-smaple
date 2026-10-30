<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'whatsapp',
        'email',
        'address',
        'working_hours',
        'google_maps_link',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
    ];

    /**
     * Fetch the contact settings row if one has been created (no static default data).
     */
    public static function getSettings(): ?self
    {
        return static::first();
    }
}
