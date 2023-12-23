<?php
/**
 *  __________________________________________ 
 * |                                          |
 * |   ╭━━━━┳━━━┳╮╱╱╭━━━┳━━━┳━━━┳╮╱╭┳━╮╭━╮    |
 * |   ┃╭╮╭╮┃╭━╮┃┃╱╱┃╭━╮┃╭━━┻╮╭╮┃┃╱┃┃┃╰╯┃┃    |
 * |   ╰╯┃┃╰┫┃╱┃┃┃╱╱┃╰━━┫╰━━╮┃┃┃┃┃╱┃┃╭╮╭╮┃    |
 * |   ╱╱┃┃╱┃┃╱┃┃┃╱╭╋━━╮┃╭━━╯┃┃┃┃┃╱┃┃┃┃┃┃┃    |
 * |   ╱╱┃┃╱┃╰━╯┃╰━╯┃╰━╯┃╰━━┳╯╰╯┃╰━╯┃┃┃┃┃┃    |
 * |   ╱╱╰╯╱╰━━━┻━━━┻━━━┻━━━┻━━━┻━━━┻╯╰╯╰╯    |
 * |__________________________________________|
 * |                                          |
 * | Permission is hereby granted, free of    |
 * | charge, to any person obtaining a copy of|
 * | of this software and accompanying files, |
 * | to use them without restriction,         |
 * | including, without limitation, the       |
 * | rights to use, copy, modify, merge,      |
 * | publish, distribute, sublicense and/or   |
 * | sell copies of the software. The authors |
 * | or copyright holders shall not be liable |
 * | for any claims, damages or other         |
 * | liability, whether in contract, tort or  |
 * | otherwise, arising out of or in          |
 * | connection with the software or your use |
 * | or other dealings with the software.     |
 * |__________________________________________|
 * |   website: tolsedum.ru                   |
 * |   email: tolsedum@gmail.com              |
 * |   email: tolsedum@yandex.ru              |
 * |__________________________________________|
 */

namespace App\Mailing\send_pulse\Request;

use App\Mailing\ApiSettings;
use App\Mailing\IApiRequest;
use App\Mailing\Request;
use App\Mailing\send_pulse\Api\ApiDataRequest;

use Exception;
use ReflectionClass;

class SendManager implements IApiRequest{
    protected ApiDataRequest $apiRequest;
    protected ApiSettings $apiSettings;

    /**
     * @param array $param [
     *      @param string 'client_id'     : value     
     *      @param string 'grant_type'    : value
     *      @param string 'client_secret' : value
     *      @param string 'access_token'  : value
     *      @param string 'token_type'    : value
     *      @param bool   'expires_in'    : value
     * ]
     */
    public function __construct(array $param){
        $this->apiSettings = new ApiSettings ([
                "settings" => $param,
                "model" => $this->getModel()
            ],
            [
                "grant_type" => "client_credentials",
                "client_id" => null,
                "client_secret" => null,
                "access_token" => null,
                "token_type" => "Bearer",
                "expires_in" => null
            ]
        );
        $this->apiRequest = new ApiDataRequest();
    }

    public function saveSettings(){
        $settings = $this->apiSettings->getSettings();
        if(empty($settings["access_token"])){
            $settings = $this->getAccessToken($settings);
            $settings["model"] = $this->getModel();
            $this->apiSettings->initObject($settings);
        }
        $this->apiSettings->saveSettings();

    }
    protected function getAccessToken($settings){
        $respons = Request::send([
            "method" => "post",
            "data" => $settings,
            "url" => $this->getRequestUrl("oauth/access_token")
        ]);
        $data = $respons->getJson();
        if(isset($data["error"])){
            throw new Exception($data["message"], 1);
        }
        $settings = array_merge($settings, $respons->getJson());
        return $settings;
    }

    public function getModel(){
        return "send_pulse";
    }

    /**
     * Get unisender api host
     * @return string
     */
    protected function getApiHost($path){
        return sprintf('https://api.sendpulse.com/%s', $path);
    }

    protected function getRequestUrl($path){
        $url = $this->getApiHost($path);
        return $url;
    }

    protected function getReturnParams($params){
        foreach (["method", "url_part", "data", "extra"] as $var) {
            if(isset($params[$var])){
                $$var = $params[$var];
            }else{
                // throw new ExceptionUnisender(ExceptionUnisender::EMPTY_PARAMS, $var);
            }
        }
        $settings = $this->apiSettings->getSettings();
        $extra["headers"][] = 'Authorization: ' 
            . $settings["token_type"] 
            . ' ' . $settings["access_token"];
        return [
            "method" => $method,
            "url" => $this->getRequestUrl($url_part),
            "data" => $data,
            "extra" => $extra
        ];
    }

    /**
     * Получение списка групп контактов
     */
    public function getContactList(array $param = []){
        $request_data = $this->apiRequest->getLists();
        return $this->getReturnParams($request_data);
    }

    /**
     * Добавление новой адресной базы
     * @param array $param = [
     *      string title                 => Название списка. Должно быть уникальным
     *      string before_subscribe_url  => URL для редиректа на страницу «перед подпиской»
     *      string after_subscribe_url   => URL для редиректа на страницу «после подписки»
     * ]
     *      
     */
    public function addContactList(array $param){
        $request_data = $this->apiRequest->createList($param);
        return $this->getReturnParams($request_data);
    }

    /**
     * Обновление контактной информации адресной базы
     * @param array $param = [
     *      int list_id   => Код списка, полученный методом getLists или createList.
     *      string title  => Название списка. Должно быть уникальным
     *      string before_subscribe_url  => URL для редиректа на страницу «перед подпиской»
     *      string after_subscribe_url   => URL для редиректа на страницу «после подписки»
     * ]
     */
    public function updateContactList(array $param){
        $request_data = $this->apiRequest->updateList($param);
        return $this->getReturnParams($request_data);
    }

    /**
     * Подписать адресата на один или несколько списков рассылки
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     * 
     *      string list_ids => Перечисленные через запятую коды списков, 
     *                  в которые надо добавить контакта
     *      array fields   => Ассоциативный массив дополнительных полей.
     *                  Массив в запросе передаётся строкой вида 
     *                  fields[NAME1]=VALUE1&fields[NAME2]=VALUE2
     *      array tags     => Перечисленные через запятую метки, которые 
     *                  добавляются к контакту. Максимально допустимое 
     *                  количество - 10 меток.
     *      int double_optin => const DoubleOption
     *      int overwrite => Режим перезаписывания полей и меток const OverwriteMode
     * ]
     */
    public function subscribeContact(array $param){
        $request_data = $this->apiRequest->subscribe($param);
        if(isset($request_data["data"]["fields"]["name"])){
            $request_data["data"]["fields"]["Name"] = 
                $request_data["data"]["fields"]["name"];
            unset($request_data["data"]["fields"]["name"]);
        }
        return $this->getReturnParams($request_data);
    }
}