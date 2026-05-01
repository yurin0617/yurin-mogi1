<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.setup'); // resources/views/profile/setup.blade.php を表示
    }
}
