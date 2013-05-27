<?php
/**
 * Description of Authenticate
 *
 * @author boruc-san, Takezo1115
 * @since  2012-11-02
 */
defined('_DP') or die("Direct access not allowed!");
define("COOKIE_NAME", COOKIE_PREFIX."USID");
define("COOKIE_DOMAIN", Config::getConfig()->script_host != 'localhost' ? Config::getConfig()->script_host : NULL);
class Authenticate {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    private $mUserData;
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($pInputUser, $pInputPassword, $pRemember = false) {
        $pInputUser = Utls::StipUserInput($pInputUser);
        $pInputPassword = Utls::StipUserInput($pInputPassword);
        $this->logIn($pInputUser, $pInputPassword, $pRemember);
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
   /**
    * Function returns current user data.
    * @return UserData
    */
   public function getData() {
       return $this->mUserData;
   }
   /**
    * Function authenticate user;
    * @param type $pInputUser Unstrippted input login 
    * @param type $pInputPassword Unstrippted input password
    * @param type $pRemember not used right now
    */
   private function logIn($pInputUser, $pInputPassword, $pRemember = false) {
        $db = DB::getDB();
        $query = "SELECT User_ID, User_Login, User_Avatar, User_Password, User_Salt, User_Level, User_Status
                  FROM ".DB_USERS." 
                  WHERE User_Login = '".$pInputUser."'";
        $result = $db->query($query);
        if($result == FALSE || $result->num_rows !== 1){
            throw new UnauthorizedAccessException("Login or pass invalid!", UnauthorizedAccessException::INVALID_LOGIN_PASS);
        }
        $row = $result->fetch_object();
        $this->mUserData = new UserData($row->User_Login, $row->User_Avatar, $row->User_Level, $row->User_Status, $row->User_ID);
        $result->free();
        if(!$this->validatePass($pInputPassword, $row->User_Password, $row->User_Salt)){
            throw new UnauthorizedAccessException("Login or pass invalid!", UnauthorizedAccessException::INVALID_LOGIN_PASS);
        }
        $this->clearExpiredData($row->User_ID);
        if($this->createSessionData($row->User_ID) === FALSE){
            throw new UnauthorizedAccessException("Failed to create session!", UnauthorizedAccessException::ERROR_CREATING_SESSION);
        }
    }
    /**
     * Validate input input password with given other password and salt;
     * @param type $pInputPassword
     * @param type $pPassword
     * @param type $pSalt
     * @return type
     */
    private function validatePass($pInputPassword, $pPassword, $pSalt) {
        $hash = crypt($pInputPassword, $pSalt);
        return $hash === $pPassword;
    }
    
    /**
     * Function insert data to DB and create session cookie.
     * @param type $pUserID
     * @return boolean
     */
    private function createSessionData($pUserID) {
                $Session_ID = self::generateSessionID();
                $User_Connection_Data = self::getUserIP();
                $User_Ip = $User_Connection_Data['User_IP'];
                $Expire = time() + Config::getConfig()->session_expire;
                $Lastaction = time();
                $db = DB::getDB();
                $query = "INSERT INTO ".DB_SESSION_STORE." 
                      (Session_ID, User_Id, Session_Expire, Session_Lastaction, Session_Ip)
                      VALUES('".$Session_ID."', ".$pUserID.", ".$Expire.", ".$Lastaction.", '".$User_Ip."')";
                $result = $db->query($query);
                if($result == FALSE) return FALSE;
                setcookie(COOKIE_NAME, $Session_ID, $Expire + 1800, Config::getConfig()->script_path, COOKIE_DOMAIN, false, true);
                return TRUE;
    }
    /**
     * Function clears older session data from DB based on given UserID
     * @param type $pUserID
     */
    private function clearExpiredData($pUserID) {
        $db = DB::getDB();
        $query = "DELETE FROM ".DB_SESSION_STORE." 
              WHERE User_Id=".$pUserID." AND Session_Expire<".time();
        $db->query($query);
    }
    /**
     * Function generates random session value
     * @return string
     */
    private static function generateSessionID(){
        $session_id = "";
        $session_id .= self::genRandomChars(16);
        $session_id .= time();
        
        return substr($session_id, 0, 32);
    }
    private static function RegenerateSessionID($pSessionID, $pUserID, $pSessionExpire) {
        $db = DB::getDB();
        $db->auto_comit(FALSE);
        $query = "DELETE FROM ".DB_SESSION_STORE."
                  WHERE Session_ID='".$_COOKIE[COOKIE_NAME]."'";
        $db->query($query);
        $Session_ID = self::generateSessionID();
        $User_Connection_Data = self::getUserIP();
        $User_Ip = $User_Connection_Data['User_IP'];
        $Lastaction = time();
        
        $db->auto_comit(TRUE);
        
    }
    /**
     * Function authenticate logged user.
     * @return \UserData
     */
    public static function CheckUser() {
        if(isset($_COOKIE[COOKIE_NAME]) && $_COOKIE[COOKIE_NAME] != ""){
            $db = DB::getDB();
            $query = "SELECT U.User_ID AS USID, User_Login, User_Avatar, Session_Lastaction, Session_Actioncount, User_Level, User_Status
                      FROM ".DB_SESSION_STORE." DSS JOIN ".DB_USERS." U ON DSS.User_ID=U.User_ID
                      WHERE Session_ID='".$_COOKIE[COOKIE_NAME]."' AND Session_Expire>".time()."
                      LIMIT 1";
            $result = $db->query($query);
            if($result == FALSE || $result->num_rows != 1){
                throw new SessionException("Session expired or not existed at all", 3);
            }
            $row = $result->fetch_object();
            $config = Config::getConfig();
            $user_connection_data = self::getUserIP();
            $action = time() - $row->Session_Lastaction;
            if($action > $config->last_activity_limit){
                throw new SessionException("Session not activity limit reached", 4);
            }
            $user = new UserData($row->User_Login, $row->User_Avatar, $row->User_Level, $row->User_Status, $row->USID);
            $query = "UPDATE ".DB_SESSION_STORE."
                      SET Session_Lastaction=".time().", Session_Ip='".$user_connection_data['User_IP']."'
                      WHERE Session_ID='".$_COOKIE[COOKIE_NAME]."'";
            $db->query($query);
            $db->query("UPDATE ".DB_USERS." SET User_Lastvisit=NOW()
                        WHERE User_Id=".$row->USID);
            return $user;
        } 
        else{
            return new UserData("", "", 0, 0, 0);
        }
    }
    /**
     * Function clears finished session data.
     */
    public static function logOut() {
        $db = DB::getDB();
        $query = "DELETE FROM ".DB_SESSION_STORE."
                  WHERE Session_ID='".$_COOKIE[COOKIE_NAME]."'";
        $db->query($query);
        setcookie(COOKIE_NAME, "", time() - 21600, Config::getConfig()->script_path, COOKIE_DOMAIN, false, true);
    }
    /**
     * Generates hash salt depends on algoritm in Config.php.
     * @return string Generated salt
     */
    public static function genAlgoSalt() {
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
    private static function genRandomChars($pLenght = 12) {
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
    private static function getUserIP()
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
class UnauthorizedAccessException extends RuntimeException{
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
        const INVALID_LOGIN_PASS = 1;
        const ERROR_CREATING_SESSION = 2;
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
}
class SessionException extends UnauthorizedAccessException{
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
        const SESSION_NOT_EXIST_OR_EXPIRED = 3;
        const SESSION_NOT_ACTIVE_LIMIT = 4;
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
}
?>