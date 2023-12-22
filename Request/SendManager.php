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

use App\Mailing\IApiRequest;
use App\Mailing\send_pulse\Api\ApiRequest;
use App\Mailing\send_pulse\ExceptionSendPulse;

class SendManager implements IApiRequest{
    /** @var string API access key */
    protected $api_key;
    /** @var string API server message language (currently supported ru, en, ua) */
    protected $lang;
    /** @var bool  */
    protected $compression = false;

    protected ApiRequest $apiRequest;

    /**
     * @param string $api_key 
     * @param string $lang
     * @param bool $compression
     */
    public function __construct(array $param){
        if(empty($param["api_key"])){
            // throw new ExceptionSendPulse(ExceptionSendPulse::);
        }else{
            $this->api_key = $param["api_key"];
        }
        foreach ([
            "lang" => "ru",
            "compression" => false
        ] as $_param => $default) {
            if(isset($param[$_param])){
                $this->{$_param} = $param[$_param];
            }else{
                $this->{$_param} = $default;
            }
        }
        $this->apiRequest = new ApiRequest(); 
    }

    /**
     * Get unisender api host
     * @return string
     */
    protected function getApiHost(){
        return sprintf('https://api.sendpulse.com', $this->lang);
    }

    protected function getRequestUrl($methode){
        $url = $this->getApiHost()
            . $methode 
            . "?format=json";

        if ($this->compression) {
            $url .= '&request_compression=bzip2';
        }else{
            $url .= "&api_key=" . $this->api_key;
        }
        return $url;
    }

    protected function compresData($url, &$request_params){
        if ($this->compression) {
            $request_params = [];
            return $url . "/" .bzcompress(http_build_query($request_params));
        } 
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
        return [
            "method" => $method,
            "url" => $this->compresData(
                $this->getRequestUrl($url_part),
                $data
            ),
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