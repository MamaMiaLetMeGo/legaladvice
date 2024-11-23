<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author_id',
        'breadcrumb',
        'body_content',
        'featured_image',
        'slug',
        'video',
        'status',
        'published_date'
    ];

    protected $casts = [
        'published_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::saving(function ($post) {
            if ($post->isDirty('status') && $post->status === 'published' && !$post->published_date) {
                $post->published_date = Carbon::now();
            }
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_date')
                    ->where('published_date', '<=', now());
    }

    public function scopeDraft(Builder $query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived(Builder $query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeWithCategories(Builder $query)
    {
        return $query->with('categories');
    }

    public function scopeInCategory(Builder $query, $category)
    {
        return $query->whereHas('categories', function ($query) use ($category) {
            $query->where('slug', $category);
        });
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('body_content', 'ILIKE', "%{$search}%");
        });
    }

    public function scopeByAuthor(Builder $query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeRecentlyPublished(Builder $query, $limit = 5)
    {
        return $query->published()
                    ->orderBy('published_date', 'desc')
                    ->limit($limit);
    }

    public function getExcerptAttribute($length = 150)
    {
        return Str::limit(strip_tags($this->body_content), $length);
    }

    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->body_content));
        $minutes = ceil($words / 200);
        return $minutes;
    }

    public function getUrlAttribute(): string
    {
        return route('posts.show', ['post' => $this->slug]);
    }

    public function getFeaturedImageUrlAttribute(): string
    {
    if (!$this->featured_image) {
        return '/images/default-post-image.jpg';
    }

    try {
        if (Storage::disk('public')->exists($this->featured_image)) {
            return asset('storage/' . $this->featured_image);
        } else {
            \Log::error('Featured image not found: ' . $this->featured_image);
            return '/images/default-post-image.jpg';
        }
    } catch (\Exception $e) {
        \Log::error('Error getting featured image: ' . $e->getMessage());
        return '/images/default-post-image.jpg';
    }
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_date && 
               $this->published_date->isPast();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_date' => Carbon::now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'published_date' => null,
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'archived'
        ]);
    }

    public function getCategoryListAttribute()
    {
        return $this->categories->pluck('name')->join(', ');
    }
}