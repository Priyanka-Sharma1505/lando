<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class postSetNewPasswordController extends Controller
{
    public function index(){

        $credentials = request()->validate([
            'email' => 'email|required',
            'password' => 'required|string|confirmed',
            'token' => 'required|string',
        ]);

        return (new UserService())->setNewPassword($credentials);
    }
}
