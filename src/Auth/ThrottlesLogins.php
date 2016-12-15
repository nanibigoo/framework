<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-10-21 14:43
 */
namespace Notadd\Foundation\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Class ThrottlesLogins.
 */
trait ThrottlesLogins
{
    /**
     * TODO: Method hasTooManyLoginAttempts Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts($this->throttleKey($request), 5, 1);
    }

    /**
     * TODO: Method incrementLoginAttempts Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit($this->throttleKey($request));
    }

    /**
     * TODO: Method sendLockoutResponse Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));
        $message = Lang::get('auth.throttle', ['seconds' => $seconds]);

        return redirect()->back()->withInput($request->only($this->username(),
            'remember'))->withErrors([$this->username() => $message]);
    }

    /**
     * TODO: Method clearLoginAttempts Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * TODO: Method fireLockoutEvent Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * TODO: Method throttleKey Description
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

    /**
     * TODO: Method limiter Description
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }
}