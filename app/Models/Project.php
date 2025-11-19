<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use HandlesTranslatableAttributes;
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'url',
        'logo_id',
    ];

    protected $casts = [
        'start_date' => 'date',
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
        return $this->belongsToMany(Partner::class)->withPivot('id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
