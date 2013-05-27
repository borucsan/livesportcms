<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Categories.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_edit']) )
        {
            $admin_object = new Admin_Categories();
            $category_id = (int)$_POST['category_id'];
            $category_name = $_POST['category_name'];
            $category_class = $_POST['category_class'];
            try {
                $admin_object->EditCategory($category_id, $category_name, $category_class);
            }
            catch (EditCategoryException $ece){
                Utls::Redirect(SROOT."admin/categories/index.php?edit_category_error=".$ece->getCode());
            }
            unset(  $_POST['submitted_edit'], 
                    $_POST['category_id'], 
                    $_POST['category_name'],
                    $_POST['category_class']   );
            Utls::Redirect(SROOT."admin/categories/index.php?msg=edited_cat");
        }
    }
?>
