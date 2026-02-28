<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => $this->getActionName($request),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'payload'    => $this->getCleanPayload($request),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    protected function getActionName(Request $request)
    {
        $route = $request->route();
        return $route ? $route->getName() ?? $request->path() : $request->path();
    }

    protected function getCleanPayload(Request $request)
    {
        $payload = $request->except(['password', 'password_confirmation', '_token', '_method']);
        return count($payload) > 0 ? $payload : null;
    }
}
