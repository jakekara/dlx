<?php namespace App\Http\Middleware;
/* Make sure the user is logged in when loading pages */


use Closure;
use Auth;
use Session;

use App\Library\FacebookHelper;

/**
    Make sure we have functioning user records
    for pages that need them.
**/

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
        $fbHelper = new FacebookHelper;
        $session = $fbHelper->getSession();
        if ($session)
        {
            return $next($request);
        }
        else
        {
            return redirect("facebook/login");
        }
    }
}