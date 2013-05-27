<?php
/**
 * Description of Admin_Settings
 *
 * @author boruc-san
 */
defined('_DP') or die("Direct access not allowed!");
class Admin_Settings {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($pTitle, $pTheme, $pHost, $pPath, $pAlgo, $pAlgoStr, $pExpire, $pActLimit, $pLiveLimit) {
        $pTitle = Utls::StipUserInput($pTitle);
        $pHost = Utls::StipUserInput($pHost);
        $pPath = Utls::StipUserInput($pPath);
        $pAlgoStr = Utls::StipUserInput($pAlgoStr);
        $pExpire = Utls::StipUserInput($pExpire);
        $pActLimit = Utls::StipUserInput($pActLimit);
        $pLiveLimit = Utls::StipUserInput($pLiveLimit);
        
        if(!is_numeric($pAlgoStr)){
            throw new InvalidSettingValueException("Algorithm strength must be number!", InvalidSettingValueException::INVALID_HASH_STR);
        }
        if(!is_numeric($pExpire)){
            throw new InvalidSettingValueException("Session expire must be number!", InvalidSettingValueException::INVALID_EXPIRE);
        }
        if(!is_numeric($pActLimit)){
            throw new InvalidSettingValueException("Last activity must be number!", InvalidSettingValueException::INVALID_NO_ACTIV);
        }
        if(!is_numeric($pLiveLimit)){
            throw new InvalidSettingValueException("Live message limit must be number!", InvalidSettingValueException::INVALID_MAX_LIVE);
        }
        $this->verifyHashStr($pAlgo, $pAlgoStr);
        $this->saveSettings($pTitle, $pTheme, $pHost, $pPath, $pAlgo, $pAlgoStr, $pExpire, $pActLimit, $pLiveLimit);
        Utls::Redirect("index.php");
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    private function verifyHashStr($pAlgo, $pAlgoStr) {
        switch ($pAlgo) {
            case "MD5":
                break;
            case "Blowfish":
                if($pAlgoStr < 4 || $pAlgoStr > 31){
                    throw new InvalidSettingValueException("", InvalidSettingValueException::INVALID_HASH_BLOWFISH);
                }
                break;
                case "SHA256":
                if($pAlgoStr < 1000 || $pAlgoStr > 999999999){
                    throw new InvalidSettingValueException("", InvalidSettingValueException::INVALID_HASH_SHA);
                }
                break;
                case "SHA512":
                if($pAlgoStr < 1000 || $pAlgoStr > 999999999){
                    throw new InvalidSettingValueException("", InvalidSettingValueException::INVALID_HASH_SHA);
                }
                break;
            default:
                break;
        }
    }
    private function saveSettings($pTitle, $pTheme, $pHost, $pPath, $pAlgo, $pAlgoStr, $pExpire, $pActLimit, $pLiveLimit) {
        $db = DB::getDB();
        echo "UPDATE ".DB_SETTINGS." SET Setting_Value=".$pTitle." WHERE Setting_Name='page_title'";
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pTitle."' WHERE Setting_Name='page_title'") == FALSE){
            throw new SettingsUpdateException();
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pTheme."' WHERE Setting_Name='theme'") == FALSE){
            throw new SettingsUpdateException();
        }
        if(SUPERADMIN){
            if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pHost."' WHERE Setting_Name='script_host'") == FALSE){
                throw new SettingsUpdateException();
            }
            if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pPath."' WHERE Setting_Name='script_path'") == FALSE){
                throw new SettingsUpdateException();
            }   
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pAlgo."' WHERE Setting_Name='hash_algo'") == FALSE){
            throw new SettingsUpdateException();
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pAlgoStr."' WHERE Setting_Name='hash_str'") == FALSE){
            throw new SettingsUpdateException();
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pExpire."' WHERE Setting_Name='session_expire'") == FALSE){
            throw new SettingsUpdateException();
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pActLimit."' WHERE Setting_Name='last_activity_limit'") == FALSE){
            throw new SettingsUpdateException();
        }
        if($db->query("UPDATE ".DB_SETTINGS." SET Setting_Value='".$pLiveLimit."' WHERE Setting_Name='live_messages_limit'") == FALSE){
            throw new SettingsUpdateException();
        }
    }
    //*************************************
    //           [**STATIC METHODS**]
    //*************************************
    public static function getAvailableHash() {
        return array("MD5", "Blowfish", "SHA256", "SHA512");
    }
}
class InvalidSettingValueException extends InvalidArgumentException{
    const INVALID_HASH_STR = 1;
    const INVALID_EXPIRE = 2;
    const INVALID_NO_ACTIV = 3;
    const INVALID_MAX_LIVE = 4;
    const INVALID_HASH_BLOWFISH = 5;
    const INVALID_HASH_SHA = 6;
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class SettingsUpdateException extends RuntimeException{
    public function __construct($message = "Failed to save settings", $code = 5, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
