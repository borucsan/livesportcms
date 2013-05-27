<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Modules.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_module']) )
        {
            $admin_object = new Admin_Modules();
            
            $module_name = $_POST['module_name'];
            $module_class = $_POST['module_class'];
            
            try {
                $admin_object->AddModule($module_name, $module_class);
            }
            catch (AddModuleException $ame){
                Utls::Redirect(SROOT."admin/modules/index.php?add_error=".$ame->getCode());
            }
            unset($_POST['submitted_module'], $_POST['module_name'], $_POST['module_class']);
            Utls::Redirect(SROOT."admin/modules/index.php?msg=added");
        }
    }
?>
