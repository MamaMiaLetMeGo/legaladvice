<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_featured',
        'color',
        'icon',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class)
                    ->withTimestamps()
                    ->orderBy('published_date', 'desc');
    }

    /**
     * Scope to include post count.
     */
    public function scopeWithPostCount(Builder $query)
    {
        return $query->withCount('posts');
    }

    /**
     * Scope to filter categories that have posts.
     */
    public function scopeHasPosts(Builder $query)
    {
        return $query->has('posts');
    }

    /**
     * Scope to get featured categories.
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the URL for the category.
     */
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }

    /**
     * Get the post count for the category.
     */
    public function getPostCountAttribute(): int
    {
        return $this->posts()->published()->count();
    }

    /**
     * Get featured posts for the category.
     */
    public function getFeaturedPostsAttribute()
    {
        return $this->posts()
            ->published()
            ->latest('published_date')
            ->take(3)
            ->get();
    }

    /**
     * Get the meta title for the category.
     */
    public function getMetaTitleAttribute($value): string
    {
        return $value ?? $this->name;
    }

    /**
     * Get the meta description for the category.
     */
    public function getMetaDescriptionAttribute($value): string
    {
        return $value ?? Str::limit($this->description, 160);
    }

    /**
     * Get the color for the category.
     */
    public function getColorAttribute($value): string
    {
        return $value ?? '#3B82F6'; // default blue
    }

    /**
     * Get the published post count for the category.
     */
    public function getPublishedPostCountAttribute(): int
    {
        return $this->posts()->published()->count();
    }

    /**
     * Check if category has any published posts.
     */
    public function hasPublishedPosts(): bool
    {
        return $this->published_post_count > 0;
    }

    /**
     * Get related categories based on posts.
     */
    public function getRelatedCategories($limit = 5)
    {
        return static::whereHas('posts', function ($query) {
            $query->whereIn('posts.id', $this->posts()->pluck('posts.id'));
        })
        ->where('id', '!=', $this->id)
        ->withCount(['posts' => function ($query) {
            $query->published();
        }])
        ->orderByDesc('posts_count')
        ->limit($limit)
        ->get();
    }
}