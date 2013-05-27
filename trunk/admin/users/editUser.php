<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Users.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_edit']) )
        {
            $admin_object = new Admin_Users();
            $user_id = (int) $_POST['user_id'];
            $user_name = $_POST['user_name'];
            $user_surname = $_POST['user_surname'];
            $user_mail = $_POST['user_mail'];
            $user_level = (int) $_POST['user_level'];
            $user_status = (int) $_POST['user_status'];
            try {
                  $admin_object->EditUser($user_id, $user_name, $user_surname, $user_mail, $user_level, $user_status);
            }
            catch (EditUserException $eae){
                Utls::Redirect(SROOT."admin/users/index.php?edit_error=".$eae->getCode());
            }
            unset(  $_POST['submitted_edit'], 
                    $_POST['user_id'], 
                    $_POST['user_name'], 
                    $_POST['user_surname'], 
                    $_POST['user_mail'],
                    $_POST['user_level'],
                    $_POST['user_status']);
            Utls::Redirect(SROOT."admin/users/index.php?msg=edited");
        }
    }
?>
