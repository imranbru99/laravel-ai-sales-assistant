<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCaptureForm extends Model
{
    use HasFactory, BelongsToTenant, HasUniversalSlug;

    protected $guarded = ['id'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
            'views_count' => 'integer',
            'submissions_count' => 'integer',
        ];
    }
}
