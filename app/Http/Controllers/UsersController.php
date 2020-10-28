<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

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
    public function edit(User $user)
    {   
        // $user為要修改的會員資料
        // dd($user);
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->all();

        if($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);

            if($result){
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '個人資料更新成功！');
    }
}
