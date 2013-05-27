<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Panels
 *
 * @author Takezo1115
 */
class Admin_Panels {
    public function __construct(){}
    public function GetModule($id)
    {
        $id = Utls::StipUserInput($id);
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_MODULES." WHERE Modules_ID = ".$id." LIMIT 1";
        $r = $db->query($q);
        $row = $r->fetch_array();
        return $row;
    }
    public function GetModules()
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_MODULES." ORDER BY Modules_hierarchy ASC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function EditModule($module_id, $order, $status)
    {
        $module_id = Utls::StipUserInput($module_id);
        $order = Utls::StipUserInput($order);
        $status = Utls::StipUserInput($status);
        $this->CheckEditModuleData($module_id, $order, $status);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "UPDATE ".DB_MODULES." 
              SET Modules_hierarchy=".$order.", Modules_panel=".$status." 
              WHERE Modules_ID=".$module_id."";
        $r = $db->query($q);
        if(!$r)
        {
            throw new EditModuleException("Edit module database error", EditModuleException::MODULE_NOT_CHANGED);
        }
    }
    private function CheckEditModuleData($module_id, $order, $status) {
        if(!is_numeric($module_id)) {
            throw new EditModuleException("Module ID is detected as non integer value", EditModuleException::MODULE_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Modules_ID FROM ".DB_MODULES." WHERE Modules_ID = ".$module_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditModuleException("Module not found in database", EditModuleException::MODULE_NOT_FOUND);
            }
        }
        if(!is_numeric($order)) {
            throw new EditModuleException("Order is detected as non integer value", EditModuleException::ORDER_NUMBER_ERROR);
        }
        if(!is_numeric($status)) {
            throw new EditModuleException("Status is detected as non integer value", EditModuleException::STATUS_NUMBER_ERROR);
        }else{
            if($status > 2){
                throw new EditModuleException("Status cannot be greater than 2", EditModuleException::STATUS_ERROR);
            }
        }
    }
}
class EditModuleException extends RuntimeException
{
    const MODULE_ID_NUMBER_ERROR    = 1;
    const MODULE_NOT_FOUND          = 2;
    const ORDER_NUMBER_ERROR        = 3;
    const STATUS_NUMBER_ERROR       = 4;
    const STATUS_ERROR              = 5;
    const MODULE_NOT_CHANGED        = 6;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}

?>
