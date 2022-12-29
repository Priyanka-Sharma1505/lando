<?php

namespace App\Http\Controllers;

use App\Enums\OrganisationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepotController extends Controller
{
    /**
     * Retrieve all depot users to show in overview
     * Edit pagination items to include locations ids as well
     */
    public function all(){

        $depot_users = DB::table('users')->where('odoo_organisation_type_id', OrganisationType::DEPOT)->paginate(15);
        $locations =

        $depot_users->getCollection()->transform(function($depot_user){
            $depot_user->locations = User::find( $depot_user->id )->locations->pluck('odoo_location_id')->toArray();
            return $depot_user;
        });

        return $depot_users;

    }
}
