<?php

namespace LaravelSecurityAudit\MailPreview\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMailPreview
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $gate = config('mail-preview.gate');

        if (is_string($gate) && $gate !== '' && Gate::denies($gate)) {
            abort(403);
        }

        return $next($request);
    }
}
