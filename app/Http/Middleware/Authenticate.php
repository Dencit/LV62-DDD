<?php

namespace App\Http\Middleware;

use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

class Authenticate extends BaseAuth
{

    public function handle($request, \Closure $next, ...$scopes)
    {
        //检查授权
        $auth = $this->apiAuth($request);
        $scopeId = $auth->scope_id;
        $request->auth = $auth;

        if( !in_array($scopeId,$scopes) ){
            Exception::app(BaseError::code('AUTH_SCOPE_FAIL'),BaseError::msg('AUTH_SCOPE_FAIL'));
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
