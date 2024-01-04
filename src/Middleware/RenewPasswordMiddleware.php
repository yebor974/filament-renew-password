<?php

namespace Yebor974\Filament\RenewPassword\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class RenewPasswordMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        if ($request->user() && $request->user()->needRenewPassword()) {
            $panel ??= Filament::getCurrentPanel()->getId();

            return Redirect::guest(URL::route("filament.{$panel}.auth.password.renew"));
        }

        return $next($request);
    }
}