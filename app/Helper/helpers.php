<?php



if (!function_exists('saveException')) {
    /**
     * Save an exception log entry using the ErrorLogService.
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
     */
    function saveException(
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
    )
    {
        return \App\Services\Error\ErrorLogService::saveException(
            $exception,
            $message,
            $status_code,
            $priority,
            $stack_trace,
            $extra_data,
            $user_id_logged,
            $url,
            $requests,
            $headers,
            $user_agent
        );
    }
}

