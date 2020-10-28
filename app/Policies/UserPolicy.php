<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $currentUser, User $user)
    {
        /*
            $currentUser->id 為目前"登入"的會員id
            $user->id 為要被修改資料的會員id (此資料由UsersController傳入)
        */
        // dd($currentUser->id, $user->id);
        return $currentUser->id === $user->id;
    }
}
