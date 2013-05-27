<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Register
 *
 * @author Takezo1115
 */
defined('_DP') or die("Direct access not allowed!");
class Register {
    //put your code here
    public function __construct($r_UserLogin, $r_UserPassword, $r_ConfirmUserPassword, $r_Email, $r_Name, $r_Surname){
        $r_UserLogin = Utls::StipUserInput($r_UserLogin);
        $r_UserPassword = Utls::StipUserInput($r_UserPassword);
        $r_ConfirmUserPassword = Utls::StipUserInput($r_ConfirmUserPassword);
        $r_Email = Utls::StipUserInput($r_Email);
        $r_Name = Utls::StipUserInput($r_Name);
        $r_Surname = Utls::StipUserInput($r_Surname);
        $this->AddUser($r_UserLogin, $r_UserPassword, $r_ConfirmUserPassword, $r_Email, $r_Name, $r_Surname);
    }
    
    
    private function AddUser($r_UserLogin, $r_UserPassword, $r_ConfirmUserPassword, $r_Email, $r_Name, $r_Surname) {
        $this->CheckData($r_UserLogin, $r_UserPassword, $r_ConfirmUserPassword, $r_Email, $r_Name, $r_Surname);
        $salt = $this->genAlgoSalt();
        $hash = crypt($r_UserPassword, $salt);
        $avatar = "default_user.png";
        $db = DB::getDB();
        $q = "INSERT INTO ".DB_USERS." (User_Login, User_Salt, User_Password, User_Email, User_Name, User_Surname, User_Level, User_Status, User_Avatar, User_Registered, User_Lastvisit)
              VALUES('".$r_UserLogin."', '".$salt."', '".$hash."', '".$r_Email."', '".$r_Name."', '".$r_Surname."', 3, 1, '".$avatar."', NOW(), NOW())";
        $r = $db->query($q);
    }
    
    private function CheckData($r_UserLogin, $r_UserPassword, $r_ConfirmUserPassword, $r_Email, $r_Name, $r_Surname) {
        
        if(!preg_match('/^([a-z0-9]{1})([a-z0-9_-]{2,11})$/Diu', $r_UserLogin)) {
            throw new InvalidDataException("Invalid Username", 1);
        }
        $dba = DB::getDB();
        $query = "SELECT User_ID FROM ".DB_USERS."
                  WHERE User_Login = '".$r_UserLogin."' LIMIT 1";
        $result = $dba->query($query);
        if($result->num_rows >= 1){
            throw new InvalidDataException("User already exists", 6);
        }
        if(!preg_match('/^(?=[a-z0-9_#@%\*-]*?[A-Z])(?=[a-z0-9_#@%\*-]*?[a-z])(?=[a-z0-9_#@%\*-]*?[0-9])([a-z0-9_#@%\*-]{8,24})$/Diu', $r_UserPassword)) {
            throw new InvalidDataException("Invalid Password", 2);
        }
        if($r_ConfirmUserPassword != $r_UserPassword) {
            throw new InvalidDataException("Mismatch of passwords", 7);
        }
        if(strlen($r_Name)>20){
            throw new InvalidDataException("Only 20 characters allowed", 3);
        }
        if(strlen($r_Surname)>20){
            throw new InvalidDataException("Only 20 characters allowed", 4);
        }
        if(!preg_match('/^([a-z0-9]{1})([^\s\t\.@]*)((\.[^\s\t\.@]+)*)@([a-z0-9]{1})((([a-z0-9-]*[-]{2})|([a-z0-9])*|([a-z0-9-]*[-]{1}[a-z0-9]+))*)((\.[a-z0-9](([a-z0-9-]*[-]{2})|([a-z0-9]*)|([a-z0-9-]*[-]{1}[a-z0-9]+))+)*)\.([a-z0-9]{2,6})$/Diu', $r_Email)){
            throw new InvalidDataException("Invalid Email", 5);
        }
        
    }
    
    protected static function genAlgoSalt() {
        $config = Config::getConfig();
        $str = intval($config->hash_str);
        switch ($config->hash_algo) {
            case "Blowfish":
                if($str < 4 || $str > 31){
                    $str = 10;
                }
                $salt = sprintf('$2a$%02d$', $str);
                $salt .= self::genRandomChars(22);
                break;
            case "SHA512":
                if($str < 1000 || $str > 999999999)
                {
                    $str = 5000;
                }
                $salt = sprintf('$6$rounds=%02d$', $str);
                $salt .= self::genRandomChars(16);
                break;
            case "SHA256":
                if($str < 1000 || $str > 999999999)
                {
                    $str = 5000;
                }
                $salt = sprintf('$5$rounds=%02d$', $str);
                $salt .= self::genRandomChars(16);
                break;
            case "MD5":
                $salt = "$1$".self::genRandomChars(12);
                break;
            default:
                trigger_error("Błędna konfiguracja skryptu!", E_USER_ERROR);
                break;
        }
        return $salt;
    }
    /**
     * Function creates random chars
     * @param type $pLenght
     * @return type
     */
    protected static function genRandomChars($pLenght = 12) {
        $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890";
        $count = strlen($chars) - 1;
        $string = "";
	for ($i = 0; $i <= $pLenght; $i++) {
            $string .= substr($chars, mt_rand(0, $count), 1);
	}
        return $string;
    }
    /**
     * Function checks if user is connected throught proxy
     * and gets his connection data: his real IP, proxy he uses
     * and host. Returns ArrayObject with this data's. 
     * @return type 
     */
    protected static function getUserIP()
    {
        /*
        if($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
            $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            $proxy = $_SERVER["REMOTE_ADDR"];
            $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
        }else{
            $IP = $_SERVER["REMOTE_ADDR"];
            $proxy = "No proxy detected";
            $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        }
         * Work in progress
         */
        $IP = $_SERVER["REMOTE_ADDR"];
        $proxy = "No proxy detected";
        $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $data = new ArrayObject();
        $data['User_IP'] = $IP;
        $data['Proxy'] = $proxy;
        $data['Host'] = $host;
        return $data;
    }

}
class InvalidDataException extends RuntimeException
{
    const PATTERN_LOGIN_ERROR    = 1;
    const PATTERN_PASSWORD_ERROR = 2;
    const LENGTH_NAME_ERROR      = 3;
    const LENGTH_SURNAME_ERROR   = 4;
    const PATTERN_EMAIL_ERROR    = 5;
    const USER_EXIST_ERROR       = 6;
    const PASSWORDS_MISMATCH     = 7;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}

?>
