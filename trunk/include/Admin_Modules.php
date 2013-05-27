<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Modules
 *
 * @author Takezo1115
 */
class Admin_Modules {
    public function __construct(){}
    public function GetMenuModules()
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT Modules_ID, Modules_name FROM ".DB_MODULES." ORDER BY Modules_ID DESC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function getDirectoryList ($directory) 
    {
        $results = array();
        $handler = opendir($directory);
        while ($file = readdir($handler)) {
           if ($file != "." && $file != ".." && $file != "index.html" && $file != "Module_body.php") {
               $file = str_ireplace(".php", "", $file);
               $results[] = $file;
            }
        }
        closedir($handler);
        return $results;
    }
    public function AddModule($module_name, $module_class) {
        $module_name = Utls::StipUserInput($module_name);
        $module_class = Utls::StipUserInput($module_class);
        
        $this->CheckAddData($module_name, $module_class);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "INSERT INTO ".DB_MODULES." (Modules_name, Modules_class, Modules_panel, Modules_hierarchy)
              VALUES('".$module_name."', '".$module_class."', 0, 0)";
        $r = $db->query($q);
    }
    public function DeleteModule($module_id) {
        $module_id = (int)  Utls::StipUserInput($module_id);
        $db = DB::getDB();
        $q = "SELECT Modules_ID FROM ".DB_MODULES." WHERE Modules_ID = ".$module_id."";
        $r = $db->query($q);
        if($r->num_rows != 1){
                throw new DeleteModuleException("Module not found in database", DeleteModuleException::MODULE_NOT_FOUND);
        }
        $q = "DELETE FROM ".DB_MODULES." WHERE Modules_ID = ".$module_id."";
        $r = $db->query($q);
        if($r->affected_rows < 0){
            throw new DeleteModuleException("Module not deleted", DeleteModuleException::MODULE_NOT_DELETED);
        }
    }
    private function CheckAddData($module_name, $module_class) {
        if(strlen($module_name) > 25){
            throw new AddModuleException("Name is too long", AddModuleException::NAME_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Modules_ID FROM ".DB_MODULES." WHERE Modules_name = '".$module_name."'";
        $r = $db->query($q);
        if($r->num_rows == 1){
            throw new AddModuleException("Module Name already used", AddModuleException::NAME_USED_ERROR);
        }
        if(strlen($module_class) > 25){
            throw new AddModuleException("Module Class is too long", AddModuleException::CLASS_SIZE_ERROR);
        }
    }
}
class AddModuleException extends RuntimeException
{
    const NAME_SIZE_ERROR          = 1;
    const NAME_USED_ERROR          = 2;
    const CLASS_SIZE_ERROR          = 3;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class DeleteModuleException extends RuntimeException
{
    const MODULE_NOT_FOUND    = 1;
    const MODULE_NOT_DELETED  = 2;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
