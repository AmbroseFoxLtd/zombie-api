<?php

namespace App\Controllers\Api;

use App\Models\Collection;

class Collections {
    
    public static function driver_collections()
    {
        $driver_id = 1;
        $collections = Collection
            ::find(1)
            ->collections_contract_lines()
            ->get()
            ->toArray();
        return $collections;
    }

}

?>