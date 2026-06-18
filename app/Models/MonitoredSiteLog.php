<?php

namespace App\Models;

use Database\Factories\MonitoredSiteLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $monitored_site_id
 * @property string $type
 * @property string $status
 * @property int|null $status_code
 * @property int|null $latency
 * @property array<mixed>|null $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MonitoredSiteLog extends Model
{
    /** @use HasFactory<MonitoredSiteLogFactory> */
    use HasFactory;

    use HasUlids;
    use Prunable;

    protected $fillable = [
        'monitored_site_id',
        'type',
        'status',
        'status_code',
        'latency',
        'details',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'latency' => 'integer',
        'details' => 'array',
    ];

    /**
     * @return BelongsTo<MonitoredSite, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(MonitoredSite::class, 'monitored_site_id');
    }

    /**
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(30));
    }
}
