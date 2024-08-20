<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * @property integer $id
 * @property integer|null $status_code
 * @property integer|null $priority
 * @property string|null $url
 * @property integer $seen_status
 * @property string|null $exception
 * @property string|null $message
 * @property integer|null $user_id_logged
 * @property string|null $stack_trace
 * @property string|null $requests
 * @property string|null $headers
 * @property string|null $extra_data
 * @property string|null $user_agent
 * @property string|null $created_at_jalali
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 */
class ErrorLog extends Model
{
    use HasFactory, SoftDeletes;
    const status_unseen = 0;
    const status_seen = 1;

    protected $fillable = [
        'status_code',
        'priority',
        'url',
        'seen_status',
        'exception',
        'message',
        'user_id_logged',
        'stack_trace',
        'requests',
        'headers',
        'extra_data',
        'user_agent',
        'created_at_jalali',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'priority' => 'integer',
        'seen_status' => 'integer',
        'user_id_logged' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($log) {
            $log->url = $log->url ?? request()?->fullUrl() ?? null;
            $log->headers = $log->headers ?? json_encode(request()->headers->all()) ?? null;
            $log->user_agent = $log->user_agent ?? request()?->userAgent() ?? null;
            $log->user_id_logged = $log->user_id_logged ?? auth()?->id() ?? null;
        });
    }

    public function setCreatedAtAttribute($value): void
    {
        $this->attributes['created_at'] = $value;

        if (isset($value)) {
            $this->attributes['created_at_jalali'] = Jalalian::fromDateTime($value)->format('Y/m/d');
        } else {
            $this->attributes['created_at_jalali'] = null;
        }
    }

}
