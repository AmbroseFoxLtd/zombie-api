<?php

namespace App\Controllers\Api;

use Zombie\Controller;
use Exception;

class Api extends Controller {

    /**
     * The default headers sent to the client
     * 
     * @var array
     */
    public $default_headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'accept, maxdataserviceversion, origin, x-requested-with, dataserviceversion,content-type',
        'Access-Control-Max-Age' => '86400',
        'Content-Type' => 'application/json',
    ];

    /**
     * The api environment requested. Used to decide how to respond to a request
     * 
     * @var string
     */
    public $env;

    /**
     * URI of the request
     * 
     * @var string
     */
    public $request_uri;

    /**
     * HTTP method used in the request
     * 
     * @var string
     */
    public $request_method;

    /**
     * Endpoint of the requested resource
     * 
     * @var string
     */
    public $request_endpoint;

    /**
     * Parameter to use in conjuction with the endpoint
     * 
     * @var string
     */
    public $request_endpoint_parameter;
    
    /**
     * Method run when the framework matches a valid api route
     * 
     * @param string $parameter The endpoint parameter
     * 
     * @return void
     */
    public function request($parameter = null): void
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_endpoint_parameter = $parameter;
        $this->requestEndpoint();
        $this->handleRequest();
        return;
    }

    /**
     * Determines the environment and endpoint from the request URI
     * 
     * @return bool Success or fail
     */
    public function requestEndpoint(): bool
    {
        list($root, $api, $env, $endpoint) = explode('/', $this->request_uri, 5);
        if ($api === 'api') {
            $this->env = $env;
            $this->request_endpoint = $endpoint;
            return true;
        } else {
            return false;
        }
    }

    public function handleRequest()
    {
        try{
            if (!$this->request_endpoint) {
                $this->sendError(400, 'No endpoint provided sent');
                return;
            }
            if ($this->env === 'driver') {
                $driver = new Driver;
                if ($this->request_method === 'GET'){
                    $data = $driver->get(
                        $this->request_endpoint,
                        ['uid' => $this->request_endpoint_parameter]
                    );
                    $this->sendResponse([], 200, ['data' => $data], '');
                } else {
                    // Send generic response
                    $this->sendResponse();
                }
            } else {
                $this->sendError(400, "Environment '{$this->env}' does not exist");
            }
        } catch(Exception $e) {
            $this->sendError($e->getCode(), $e->getMessage());
        }
    }

    public function sendError(int $http_code, string $error_message): void
    {
        $this->sendResponse([], $http_code, [], $error_message);
        return;
    }

    public function sendResponse(array $additional_headers = [], int $http_code = 200, array $body = [], string $message = ''): void
    {
        $headers = array_merge($this->default_headers, $additional_headers);
        $response = new APIResponse;
        $response->buildResponse($headers, $http_code, $body, $message);
        $response->outputResponse(true);
        return;
    }

}

?>