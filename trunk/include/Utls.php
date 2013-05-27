<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utls
 *
 * @author boruc-san
 * @since  2012-11-15
 */
class Utls {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    const FILES = 101;
    const DIRS = 102;
    const ALL = 103;
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    public static function Redirect($pUrl) {
        header("Location: ".$pUrl);
        exit();
    }
    public static function getUrl() {
        $protocol = "http";
        if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
             $protocol .= "s";
        }
        $protocol .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL = $protocol.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].str_replace(basename($_SERVER['PHP_SELF']), "", $_SERVER['REQUEST_URI']);
        } 
        else {
            $pageURL = $protocol.$_SERVER["SERVER_NAME"].str_replace(basename($_SERVER['PHP_SELF']), "", $_SERVER['REQUEST_URI']);
        }
        return $pageURL;
    }
    public static function StipUserInput($input) {
        trim($input);
        if(MAGIC_QUOTES){
            stripslashes($input);
        }
        $wrong = array("'<script[^>]*?>.*?</script>'si", "'<[/!]*?[^<>]*?>'si", );
        
        $replace = array("", "");
        
        return preg_replace($wrong, $replace, $input);
    }
    public static function getFolderList($pFolder, $pSkip = array(".", ".."), $pType = self::ALL){
       if($pType != self::FILES && $pType != self::DIRS){
           $pType = self::ALL;
       }
       $list = array();
       $dir = opendir($pFolder);
       while($file = readdir($dir)){
           if(($pType == self::FILES || $pType == self::ALL) && !in_array($file, $pSkip)){
               if(!is_dir($pFolder.$file)){
                   $list[] = $file;
               }
           }
           else if(($pType == self::DIRS || $pType == self::ALL) && !in_array($file, $pSkip)){
               if(is_dir($pFolder.$file)){
                   $list[] = $file;
               }
           }
       }
       closedir($dir);
       return $list;
    }
}
?>
