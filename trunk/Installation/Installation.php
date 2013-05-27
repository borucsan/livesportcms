<?php
/**
 * Description of Installation
 *
 * @author boruc-san
 */
defined('_DP') or die("Direct access not allowed!");
class Installation {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    const CONF_FILENAME = "./config/db_config.php";
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    private $mStep = 1;
    public $db = NULL;
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($pStep) {
        $this->mStep = $pStep;
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    public function getStepTitle() {
        switch ($this->mStep) {
            default:
            case 1:
                return "Krok 1 - Start.";
                break;
            case 2:
                return "Krok 2 - Konfiguracja bazy danych MySQL.";
                break;
            case 3:
                return "Krok 3 - Konfiguracja bazy danych MySQL.";
                break;
            case 4:
                return "Krok 4 - Konfiguracja serwisu.";
                break;
            case 5:
                return "Krok 5 - Konfiguracja serwisu.";
                break;
            case 6:
                return "Krok 6 - Tworzenie użytkownika.";
                break;
            
        }
    }
    public function verifyFileRights(){
        clearstatcache();
        if(!file_exists(self::CONF_FILENAME)){
            @fclose(fopen(self::CONF_FILENAME, "w"));
        }
        $writeable = array("config" => false,
                        "config/db_config.php" => false,
                        "media" => false,
                        "media/images" => false,
                        "media/images/avatars" => false,
                        "media/sounds" => false);
        foreach ($writeable as $key => $value) {
            if(file_exists($key)){
                if(is_writable($key)){
                    $writeable[$key] = true;
                }
                else if(@chmod ($key, "0777") && clearstatcache() && is_writable($key)){
                    $writeable[$key] = true;
                }
                else{
                    $writeable[$key] = false;
                }
            }
            else{
                $writeable[$key] = false;
            }
        }
        return $writeable;
    }
    public function checkDBSettings($pDB_host, $pDB_port, $pDB_name, $pDB_user, $pDB_password, $pDB_prefix) {
        @$this->db = new mysqli($pDB_host, $pDB_user, $pDB_password, $pDB_name, $pDB_port, false);
        if(mysqli_connect_error()){
            throw new InstallDataBaseException(mysqli_connect_errno()." ".mysqli_connect_error(), InstallDataBaseException::ERR_CONECT_DB);
        }
        $name = $pDB_prefix.mt_rand(1, 999)."test";
        $query = "CREATE TABLE ".$name."
                  (TEST INTEGER NOT NULL) ENGINE=InnoDB";
        if($this->db->query($query) == FALSE){
            throw new InstallDataBaseException("Failed to create test DB", InstallDataBaseException::ERR_CREATE_TEST_DB);
        }
        $query = "DROP TABLE ".$name;
        if($this->db->query($query) == FALSE){
            throw new InstallDataBaseException("Failed to drop test DB.", InstallDataBaseException::ERR_DEL_TEST_DB);
        }
    }
    public function SaveConfig($pDB_host, $pDB_port, $pDB_name, $pDB_user, $pDB_password, $pDB_prefix) {
        $file = @fopen(self::CONF_FILENAME, "w");
        $content = "<?php\n";
        $content .= "\$db_config = array();\n";
        $content .= "\$db_config['host'] = \"".$pDB_host."\";\n";
        $content .= "\$db_config['name'] = \"".$pDB_name."\";\n";
        $content .= "\$db_config['user'] = \"".$pDB_user."\";\n";
        $content .= "\$db_config['pass'] = \"".$pDB_password."\";\n";
        $content .= "\$db_config['port'] = ".$pDB_port.";\n";
        $content .= "\$db_config['sock'] = false;\n";
        $content .= "define(\"DB_PREFIX\", \"".$pDB_prefix."\");\n";
        $content .= "define(\"COOKIE_PREFIX\", \"".$pDB_prefix."\");\n";
        $content .= "?>";
        if(fwrite($file, $content) === FALSE){
            fclose($file);
            throw new InstallDataBaseException("Failed to save config file.", InstallDataBaseException::ERR_SAVE_CONF);
        }
        fclose($file);
    }
    public function CreateTables($pDBprefix) {
        $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."users");
        if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
        }
        $result = $this->db->query("CREATE TABLE `".$pDBprefix."users` (
                                    `User_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                    `User_Login` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
                                    `User_Salt` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
                                    `User_Password` varchar(128) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
                                    `User_Name` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT '',
                                    `User_Surname` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT '',
                                    `User_Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
                                    `User_Level` tinyint(3) NOT NULL DEFAULT '0',
                                    `User_Status` tinyint(1) NOT NULL DEFAULT '0',
                                    `User_Avatar` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
                                    `User_Lastvisit` datetime NOT NULL,
                                    `User_Registered` datetime NOT NULL,
                                    PRIMARY KEY (`User_Id`),
                                    UNIQUE KEY `User_Login` (`User_Login`)
                                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."session_store");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."session_store` (
                                     `Session_Id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                                     `User_Id` int(10) unsigned NOT NULL,
                                     `Session_Expire` int(20) unsigned NOT NULL,
                                     `Session_Lastaction` int(20) unsigned NOT NULL,
                                     `Session_Ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0',
                                     `Session_Actioncount` tinyint(3) unsigned NOT NULL DEFAULT '0',
                                     PRIMARY KEY (`Session_Id`),
                                     KEY `User_Id` (`User_Id`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         

         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."categories");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."categories` (
                                     `Categorie_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Categorie_Name` varchar(15) NOT NULL,
                                     `Categorie_Live_Template` varchar(64) NOT NULL,
                                     PRIMARY KEY (`Categorie_ID`)
                                     ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."subcategories");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."subcategories` (
                                     `Subcategory_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Categorie_ID` int(10) NOT NULL,
                                     `Subcategory_Name` varchar(128) NOT NULL,
                                     PRIMARY KEY (`Subcategory_ID`),
                                     KEY `Categorie_ID` (`Categorie_ID`)
                                     ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."articles");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallationException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."articles` (
                                     `Article_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `User_ID` int(10) unsigned NOT NULL,
                                     `Subcategory_ID` int(10) unsigned DEFAULT NULL,
                                     `Commentary_ID` int(10) unsigned DEFAULT NULL,
                                     `Live_ID` int(10) unsigned DEFAULT NULL,
                                     `Article_Title` varchar(48) NOT NULL,
                                     `Article_Creation_Date` datetime NOT NULL,
                                     `Article_Brief` varchar(512) NOT NULL,
                                     `Article_Text` text NOT NULL,
                                     `Article_Image` varchar(40) DEFAULT NULL,
                                     PRIMARY KEY (`Article_ID`),
                                     KEY `User_ID` (`User_ID`),
                                     KEY `Commentary_ID` (`Commentary_ID`),
                                     KEY `Live_ID` (`Live_ID`)
                                     ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."live_commentary");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."live_commentary` (
                                     `Live_Commentary_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `User_ID` int(10) unsigned NOT NULL,
                                     `Score_ID` int(10) unsigned NOT NULL,
                                     `Live_Commentary_Live` tinyint(1) unsigned NOT NULL DEFAULT '0',
                                     PRIMARY KEY (`Live_Commentary_ID`),
                                     KEY `User_ID` (`User_ID`),
                                     KEY `Score_ID` (`Score_ID`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."live_messages");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."live_messages` (
                                     `Live_Message_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Live_Commentary_ID` int(10) unsigned NOT NULL,
                                     `Live_Message_Update` datetime NOT NULL,
                                     `Live_Message_Title` varchar(64) NOT NULL DEFAULT '\"\"',
                                     `Live_Message_Text` text NOT NULL,
                                     `Live_Message_Order` mediumint(8) unsigned NOT NULL,
                                     PRIMARY KEY (`Live_Message_ID`),
                                     KEY `Live_Stream_ID` (`Live_Commentary_ID`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."scores");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."scores` (
                                     `Score_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Subcategory_ID` int(10) unsigned NOT NULL,
                                     `Score_Home_Name` varchar(128) NOT NULL,
                                     `Score_Away_Name` varchar(128) NOT NULL,
                                     `Score_Home_Score` mediumint(9) NOT NULL DEFAULT '0',
                                     `Score_Away_Score` mediumint(9) NOT NULL DEFAULT '0',
                                     `Score_Event_Datetime` datetime NOT NULL,
                                     PRIMARY KEY (`Score_ID`),
                                     KEY `Category_ID` (`Subcategory_ID`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."subscores");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."subscores` (
                                     `Subscore_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Score_ID` int(10) unsigned NOT NULL,
                                     `Subscore_Home_Subscore` smallint(5) unsigned NOT NULL DEFAULT '0',
                                     `Subscore_Away_Subscore` smallint(5) unsigned NOT NULL DEFAULT '0',
                                     `Subscore_Event_Order` tinyint(3) unsigned NOT NULL,
                                     PRIMARY KEY (`Subscore_ID`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."settings");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."settings` (
                                     `Setting_Name` varchar(128) NOT NULL,
                                     `Setting_Value` varchar(256) NOT NULL,
                                     PRIMARY KEY (`Setting_Name`)
                                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
         
         
         $result = $this->db->query("DROP TABLE IF EXISTS ".$pDBprefix."modules");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to delete existing table.", InstallDataBaseException::ERR_DEL_DB);
         }
         $result = $this->db->query("CREATE TABLE `".$pDBprefix."modules` (
                                     `Modules_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                     `Modules_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
                                     `Modules_class` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
                                     `Modules_panel` int(1) NOT NULL DEFAULT '0',
                                     `Modules_hierarchy` int(5) NOT NULL DEFAULT '0',
                                     PRIMARY KEY (`Modules_ID`)
                                     ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
         if($result === FALSE){
            throw new InstallDataBaseException("Failed to create table.", InstallDataBaseException::ERR_CREATE_DB);
         }
    }
    public function SaveSettings($pPageTitle, $pTheme, $pAlgo, $pAlgoStr, $pExpire, $pLastActivity, $pMaxLMLimit) {
        if(!is_numeric($pAlgoStr) && $pAlgo != "MD5"){
            throw new InstallSettingsException("Algorithm strength is NaN!", InstallSettingsException::INVALID_DATA);
        }
        $this->verifyHashStr($pAlgo, $pAlgoStr);
        if(!is_numeric($pExpire)){
            throw new InstallSettingsException("Session Expire is NaN!", InstallSettingsException::INVALID_DATA);
        }
        if(!is_numeric($pLastActivity)){
            throw new InstallSettingsException("Last activity is NaN!", InstallSettingsException::INVALID_DATA);
        }
        if(!is_numeric($pMaxLMLimit)){
            throw new InstallSettingsException("live message limit is NaN!", InstallSettingsException::INVALID_DATA);
        }
        $url = parse_url(Utls::getUrl());
        $db = DB::getDB();
        $db->set_charset("utf8");
        $query = "INSERT INTO ".DB_PREFIX."settings(Setting_Name, Setting_Value) VALUES";
        $query .= "('page_title', '".$pPageTitle."'),\n";
        $query .= "('theme', '".$pTheme."'),\n";
        $query .= "('script_path', '".$url['path']."'),\n";
        $query .= "('script_host', '".$url['host']."'),\n";
        $query .= "('hash_algo', '".$pAlgo."'),\n";
        $query .= "('hash_str', '".$pAlgoStr."'),\n";
        $query .= "('session_expire', '".$pExpire."'),\n";
        $query .= "('last_activity_limit', '".$pLastActivity."'),\n";
        $query .= "('live_messages_limit', '".$pMaxLMLimit."')";
        if($db->query($query) == FALSE){
            throw new InstallDataBaseException("Failed to save settings in db.", InstallDataBaseException::ERR_SAVE_SETTINGS);
        }
        $this->AddDefaultModules();
    }
    private function AddDefaultModules(){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $query = "INSERT INTO ".DB_PREFIX."modules(Modules_name, Modules_class, Modules_panel, Modules_hierarchy) VALUES";
        $query .= "('Użytkownik', 'User_Module', 2, 1),\n";
        $query .= "('Ostatnie wyniki', 'Latest_Scores', 1, 1)";
        if($db->query($query) == FALSE){
            throw new InstallDataBaseException("Failed to save settings in db.", InstallDataBaseException::ERR_SAVE_SETTINGS);
        }
    }
    private function verifyHashStr($pAlgo, $pAlgoStr) {
        switch ($pAlgo) {
            case "MD5":
                break;
            case "Blowfish":
                if($pAlgoStr < 4 || $pAlgoStr > 31){
                    throw new InstallSettingsException("Invalid Blowfish str.", InstallSettingsException::INV_BLOWFISH_STR);
                }
                break;
                case "SHA256":
                if($pAlgoStr < 1000 || $pAlgoStr > 999999999){
                    throw new InstallSettingsException("Invalid SHA256 str.", InstallSettingsException::INV_SHA_STR);
                }
                break;
                case "SHA512":
                if($pAlgoStr < 1000 || $pAlgoStr > 999999999){
                    throw new InstallSettingsException("Invalid SHA512 str.", InstallSettingsException::INV_SHA_STR);
                }
                break;
            default:
                break;
        }
    }
    //*************************************
    //           [**STATIC METHODS**]
    //*************************************
}
abstract class InstallationException extends RuntimeException{
   public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
   }
}
class InstallDataBaseException extends InstallationException{
    const ERR_CREATE_TEST_DB = 1;
    const ERR_DEL_TEST_DB = 2;
    const ERR_SAVE_CONF = 3;
    const ERR_DEL_DB = 4;
    const ERR_CREATE_DB = 5;
    const ERR_CONECT_DB = 6;
    const ERR_SAVE_SETTINGS = 7;
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class InstallSettingsException extends InstallationException{
    const INVALID_DATA = 12;
    const INV_BLOWFISH_STR = 13;
    const INV_SHA_STR = 14;
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
