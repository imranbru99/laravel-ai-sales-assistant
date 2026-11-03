<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory, BelongsToTenant, HasUniversalSlug;

    protected $guarded = ['id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
