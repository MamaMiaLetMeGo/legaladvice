<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class)
                    ->withTimestamps()
                    ->orderBy('published_date', 'desc');
    }

    public function scopeWithPostCount(Builder $query)
    {
        return $query->withCount('posts');
    }

    public function scopeHasPosts(Builder $query)
    {
        return $query->has('posts');
    }

    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getUrlAttribute()
    {
        return url("/category/{$this->slug}");
    }
}