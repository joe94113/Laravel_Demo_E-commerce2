<?php

namespace App\Exceptions;

use App\Models\LogError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
        $this->reportable(function (Throwable $e) { // 紀錄錯誤資訊
            $user = auth()->user();
            LogError::create([
                'user_id' => $user ? $user->id : 0,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'trace' => array_map(function($trace){
                    unset($trace['args']);
                    return $trace;
                }, $e->getTrace()),
                'method' => request()->getMethod(),
                'params' => request()->all(),
                'uri' => request()->getPathInfo(),
                'user_agent' => request()->userAgent(),
                'header' => request()->headers->all(),
            ]);
        });

        $this->renderable(function(Throwable $e){
            // return response()->view('error');
            return response($e);
        });
    }
    // 覆寫
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response('授權失敗', 401);
    }
}
