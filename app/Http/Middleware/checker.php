<?php namespace App\Http\Middleware;
/* Make sure the user is logged in when loading pages */


use Closure;
use Auth;


class checker {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        // TODO - Check for authentication or redirect
        
        if (!Auth::check()) //return $next($request)
        
        {
            // for now, pass through
            return $next($request);
        }

        
        return $next($request);

        //return $next($request);
        // otherwise, redirect user to login
        //return "Not logged in";
	}

}
