<?php namespace App\Http\Controllers;


use Auth;
use GameController;
use App\Game;
class HomeController extends Controller {

	/*
	
        Load the non-game views
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
        return redirect('facebook/login');
    }
    
    public function userHome()
    {
        //dd (Auth::user()->id);
        
        return view('user.home', $this->getGames());
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
    
    public function getGames()
    {
        $games = Game::where('players', 'LIKE', '%:' . Auth::user()->id . ':%')->orderBy('updated_at', 'desc')->get();
        $gamesWithInvites = Game::where('user_invites', 'LIKE', '%:' . Auth::user()->id . ':%')->get();

        return array(
            'games'=>$games, 
            'gamesWithInvites' => $gamesWithInvites
        );
    }
    
    /**
        show about page
    **/
    public function showAbout()
    {
        return view("guest.about");
    }
    

}
