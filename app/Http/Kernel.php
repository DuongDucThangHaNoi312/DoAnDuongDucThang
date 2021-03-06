<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Như các bạn thấy trong file Kernel.php chứa rất nhiều biến liên quan đến middleware. 
    // Từ đó chúng ta có thể dễ dàng đoán được nhiệm vụ của file Http Kernel.php này, đó là định nghĩa danh sách các middleware mà tất cả các request gửi đến ứng dụng phải vượt qua được (pass – một trong những mục đích của middleware là dùng để xác thực) trước khi được xử lý logic.
    // Thực tế, không chỉ khai báo các middleware của ứng dụng, Http Kernel còn định nghĩa một mảng các bootstrappers cần phải chạy trước khi các request được xử lý bao gồm: cấu hình xử lý lỗi (error handling), cấu hình ghi log (logging), phát hiện môi trường của ứng dụng (detect the application environment) thông qua các biến env và thực hiện các tác vụ khác cần được hoàn thành trước khi request thực sự được xử lý.
    // Về cơ bản Http Kernel nhận vào một request và trả lại một response để tiếp tục vòng đời của một request.

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'          => \App\Http\Middleware\Authenticate::class,
        'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'      => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'       => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'     => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed'    => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'  => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'  => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role'      => \Laratrust\Middleware\LaratrustRole::class,
        'permission'=> \Laratrust\Middleware\LaratrustPermission::class,
        'ability'   => \Laratrust\Middleware\LaratrustAbility::class,
        'admin'     => \App\Http\Middleware\AdminAuthenticate::class,
        'customer'  => \App\Http\Middleware\CustomerAuthenticate::class,
        'admin.middleware'  => \App\Http\Middleware\AdminMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
