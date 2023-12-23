<?php

namespace App\Mailing\send_pulse\Api;

use app\Mailing\send_pulse\ExceptionSendPulse;

class ApiDataRequest{
    public function __call($methode, $args){
        if(method_exists($this, $methode)){
            $params = isset($args[0]) ? $args[0] : [];
            $methode_data = $this->{$methode}($params);
            list($field_exist, $field_missing) = $this->checkFields(
                $methode_data["pattern"], $params
            );
            $request_params = $this->formParams(
                $field_exist, $field_missing, $params
            );
            
            return [
                'method' => $methode_data["method"],
                "url_part" => $methode_data["url"],
                "data" =>  $request_params,
                "extra" => [
                    "headers" => [
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Expect:'
                    ],
                ],
            ];
        }else{
            throw new ExceptionSendPulse(ExceptionSendPulse::METHOD_IS_NOT_EXISTS);
        }
    }
}