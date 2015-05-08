<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use App\Library\FacebookHelper;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

        // if there is a facebook signed_request, verify it
        // but don't look for normal CSRF token 
        if ($request->has('signed_request'))
        {
            $facebookHelper = new FacebookHelper;
            if ($facebookHelper->verifySignedRequest($request->get('signed_request')))
            {
                // signed request is valid, proceed
                return $next($request);
            }
            else
            {
                return redirect("/apologize");
            }
        
        }
        
        
        // normal
        return parent::handle($request, $next);
	}

}
