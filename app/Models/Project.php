<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property \App\Enums\Project\Status $status
 * @property \Illuminate\Support\Carbon $start_date
 * @property string|null $url
 * @property string|null $logo_id
 * @property \App\Models\Media|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Project extends Model
{
    use HandlesTranslatableAttributes;

    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'url',
        'logo_id',
        'creator_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'status' => \App\Enums\Project\Status::class,
    ];

    /**
     * @var string[]
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany<Partner, $this>
     */
    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class);
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isReferenced(): bool
    {
        return $this->partners()->exists();
    }
}
