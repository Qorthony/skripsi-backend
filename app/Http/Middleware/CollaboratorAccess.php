<?php

namespace App\Http\Middleware;

use App\Models\Collaborator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CollaboratorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route()->parameter('event');
        if ($request->access_code && $event) {
            
            $validAccessCode = Collaborator::where('kode_akses', $request->access_code)
                ->where('event_id', $event->id)
                ->exists();

            if ($validAccessCode) {
                return $next($request);
            }

        }
        return abort(403, 'Access code invalid');
    }
}
