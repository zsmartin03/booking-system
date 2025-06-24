<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'hu'];
        $locale = null;

        // Check if locale is provided in the request
        if ($request->has('locale') && in_array($request->get('locale'), $supportedLocales)) {
            $locale = $request->get('locale');
            session(['locale' => $locale]);
        }
        // Check session
        elseif (session()->has('locale') && in_array(session('locale'), $supportedLocales)) {
            $locale = session('locale');
        }
        // Fallback to default
        else {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
