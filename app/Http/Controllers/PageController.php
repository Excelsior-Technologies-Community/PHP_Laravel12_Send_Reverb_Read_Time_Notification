<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function admin()
    {
        $users = User::all();

        // In demo environment, authenticate admin or first user for broadcasting presence
        if (!Auth::check()) {
            $adminUser = User::where('email', 'like', '%admin%')->first() ?: User::first();
            if ($adminUser) {
                Auth::login($adminUser);
            }
        }

        return view('admin', compact('users'));
    }

    public function user($id)
    {
        $user = User::findOrFail($id);
        // Authenticate session as this user so Reverb presence and user channels work seamlessly
        Auth::login($user);

        return view('user', compact('user'));
    }
}