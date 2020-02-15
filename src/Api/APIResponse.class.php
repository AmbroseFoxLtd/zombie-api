<?php

namespace Zombie\Api;

class APIResponse {

    const GENERIC_OK_MESSAGE = 'OK';
    const GENERIC_BAD_REQUEST_MESSAGE = 'The request failed due to malformed request syntax';
    const GENERIC_UNAUTHORIZED_MESSAGE = 'Invaild credentials';
    const GENERIC_FORBIDDEN_MESSAGE = 'You are not authorized to access this resource';
    const GENERIC_NOT_FOUND_MESSAGE = 'Resource could not be found';
    const GENERIC_SERVER_ERROR_MESSAGE = 'An error occured and the request could not be completed';

    const OK_HTTP_CODE = 200;
    const BAD_REQUEST_HTTP_CODE = 400;
    const UNAUTHORIZED_HTTP_CODE = 401;
    const FORBIDDEN_HTTP_CODE = 403;
    const NOT_FOUND_HTTP_CODE = 404;
    const SERVER_ERROR_HTTP_CODE = 500;

    private $headers = [];
    private $http_code;
    private $specific_message = '';
    private $body = [];

    public function buildResponse(array $headers, int $http_code, array $body, string $specific_error_message): void
    {
        $this->headers = $headers;
        $this->http_code = $http_code;
        $this->body = $body;
        $this->specific_message = $specific_error_message;
        return;
    }

    public function outputResponse(bool $json_encode = true): void
    {
        if ($this->headers) {
            foreach ($this->headers as $header => $value) {
                header("{$header}: {$value}");            
            }
        }
        if ($this->http_code) {
            http_response_code($this->http_code);
        } else {
            $this->http_code = 200;
        }
        switch ($this->http_code){
            case 200:
                $message = "[".self::GENERIC_OK_MESSAGE."]{$this->specific_message}";
                break;
            case 400:
                $message = "[".self::GENERIC_BAD_REQUEST_MESSAGE."]{$this->specific_message}";
                break;
            case 401:
                $message = "[".self::GENERIC_UNAUTHORIZED_MESSAGE."]{$this->specific_message}";
                break;
            case 403:
                $message = "[".self::GENERIC_FORBIDDEN_MESSAGE."]{$this->specific_message}";
                break;
            case 404:
                $message = "[".self::GENERIC_NOT_FOUND_MESSAGE."]{$this->specific_message}";
                break;
            case 500:
            default:
                $message = "[".self::GENERIC_SERVER_ERROR_MESSAGE."]{$this->specific_message}";
                break;
        }
        $this->body['message'] = $message;
        echo json_encode($this->body);
        return;
    }

}

?>