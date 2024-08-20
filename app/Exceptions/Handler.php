<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
public function report(Exception|\Throwable $exception): void
{
    $this->storeExceptionInDatabase($exception);
     parent::report($exception);
}




protected function storeExceptionInDatabase(Exception $exception): void
{
    saveException(
        class_basename($exception),
        $exception->getMessage(),
        $exception instanceof HttpException ? $exception->getStatusCode() : 500,
        1,
        $exception->getTraceAsString(),
        json_encode([
            'request_data' => request()->all()??null,
            'exception_trace' => $exception->getTraceAsString(),
        ]),
        auth()->id()??null ,
        request()->fullUrl()??null,
        json_encode(request()->all())??null,
        json_encode(request()->headers->all()??null),
        request()->header('User-Agent')??null
    );
}

}
