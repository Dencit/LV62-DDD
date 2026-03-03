<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *应用程序的全局HTTP中间件堆栈。这些中间件在应用程序的每个请求期间都会运行。
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\Cors::class,
//        \App\Http\Middleware\TrustProxies::class,
//        \App\Http\Middleware\CheckForMaintenanceMode::class,
//        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
//        \App\Http\Middleware\TrimStrings::class,
//        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     * 应用程序的路由中间件。这些中间件可以分配给组或单独使用。
     * @var array
     */
    protected $routeMiddleware = [
        'auth'     => \App\Http\Middleware\Authenticate::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
//        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
//        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
//        'can' => \Illuminate\Auth\Middleware\Authorize::class,
//        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
//        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
//        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
//        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     * This forces non-global middleware to always be in the given order.
     * 中间件的优先级排序列表。这迫使非全局中间件始终处于给定的顺序。
     * @var array
     */
    protected $middlewarePriority = [
//        \App\Http\Middleware\Authenticate::class,
//        \Illuminate\Routing\Middleware\SubstituteBindings::class,
//        \Illuminate\Session\Middleware\StartSession::class,
//        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//        \Illuminate\Routing\Middleware\ThrottleRequests::class,
//        \Illuminate\Session\Middleware\AuthenticateSession::class,
//        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
