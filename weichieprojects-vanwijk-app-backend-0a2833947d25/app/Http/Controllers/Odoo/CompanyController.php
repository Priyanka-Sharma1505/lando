<?php

namespace App\Http\Controllers\Odoo;

use App\Enums\RequestType;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Odoo\Partner;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Return all Companies from Odoo
     * that are active, is a company
     * and doens't have a parent
     *
     * @return \Illuminate\Support\Collection|void
     */
    public function companies()
    {
        // Getting partners (companies) from cache
        $partners = (new Partner)->get();

        return $partners->where('active', true)
            ->where('company_type', 'company')
            ->where('parent_id', false);
    }

    /**
     *
     * @return \Illuminate\Support\Collection|void
     */
    public function addresses()
    {
        // Getting partners (companies) from cache
        $partners = (new Partner)->get(request()->has('fresh'));

        return $partners->where('active', true)
            ->whereIn('type', ['delivery'])
            ->sortBy('name');
    }

    /**
     *
     * @return \Illuminate\Support\Collection|void
     */
    public function clients()
    {
        // Getting partners (companies) from cache
        $partners = (new Partner)->get(request()->has('fresh'));

        return $partners->where('active', true)
            ->where('company_type', 'company')
            ->sortBy('name');
    }

    /**
     * Get Odoo Company data by ID
     * @param $id
     * @return mixed
     */
    public function company($id){

        $company = (new Partner)->getById($id)->collect()->first();
        $user = User::where('odoo_user_id', $id)->firstOrFail();

        // Retrieve "linked" delivery addresses
        $delivery_addresses = $user->deliveryAddresses->pluck('odoo_delivery_address_id')->toArray();

        $company_addresses = [];

        if( isset($company['children']) ) {
            foreach ($company['children'] as $address) {
                if (in_array($address['id'], $delivery_addresses)) {
                    $company_addresses[] = $address;
                }

                if ($address['type'] == 'invoice') {
                    $company['invoice'] = $address;
                }
            }

            $company['children'] = $company_addresses;
        }


        $company['accounts'] = User::where('parent_user_id', $user->id)->get()->toArray();

        $clients = Client::with('request')->where('user_id', $user->id)->get();
        if($clients->isNotEmpty()){

            $clients = $clients->filter(function ($client){
                if( isset($client['request']) ){
                    if( $client['request']['status'] != RequestType::OPEN && $client['request']['status'] != RequestType::CLOSED ){
                        return false;
                    }
                }
                return true;
            });

            $company['clients'] = $clients->toArray();
        }

        return $company;
    }

    /**
     * Update Odoo resource
     * @param $id
     * @param $data
     */
    public function update(Request $request){
        $data = collect( $request->input('data') )->only( Partner::$fillable )->toArray();
        return $request->input('data');

        // We're not going to update to ODOO yet
        //return (new Partner)->update($request->input('id'), $data);
    }

}
