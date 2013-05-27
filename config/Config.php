<?php
/**
 * Description of Config
 *
 * @author boruc-san
 * @since  2012-11-01
 */
defined('_DP') or die("Direct access not allowed!");
final class Config {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    
    private static $instance;
        
    private $mData = array();
    
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    private function __construct() {
        $this->getData();
    }

    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    private function getData() {
        $db = DB::getDB();
        $query = "SELECT Setting_Name, Setting_Value
                  FROM ".DB_SETTINGS;
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("");
        }
        while ($row = $result->fetch_object()) {
            $this->mData[$row->Setting_Name] = $row->Setting_Value;
        }
    }
    public function __get($name) {
        return $this->mData[$name];
    }
    //*************************************
    //           [**STATIC METHODS**]
    //*************************************
    public static final function getConfig(){
        if(!self::$instance){
            try{
                self::$instance = new self();
            }
            catch(Exception $e){
                
            }
        }
        return self::$instance;
    }
}
?>
