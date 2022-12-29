<?php

namespace App\Services;

use App\Models\Odoo\Attributes\Interior;
use App\Models\Odoo\Pricelist;
use App\Models\Odoo\PricelistItem;
use App\Models\Odoo\Translation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\Fluent\Concerns\Has;
use Mockery\Generator\StringManipulation\Pass\Pass;

class UserService
{

    public function resetPassword($credentials)
    {
        Password::sendResetLink($credentials);

        return response()->json(['message' => 'Password reset link sent!'], 200);
    }

    public function setNewPassword($credentials)
    {
        $status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if( $status == Password::INVALID_TOKEN){
            return response()->json(['message' => 'Password reset failed!'], 500);
        }

        return response()->json(['message' => 'Password reset complete!'], 200);
    }

}
