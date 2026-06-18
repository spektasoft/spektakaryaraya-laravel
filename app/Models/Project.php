<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use App\Enums\Project\Status;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property Status $status
 * @property Carbon $start_date
 * @property string|null $url
 * @property string|null $logo_id
 * @property Media|null $logo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Project extends Model
{
    use HandlesTranslatableAttributes;

    /** @use HasFactory<ProjectFactory> */
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
        'status' => Status::class,
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
