<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'label',
        'icon',
        'description',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Articles in this category
     */
    public function articles(): HasMany
    {
        return $this->hasMany(FaqArticle::class, 'category_id');
    }

    /**
     * Active articles only
     */
    public function activeArticles(): HasMany
    {
        return $this->articles()->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get article count
     */
    public function getArticleCountAttribute(): int
    {
        return $this->activeArticles()->count();
    }
}
