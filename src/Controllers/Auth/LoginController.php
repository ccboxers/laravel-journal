<?php

namespace Layman\LaravelJournal\Controllers\Auth;

use Layman\LaravelJournal\Auth\AuthenticatesUsers;
use Layman\LaravelJournal\Controllers\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'journal/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('journal.guest')->except('logout');
        $this->middleware('journal.auth')->only('logout');
    }
}
