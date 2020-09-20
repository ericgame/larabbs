<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    //個人中心
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    //編輯資料
    public function edit()
    {

    }

    public function update()
    {

    }
}
