<?php

namespace App\Services;

use App\Models\Odoo\Location;
use App\Models\Odoo\Stock;

class StockService {

    /**
     * Retrieve all stock locations
     */
    function locations(){ /*$location_id=null*/
        $location = (new Location());
        return $location->all();
    }

    /**
     *
     */
    function fromLocation($location_id){

        $stock = (new Stock());
        return $stock->fromLocation($location_id);

    }

}
