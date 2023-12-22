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

namespace app\Mailing\send_pulse;

use Exception;

class ExceptionSendPulse extends Exception{
    /** No data */
    const NO_DATA = 8;

    /** Sender email address missing */
    const SENDER_EMAIL_MISSING = 10;

    /** Can’t find recipients addresses */
    const CF_RECIPIENTS_ADDRESSES = 11;

    /** Empty email message content field */
    const EMPTY_EMAIL_MESSAGE = 13;

    /** Can’t find email address with the specified ID */
    const CF_EMAIL_ID = 14;

    /** Can’t find the email address */
    const CF_EMAIL = 17;

    /** Email address already exists */
    const EMAIL_ALREADY_EXISTS = 19;

    /** Using free email services (not allowed) */
    const FREE_EMAIL_SERVICES = 20;

    /** No such email address awaiting activation */
    const NO_SUCH_EMAIL = 21;

    /** Invalid email address type. Using free email services is not allowed. */
    const INVALID_EMAIL_ADDRESS_TYPE = 97;

    /** Empty mailing list name */
    const EMPTY_MAILING_LIST_NAME = 201;

    /** This mailing list name already exists */
    const MAILING_LIST_NAME_ALREADY_EXISTS = 203;

    /** Mailing list empty */
    const MAILING_LIST_EMPTY = 211;

    /** Mailing list not found */
    const MAILING_LIST_NOT_FOUND = 213;

    /** Can’t find email addresses in the mailing list */
    const CF_EMAIL_IN_MAILING_LIST = 303;

    /** Specified SMTP user doesn’t exist. Create an SMTP account. */
    const CREATE_SMTP_ACCOUNT = 400;

    /** Can’t find the email address */
    const СА_EMAIL_ADDRESS = 502;

    /** Can’t find the campaign. Probably, it has already been sent. */
    const СА_CAMPAIGN = 602;

    /** Sender email address or name not specified. */
    const EMAIL_NAME_NOT_SPECIFIED = 701;

    /** Can’t find the mailing list */
    const СА_MAILING_LIST = 703;

    /** Can’t find the sender */
    const CF_SENDER = 704;

    /** Account balance depleted */
    const ACCOUNT_BALANCE_DEPLETED = 707;

    /** Wait 15 minutes before sending to the same list again */
    const WAIT_BEFORE_SENDING = 711;

    /** Empty subject field */
    const EMPTY_SUBJECT_FIELD = 720;

    /** Email message empty */
    const EMAIL_MESSAGE_EMPTY = 721;

    /** Mailing list ID not specified */
    const MAILING_LIST_ID_NOT_SPECIFIED = 722;

    /** API-campaigns limit exceeded (5 per hour) */
    const LIMIT_EXCEEDED = 791;

    /** Incorrect date format. Use YYYY-MM-DD hh:ii:ss that equals or greater than the current date */
    const INCORRECT_DATE_FORMAT = 799;

    /** Invalid operation */
    const INVALID_OPERATION = 800;

    /** Campaign not found */
    const CAMPAIGN_NOT_FOUND = 802;

    /** Sender name not specified */
    const SENDER_NAME_NOT_SPECIFIED = 901;

    /** Selected email address already in use */
    const EMAIL_ALREADY_USE = 902;

    /** Sender email address not specified */
    const SENDER_EMAIL_NOT_SPECIFIED = 903;

    /** Email address blacklisted */
    const EMAIL_BLACKLISTED = 904;

    /** Sender address quota reached */
    const SENDER_QUOTA_REACHED = 905;

    /** Email address syntax error */
    const EMAIL_SYNTAX_ERROR = 906;

    /** Email address not specified */
    const EMAIL_NOT_SPECIFIED = 1101;

    /** The specified sender doesn’t exist */
    const SENDER_DOESNT_EXIST = 1003;

    /** The activation code has been sent. Wait 15 minutes to retry */
    const ACTIVATION_CODE_SENT = 1004;
    
    /** Error sending confirmation */
    const ERROR_SENDING = 1005;

    /** Activation code not specified */
    const CODE_NOT_SPECIFIED = 1104;
    
    /** More than 10 requests per second */
    const MORE_REQUESTS_SECOND = 2020202020;


    protected $language_local = "ru";

    protected function getLanguageVarList(){
        $file_name = "../app/Mailing/unisender/language_vars/errors."
            . $this->language_local
            . ".json";
        return json_decode(file_get_contents($file_name), true);
    }
    
    protected function getDescription(string $error_code, string $message){
        $lang_var = $this->getLanguageVarList();
        if(isset($lang_var[$error_code])){
            return $lang_var[$error_code] . " {$message}";
        }
    }
    
    public function __construct(string $error_code, $message = ""){
        $message = $this->getDescription($error_code, $message);
        parent::__construct($message, 1);
    }
}