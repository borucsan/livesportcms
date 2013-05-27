<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Users
 *
 * @author Takezo1115
 */
class Admin_Users {
    public function __construct(){}
    public function GetUser($id)
    {
        $id = Utls::StipUserInput($id);
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_USERS." WHERE User_ID = ".$id." LIMIT 1";
        $r = $db->query($q);
        $row = $r->fetch_array();
        return $row;
    }
    public function GetUsers()
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_USERS." ORDER BY User_Login";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function EditUser($user_id, $user_name, $user_surname, $user_mail, $user_level, $user_status)
    {
        $user_id = Utls::StipUserInput($user_id);
        $user_name = Utls::StipUserInput($user_name);
        $user_surname = Utls::StipUserInput($user_surname);
        $this->CheckEditUserData($user_name, $user_surname, $user_mail, $user_level, $user_status);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "UPDATE ".DB_USERS." 
              SET User_Name='".$user_name."', 
                  User_Surname='".$user_surname."', 
                  User_Email='".$user_mail."', 
                  User_Level=".$user_level.", 
                  User_Status=".$user_status." 
              WHERE User_ID=".$user_id."";
        $r = $db->query($q);
        if(!$r)
        {
            throw new EditUserException("Edit user database error", EditUserException::USER_NOT_CHANGED);
        }
    }
    public function DeleteUsers()
    {
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "DELETE FROM ".DB_USERS." WHERE User_Status = 3";
        $r = $db->query($q);
         if($r->affected_rows < 0){
            throw new EditUserException("Users not deleted", EditUserException::USERS_NOT_DELETED);
        }
    }
    private function CheckEditUserData($user_name, $user_surname, $user_mail, $user_level, $user_status) {
        
        if(strlen($user_name)>20){
            throw new EditUserException("Only 20 characters allowed for Name", EditUserException::USER_NAME_ERROR);
        }
        if(strlen($user_surname)>20){
            throw new EditUserException("Only 20 characters allowed for Surname", EditUserException::USER_SURNAME_ERROR);
        }
        if(!preg_match('/^([a-z0-9]{1})([^\s\t\.@]*)((\.[^\s\t\.@]+)*)@([a-z0-9]{1})((([a-z0-9-]*[-]{2})|([a-z0-9])*|([a-z0-9-]*[-]{1}[a-z0-9]+))*)((\.[a-z0-9](([a-z0-9-]*[-]{2})|([a-z0-9]*)|([a-z0-9-]*[-]{1}[a-z0-9]+))+)*)\.([a-z0-9]{2,6})$/Diu', $user_mail)){
            throw new EditUserException("Invalid Email", EditUserException::USER_MAIL_ERROR);
        }
        if((int)$user_level < 0 || (int)$user_level > 3)
        {
            throw new EditUserException("User Level out of range", EditUserException::USER_LEVEL_ERROR);
        }
        if((int)$user_status < 0 || (int)$user_status > 3)
        {
            throw new EditUserException("User Status out of range", EditUserException::USER_STATUS_ERROR);
        }
    }
    
}
class EditUserException extends RuntimeException
{
    const USER_NAME_ERROR           = 1;
    const USER_SURNAME_ERROR        = 2;
    const USER_MAIL_ERROR           = 3;
    const USER_LEVEL_ERROR          = 4;
    const USER_STATUS_ERROR         = 5;
    const USER_NOT_CHANGED          = 6;
    const USERS_NOT_DELETED         = 7;
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
