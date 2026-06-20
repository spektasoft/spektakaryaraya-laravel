<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use App\Enums\MonitoredSite\Status;
use Database\Factories\MonitoredSiteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

/**
 * @property int $id
 * @property Project $project
 * @property string $name
 * @property string $url
 * @property Status $status
 * @property string $uptime_status
 * @property int|null $last_uptime_code
 * @property Carbon|null $last_uptime_checked_at
 * @property int|null $last_uptime_latency
 * @property string $integrity_status
 * @property Carbon|null $last_integrity_checked_at
 * @property string|null $expected_md5_hash
 * @property string|null $last_md5_hash
 * @property int|null $expected_links_count
 * @property int|null $last_links_count
 * @property int|null $expected_scripts_count
 * @property int|null $last_scripts_count
 * @property string|null $last_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'creator_id',
    'project_id',
    'name',
    'url',
    'status',
    'uptime_status',
    'last_uptime_code',
    'last_uptime_checked_at',
    'last_uptime_latency',
    'integrity_status',
    'last_integrity_checked_at',
    'expected_md5_hash',
    'last_md5_hash',
    'expected_links_count',
    'last_links_count',
    'expected_scripts_count',
    'last_scripts_count',
    'last_error',
])]
class MonitoredSite extends Model
{
    use HandlesTranslatableAttributes;

    /** @use HasFactory<MonitoredSiteFactory> */
    use HasFactory;

    use HasUlids;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => Status::class,
    ];

    /**
     * @var string[]
     */
    public $translatable = [
        'name',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * @return HasMany<MonitoredSiteLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(MonitoredSiteLog::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::Active);
    }

    /**
     * @throws \Exception
     */
    public function recalibrateFromUrl(): void
    {
        $response = Http::withOptions([
            'stream' => true,
            'timeout' => 15,
            'verify' => true,
        ])->get($this->url);

        if (! $response->successful()) {
            throw new \Exception(__('monitoring.resource.actions.notifications.request_failed', ['code' => $response->status()]));
        }

        /** @var StreamInterface $body */
        $body = $response->toPsrResponse()->getBody();
        $content = '';

        while (! $body->eof() && strlen($content) < 20480) {
            $content .= $body->read(1024);
        }

        $body->close();

        $this->recalibrateBaseline($content);
    }

    public function recalibrateBaseline(string $content): void
    {
        $this->expected_md5_hash = md5($content);
        $this->expected_links_count = (int) preg_match_all('/<a[\s>]/i', $content);
        $this->expected_scripts_count = (int) preg_match_all('/<script[\s>]/i', $content);
        $this->save();
    }
}
