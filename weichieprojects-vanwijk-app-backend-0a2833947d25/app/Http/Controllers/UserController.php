<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function user($id){
        return User::with('locations', 'addressRequests', 'addressRequests.request')->findOrFail($id);
    }

    public function update(){

        $user = User::findOrfail(request()->input('data.id'));

        $user->first_name = request()->input('data.first_name');
        $user->last_name = request()->input('data.last_name');
        $user->function = request()->input('data.function');
        $user->save();

        return $user;
    }
}
