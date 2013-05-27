<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Categories.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_edit']) )
        {
            $admin_object = new Admin_Categories();
            $subcategory_id = (int)$_POST['subcategory_id'];
            $subcategory_name = $_POST['subcategory_name'];
            $category_id = $_POST['category_id'];
            try {
                $admin_object->EditSubCategory($subcategory_id, $subcategory_name, $category_id);
            }
            catch (EditSubCategoryException $esce){
                Utls::Redirect(SROOT."admin/categories/index.php?edit_subcat_error=".$esce->getCode());
            }
            unset(  $_POST['submitted_edit'], 
                    $_POST['subcategory_id'], 
                    $_POST['subcategory_name'],
                    $_POST['category_id']   );
            Utls::Redirect(SROOT."admin/categories/index.php?msg=edited_subcat");
        }
    }
?>
