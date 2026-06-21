<?php

namespace App\Models;

use App\Concerns\HandlesTranslatableAttributes;
use App\Enums\MonitoredSite\Status;
use App\Jobs\CheckSiteIntegrityJob;
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
     * @var array<string, mixed>
     */
    protected $attributes = [
        'integrity_status' => 'pending',
    ];

    protected static function booted(): void
    {
        static::created(function (MonitoredSite $site) {
            CheckSiteIntegrityJob::dispatch($site);
        });
    }

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

    public function isReferenced(): bool
    {
        return false;
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
        $content = $this->fetchCurrentContent();
        $this->recalibrateBaseline($content);
    }

    public function recalibrateBaseline(string $content): void
    {
        $normalized = $this->normalizeContent($content);

        $this->expected_md5_hash = md5($normalized);
        $this->expected_links_count = (int) preg_match_all('/<a[\s>]/i', $content);
        $this->expected_scripts_count = (int) preg_match_all('/<script[\s>]/i', $content);

        $this->integrity_status = 'clean';
        $this->last_md5_hash = $this->expected_md5_hash;
        $this->last_integrity_checked_at = now();
        $this->last_error = null;

        $this->save();
    }

    /**
     * @throws \Exception
     */
    public function fetchCurrentContent(): string
    {
        $response = Http::withOptions([
            'stream' => true,
            'timeout' => 15,
            'connect_timeout' => 5,
            'verify' => true,
        ])->withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ])->get($this->url);

        if (! $response->successful()) {
            throw new \Exception(__('monitoring.integrity.log_error', ['error' => 'HTTP '.$response->status()]));
        }

        /** @var StreamInterface $body */
        $body = $response->toPsrResponse()->getBody();
        $content = '';

        while (! $body->eof() && strlen($content) < 2097152) {
            $content .= $body->read(1024);
        }
        $body->close();

        return $content;
    }

    public function normalizeContent(string $html): string
    {
        $assets = [];

        // 1. Extract all script sources, accounting for variations in whitespace and quotes
        if (preg_match_all('/<script[^>]+src\s*=\s*["\']?([^"\'\s>]+)["\']?/i', $html, $matches)) {
            $assets = array_merge($assets, $matches[1]);
        }

        // 2. Extract all link destinations (stylesheets, preloads, icons)
        if (preg_match_all('/<link[^>]+href\s*=\s*["\']?([^"\'\s>]+)["\']?/i', $html, $matches)) {
            $assets = array_merge($assets, $matches[1]);
        }

        // 3. Normalize URLs by stripping cache-busting parameters (anything after '?')
        $assets = array_map(function ($url) {
            return strtok($url, '?');
        }, $assets);

        // 4. Remove empty paths, discard duplicates, and sort alphabetically for consistency
        $assets = array_filter(array_unique($assets));
        sort($assets);

        // 5. Return a clean, carriage-return delimited list of files for hashing
        return implode("\n", $assets);
    }
}
