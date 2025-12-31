<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function admin()
    {
        $users = User::all();
        return view('admin', compact('users'));
    }

    public function user($id)
    {
        $user = User::findOrFail($id);
        return view('user', compact('user'));
    }
}