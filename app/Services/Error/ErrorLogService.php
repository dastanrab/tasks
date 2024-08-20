<?php

namespace App\Services\Error;

use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ErrorLogService
{
    public static function index(array $fields): Builder
    {
        $query = ErrorLog::query();

        if (!empty($fields['status_code'])) {
            $query->where('status_code', $fields['status_code']);
        }

        if (!empty($fields['priority'])) {
            $query->where('priority', $fields['priority']);
        }

        if (!empty($fields['url'])) {
            $query->where('url', 'like', '%' . $fields['url'] . '%');
        }

        if (!empty($fields['exception'])) {
            $query->where('exception', 'like', '%' . $fields['exception'] . '%');
        }

        if (!empty($fields['message'])) {
            $query->where('message', 'like', '%' . $fields['message'] . '%');
        }

        if (!empty($fields['user_id_logged'])) {
            $query->where('user_id_logged', $fields['user_id_logged']);
        }

        if (!empty($fields['created_at_jalali'])) {
            $query->where('created_at_jalali', 'like', '%' . $fields['created_at_jalali'] . '%');
        }

        return $query;
    }

    public static function store(array $fields): Model|Builder
    {
        return ErrorLog::query()->create($fields);
    }

    public static function update(ErrorLog $errorLog, array $fields): ErrorLog
    {
        $errorLog->update($fields);
        return $errorLog;
    }

    /**
     * Save an exception log entry to the database.
     *
     * @param string $exception
     * @param string $message
     * @param int|null $status_code
     * @param int|null $priority
     * @param string|null $stack_trace
     * @param string|null $extra_data
     * @param int|null $user_id_logged
     * @param string|null $url
     * @param string|null $requests
     * @param string|null $headers
     * @param string|null $user_agent
     * @return Builder|Model
     */
    public static function saveException(
        string $exception,
        string $message,
        int    $status_code = null,
        int    $priority = null,
        string $stack_trace = null,
        string $extra_data = null,
        int    $user_id_logged = null,
        string $url = null,
        string $requests = null,
        string $headers = null,
        string $user_agent = null
    ): Builder|Model
    {
        return ErrorLog::query()->create([
            'exception' => $exception,
            'message' => $message,
            'status_code' => $status_code,
            'priority' => $priority,
            'stack_trace' => $stack_trace,
            'extra_data' => $extra_data,
            'user_id_logged' => $user_id_logged ?? auth()?->id(),
            'url' => $url ?? request()?->fullUrl(),
            'requests' => $requests ?? json_encode(request()?->all()),
            'headers' => $headers ?? json_encode(request()?->headers->all()),
            'user_agent' => $user_agent ?? request()?->userAgent(),
        ]);
    }

}
