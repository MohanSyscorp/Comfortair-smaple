<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'caption',
        'category',
        'status',
    ];

    /**
     * Pre-approved categories according to Functional Specification.
     */
    public const CATEGORIES = [
        'Installation photos',
        'Completed projects',
        'Service work',
        'Product images',
    ];

    /**
     * Scope for published items.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope by category.
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get accessible image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
