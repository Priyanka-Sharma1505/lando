<?php

namespace App\Services;

use App\Enums\Roles;
use App\Models\Odoo\Partner;
use App\Models\User;

class CompanyService
{

    function createAccount()
    {
        $user = new User();

        $user->parent_user_id = request()->input('company_id');
        $user->first_name = request()->input('first_name');
        $user->last_name = request()->input('last_name');
        $user->function = request()->input('function');
        $user->active = request()->input('active');
        $user->email = request()->input('email');
        $user->date_of_birth = request()->input('date_of_birth');
        $user->company_name = request()->input('company_name');
        $user->phone = request()->input('phone');
        $user->password = request()->input('password');

        // set permissions
        if( request()->input('all_permissions') ){
            $user->syncPermissions( request()->input('all_permissions') );
        }

        // add role
        $user->assignRole(Roles::PORTAL_ACCOUNT);

        $user->save();

        // Send password reset mail
        (new UserService())->resetPassword([
            'email' => $user->email,
        ]);

        return $user;
    }

    public function updateAccount()
    {
        /** @var User $user */
        $user = User::findOrfail(request()->input('id'));

        $user->first_name = request()->input('first_name');
        $user->last_name = request()->input('last_name');
        $user->function = request()->input('function');
        $user->active = request()->input('active');
        $user->email = request()->input('email');
        $user->date_of_birth = request()->input('date_of_birth');

        // Update permission
        if( request()->input('all_permissions') ){
            $user->syncPermissions( request()->input('all_permissions') );
        }

        $user->save();

        return $user;
    }

    function addClient()
    {

    }

    public function find($id){
        return (new Partner)->find($id);
    }

}
