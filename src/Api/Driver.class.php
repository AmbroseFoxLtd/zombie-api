<?php

namespace App\Controllers\Api;

class Driver {
    
    public function get(string $endpoint, array $parameters): array
    {
        if ($endpoint === 'collections') {
            throw new Exception('', 403);
            if ($parameters['uid']) {
                return [];
            } else {
                $data = Collections::driver_collections();
                return $data ?? [];
            }
        } else {
            return [];
        }
    }

    public function post(string $endpoint, array $parameters)
    {
        
    }

}

?>