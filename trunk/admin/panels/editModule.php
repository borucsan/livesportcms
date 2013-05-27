<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Panels.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_edit']) )
        {
            $admin_object = new Admin_Panels();
            $module_id = (int) $_POST['module_id'];
            $order = (int) $_POST['order'];
            $status = (int) $_POST['status'];
            try {
                $admin_object->EditModule($module_id, $order, $status);
            }
            catch (EditModuleException $eme){
                Utls::Redirect(SROOT."admin/panels/index.php?edit_error=".$eme->getCode());
            }
            unset(  $_POST['submitted_edit'], 
                    $_POST['module_id'], 
                    $_POST['order'],
                    $_POST['status']   );
            Utls::Redirect(SROOT."admin/panels/index.php?msg=edited");
        }
    }
?>
