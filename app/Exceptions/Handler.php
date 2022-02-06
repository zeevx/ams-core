<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            return ResponseHelper::errorWithMessage($e->getMessage(), INTERNAL_SERVER_ERROR);
        });

        //Other Exceptions
        $this->renderable(function (\Exception $e, $request) {
            return ResponseHelper::errorWithMessage($e->getMessage(), INTERNAL_SERVER_ERROR);
        });

        //Forbidden Exception
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return ResponseHelper::errorWithMessage('Access Denied', FORBIDDEN);
        });
    }
}
