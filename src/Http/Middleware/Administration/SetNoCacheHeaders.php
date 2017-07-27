<?php namespace Tranquility\Http\Middleware\Administration;

use Closure;

class SetNoCacheHeaders {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)	{
		$response = $next($request);
		
		// Add header to the response
		return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	}
}
