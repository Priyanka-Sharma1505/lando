<?php

namespace App\Models\Odoo;

class Partner extends Base
{
    public $resource = 'res.partner';
    public $fields = [
        'id',
        'name',
        'parent_id',
        'child_ids',
        'display_name',
        'function',
        'type',
        'company_type',
        'date',
        'website',
        'active',
        'email',
        'city',
        'street',
        'street_name',
        'street_number',
        'zip',
        'phone',
        'country_id',
        'property_product_pricelist',
        //'x_studio_franco_levering_vanaf_1',
        'user_id',
        /*'x_studio_facturatie_voorkeuren',
        'x_studio_status_organisatie',
        'x_studio_brancheorganisatie',
        'x_studio_field_J7TlG', // Tarievenlijsten Ink. 2022
        'x_studio_field_mPJWX', // Tarievenlijsten Adv. 2022*/
        //'x_studio_field_ZxHzg' // Type organisatie
    ];
    public static $fillable = [
        'street',
        'street_name',
        'street_number',
        'city',
        'zip'
    ];

    public function __construct($data=null)
    {
        $this->data = $data;
    }

    public function collect(){
        $data = collect( $this->data );
        $data = $data->map(function ($item){
            $item['street_number'] = ($item['street_number']==false) ? '' : $item['street_number'];
            return $item;
        });
        return $data;
    }

    public function find($id){
        $company = (new Partner)->cache('company_id_'.$id, function() use($id){
            return (new Partner)->connect()->where('id','=',$id)->fields($this->fields)->get($this->resource);
        });
        return $company;
    }

    public function organisationType()
    {
        $organisation_types = (new OrganisationType())->get();

        $organisation_mapping = collect($this->data['x_studio_field_ZxHzg'])->map(function($organisation_type_id) use ($organisation_types){
            return $organisation_types->where('id', $organisation_type_id)->first();
        });

        return $organisation_mapping;
    }
}
