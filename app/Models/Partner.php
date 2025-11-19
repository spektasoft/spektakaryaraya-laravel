<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Partner extends Model
{
    use HandlesTranslatableAttributes;

    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'url',
        'logo_id',
    ];

    /**
     * @var string[]
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withPivot('id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function isReferenced(): bool
    {
        return $this->projects()->exists();
    }
}
