<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class postUpdateAccountController extends Controller
{
    public function index(){

        request()->validate(User::$updateRules);

        return (new CompanyService())->updateAccount();

    }
}
