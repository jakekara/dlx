<?php namespace App\Http\Controllers;


use Auth;
use GameController;
class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}
    
    public function dashboard()
    {
        return view('home.dashboard');
    }
        
    public function guestHome()
    {
        return view('guest.home');
    }
    
    public function userHome()
    {
        return view('user.home', array(
            'games'=>Game::where('players', 'LIKE', '%:' . Auth::user()->id . ':')
        ));
    }
    
    public function goHome()
    {
        if (Auth::check())
        {
            return $this->userHome();
        }
        else
        {
            return $this->guestHome();
        }
    }

}
