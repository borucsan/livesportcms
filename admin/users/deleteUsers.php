<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Users.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_delete']) )
        {
            $admin_object = new Admin_Users();
            try {
                  $admin_object->DeleteUsers();    
            }
            catch (EditUserException $eae){
                Utls::Redirect(SROOT."admin/users/index.php?edit_error=".$eae->getCode());
            }
            unset(  $_POST['submitted_delete'] );
            Utls::Redirect(SROOT."admin/users/index.php?msg=deleted");
        }
    }
?>
