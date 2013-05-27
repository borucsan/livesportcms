<?php
 define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Modules.php");
    if(ADMIN)
    {
        if( isset($_GET['module_id']) )
        {
            $admin_object = new Admin_Modules();
            $module_id = (int)$_GET['module_id'];
            try {
                    $admin_object->DeleteModule($module_id);
                }
            catch (DeleteModuleException $dme){
                Utls::Redirect(SROOT."admin/modules/index.php?delete_error=".$dme->getCode());
            }
            unset($_GET['module_id']);
            Utls::Redirect(SROOT."admin/modules/index.php?msg=deleted");
        }
    }

?>
