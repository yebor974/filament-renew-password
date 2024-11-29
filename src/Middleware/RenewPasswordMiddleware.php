<?php

namespace Yebor974\Filament\RenewPassword\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;

class RenewPasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @throws \Exception
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        if ($request->routeIs(Filament::getCurrentPanel()->generateRouteName('auth.logout'))) {
            return $next($request);
        }

        /** @var RenewPasswordContract|null $user */
        $user = $request->user();

        if (
            $user
            && in_array(RenewPasswordContract::class, class_implements($user))
            && $user->needRenewPassword()
        ) {
            $panelId = Filament::getCurrentPanel()->getId();

            return Redirect::guest(URL::route("filament.{$panelId}.auth.password.renew"));
        }

        return $next($request);
    }
}
