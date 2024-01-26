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
namespace Aapp\Mailing\send_pulse\Api\traits;

use App\Mailing\send_pulse\ExceptionSendPulse;;

/** 
 * Работа со списками контактов
 * @author Tolsedum
*/
trait Contacts{
    
    /**
     * Создать новый список рассылки
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#lists
     * @return array [
     *      url => url запроса
     * 
     *      название параметра => обязательный ли он
     * 
     *      string bookName => Название списка рассылки
     *      
     * ]
     */
    protected function createList($params){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "bookName" => true, 
            ], 
            "url" => "addressbooks",
            "method" => "post"
        ];
    }

    /**
     * Изменить свойства списка рассылки
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#edit-list
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     * 
     *      int id   => Идентификатор списка рассылки
     *      string name  => Новое название списка рассылки
     * ]
     */
    protected function updateList($params){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "id" => true,
                "name" => true, 
            ],
            "url" => "addressbooks/" . isset($params["id"]) ? $params["id"] : "",
            "methode" => "put"
        ];
    }

    /**
     * Получить списки для рассылок с их кодами
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#lists-list
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      int limit Количество записей необязательно
     *      int offset Смещение (первая отображаемая запись)
     * ]
     */
    protected function getLists($params){
        return [
            "pattern" => [
                "limit" => false,
                "offset" => false
            ],
            "url" => "addressbooks",
            "method" => "get"
        ];
    }

    /**
     * Получить информацию о списке рассылки 
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#edit-list
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      int id Идентификатор списка рассылки
     * ]
     */
    protected function getBoock($params){
        return [
            "pattern" => [
                "id" => true,
            ],
            "url" => "addressbooks/" . isset($params["id"]) ? $params["id"] : "",
            "method" => "get"
        ];
    }

    /**
     * Получение списка переменных для списка рассылки
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#variables
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      int id Идентификатор списка рассылки
     * ]
     */
    protected function getBoockParams($params){
        return [
            "pattern" => [
                "id" => true,
            ],
            "url" => "addressbooks/" 
                . isset($params["id"]) ? $params["id"] : "" 
                . "/variables",
            "method" => "get"
        ];
    }

    /**
     * Получить список писем из списка рассылки
     * @link https://sendpulse.com/ru/integrations/api/bulk-email#lists-emails
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      int id Идентификатор списка рассылки
     *      int limit Количество записей необязательно
     *      int offset Смещение (первая запись для отображения)
     *      bool active Используйте этот параметр, чтобы получить список контактов со статусами "Активный" и "Новый".
     *      bool not_active	Используйте этот параметр, чтобы получить список неактивных контактов. Подробнее "Электронная почта
     * ]
     */
    protected function getListMails($params){
        return [
            "pattern" => [
                "id" => true,
                "limit" => false,
                "offset" => false,
                "active" => false,
                "not_active" => false,
            ],
            "url" => "addressbooks/" 
                . isset($params["id"]) ? $params["id"] : "" 
                . "/emails",
            "method" => "get"
        ];
    }

    

    
    
    /**
     * Удалить список контактов
     * @link https://www.unisender.com/ru/support/api/contacts/deletelist/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      int list_id   => Код списка, полученный методом getLists или createList.
     * ]
     */
    protected function deleteList($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "list_id" => true,
            ],
            "url" => "deleteList"
        ];
    }
    /**
     * Подписать адресата на один или несколько списков рассылки
     * @link https://www.unisender.com/ru/support/api/contacts/subscribe/
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
    protected function subscribe($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "list_ids" => true,
                "fields" => true,
                "tags" => false,
                "overwrite" => false,
            ],
            "url" => "subscribe",
        ];
    }
    /**
     * Исключить адресата из списков рассылки
     * @link https://www.unisender.com/ru/support/api/contacts/exclude/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      string contact_type => Тип исключаемого контакта - либо 'email', либо 'phone'.
     *      string contact      => Email или телефон, который исключаем
     *      array list_ids     => Перечисленные через запятую коды списков, из которых мы 
     *                      исключаем контакта. Если не указаны, то исключаем из 
     *                      всех списков. Коды списков можно узнать с помощью метода 
     *                      getLists. Они совпадают с кодами, используемыми в форме 
     *                      подписки.
     * ]
     */
    protected function exclude($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "contact_type" => true,
                "contact" => true,
                "list_ids" => false,
            ],
            "url" => "exclude",
        ];
    } 
    /**
     * Отписать адресата от рассылки
     * @link https://www.unisender.com/ru/support/api/contacts/exclude/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      string contact_type => Тип исключаемого контакта - либо 'email', либо 'phone'.
     *      string contact      => Email или телефон, который исключаем.
     *      array list_ids     => Перечисленные через запятую коды списков, из которых мы 
     *                      исключаем контакта. Если не указаны, то исключаем из всех 
     *                      списков. Коды списков можно узнать с помощью метода getLists. 
     *                      Они совпадают с кодами, используемыми в форме подписки.
     * ]
     */
    
    protected function unsubscribe($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "contact_type" => true,
                "contact" => true,
                "list_ids" => false,
            ],
            "url" => "unsubscribe"
        ];
    }
    /**
     * Массовый импорт и синхронизация контактов;
     * @link https://www.unisender.com/ru/support/api/contacts/importcontacts/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      array field_names     => FieldNames Массив названий столбцов данных. 
     *                               Обязательно должно присутствовать хотя бы 
     *                               поле «email», иначе метод вернет ошибку.
     *      array data            => Массив данных контактов, каждый элемент которого — 
     *                               массив полей в том порядке, в котором следуют field_names.
     *      bool overwrite_tags   => Необязательное логическое поле (0 или 1, по умолчанию 0). Перезаписываются ли метки (если 1), 
     *                               или только добавляются новые, не удаляя старых (если 0).
     *      bool overwrite_lists  => Необязательное логическое поле (0 или 1, по умолчанию 0).
     *                               Единица означает — заменить на новые все данные 
     *                               о том, когда и в какие списки включены и от каких 
     *                               отписаны контакты.
     * ]
     */
    protected function importContacts($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "field_names" => true,
                "data" => true,
                "overwrite_tags" => false,
                "overwrite_lists" => false,
            ],
            "url" => "importContacts"
        ];
    }
    /**
     * Экспорт данных по контактам;
     * @link https://www.unisender.com/ru/support/api/contacts/exportcontacts/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      string notify_url  => URL, на который будет отправлен ответ после того, 
     *                            как файл экспорта будет сформирован.
     *      array list_id      => Необязательный код экспортируемого списка. 
     *                            Если не указан, будут экспортированы все списки. 
     *                            Коды списков можно узнать с помощью метода getLists.
     *      array field_names  => Массив имён системных и пользовательских полей, 
     *                            которые надо экспортировать. Если отсутствует, 
     *                            то экспортируются все возможные поля. Способ передачи
     *                            с помощью HTTP: field_names[]=1&field_names[]=2
     *      string email       => Email адрес. Если этот параметр указан, то результат 
     *                            будет содержать только один контакт с таким e-mail адресом.
     *      string phone       => Номер телефона. Если этот параметр указан, то результат 
     *                            будет содержать только один контакт с таким номером телефона.
     *      string tag         => Метка. Если этот параметр указан, то при поиске будут 
     *                            учтены только контакты, имеющие такую метку 
     *      string email_status=> Статус email адреса. Если этот параметр указан, то результат 
     *                            будет содержать только контакты с таким статусом email адреса.
     *                            EmailStatus
     *      string phone_status=> Статус телефона. Если этот параметр указан, то результат будет 
     *                            содержать только контакты с таким статусом телефона.
     *                            PhoneStatus
     * ]
     */
    protected function exportContacts($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "notify_url" => false,
                "list_id" => false,
                "field_names" => false,
                "email" => false,
                "phone" => false,
                "tag" => false,
                "email_status" => false,
                "phone_status" => false,
            ],
            "url" => "async/exportContacts"
        ];
    }
    /**
     * Запросить статус выполнения задания
     * @link https://www.unisender.com/ru/support/api/contacts/exportcontacts/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      
     * ]
     */
    protected function getTaskResult($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "task_uuid" => true,
            ],
            "url" => "async/getTaskResult",
        ];
    }


    /**
     * Получить информацию о размере базы пользователя
     * @link https://www.unisender.com/ru/support/api/contacts/gettotalcontactscount/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      string login => Логин пользователя в системе.
     * ]
     */
    protected function getTotalContactsCount($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => ["login" => true,],
            "url" => "getTotalContactsCount"
        ];
    } 
    /**
     * Получить количество контактов в списке
     * @link https://www.unisender.com/ru/support/api/contacts/getcontactcount/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      array list_id => id списка, по которому осуществляется поиск.
     *      array params  => список параметров для поиска (хотя бы один параметр).
     * ]
     */
    protected function getContactCount($params = []){
        if(empty($params)){
            throw new ExceptionSendPulse(ExceptionSendPulse::EMPTY_PARAMS);
        }
        return [
            "pattern" => [
                "list_id" => true,
                "params" => true,
            ],
            "url" => "getContactCount"
        ];  
    } 
    /**
     * Получить информацию об одном контакте.
     * @link https://www.unisender.com/ru/support/api/contacts/getcontact/
     * @return array [
     *      url => url запроса
     *      название параметра => обязательный ли он
     *      email => E-mail адрес. Возвращает данные контакта по заданному значению этого параметра.
     *      array include_lists => Вывод информации о списках, в которые добавлен контакт. Параметр принимает значение 1 или 0.
     *      array include_fields => Вывод информации о дополнительных полях контакта. Параметр принимает значение 1 или 0.
     *      array include_details => Вывод дополнительной информации о контакте. Параметр принимает значение 1 или 0.
     * ]
     */
    protected function getContact($params = []){
        return [
            "pattern" => [
                "email" => true,
                "include_lists" => false,
                "include_fields" => false,
                "include_details" => false,
            ],
            "url" => "getContact"
        ];  
    }
}