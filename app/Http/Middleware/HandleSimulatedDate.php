<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSimulatedDate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('simulated_date')) {
            $param = $request->query('simulated_date');
            if (in_array($param, ['reset', 'clear', 'off'], true)) {
                $request->session()->forget('spmb_simulated_date');
            } elseif ($param) {
                try {
                    $parsed = Carbon::parse($param);
                    $request->session()->put('spmb_simulated_date', $parsed->format('Y-m-d H:i:s'));
                } catch (\Throwable) {
                    // ignore invalid date string
                }
            }
        }

        return $next($request);
    }
}
