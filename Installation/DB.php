<?php
/**
 * Description of DB
 *
 * @author boruc-san
 * @since  2012-11-02
 */
defined('_DP') or die("Direct access not allowed!");
final class DB extends mysqli {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    private static $instance;
    private static $config = array();
    
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    private function __construct() {
        $conf = self::$config;
        parent::__construct($conf['host'],
                $conf['user'],
                $conf['pass'],
                $conf['name'],
                $conf['port'],
                $conf['sock']);
        unset($conf);
        if(mysqli_connect_error()){
            throw new DBConnectException(mysqli_connect_error(), mysqli_connect_errno());
        }
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    //*************************************
    //           [**STATIC METHODS**]
    //*************************************
     public static final function getDB(){
        if(!self::$instance){
            try{
                self::$instance = new self();
            }
            catch (DBConnectException $dbce){
                trigger_error("DB ERROR ".$dbce->getCode().":".$dbce->getMessage(), E_USER_ERROR);
            }
        }
        return self::$instance;
    }
    public static final function configure(array $pConfigArray) {
        self::$config = $pConfigArray;
    }
}
class DBConnectException extends RuntimeException{
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
