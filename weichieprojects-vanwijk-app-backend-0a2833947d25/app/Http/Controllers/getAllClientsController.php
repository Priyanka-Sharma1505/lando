<?php

namespace App\Http\Controllers;

use App\Enums\OrganisationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class getAllClientsController extends Controller
{
    public function index(){

        $clients = DB::table('users')->select('id')->where('odoo_organisation_type_id', OrganisationType::FUNERAL_DIRECTOR)->pluck('id')->values();

        return User::whereIn('id', $clients)->get();
    }
}
