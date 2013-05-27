<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Categories.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_subcategory']) )
        {
            $admin_object = new Admin_Categories();
            
            $subcategory_name = $_POST['subcategory_name'];
            $category_id = (int) $_POST['category_id'];
            
            try {
                $admin_object->AddSubCategory($subcategory_name, $category_id);
            }
            catch (AddSubCategoryException $asce){
                Utls::Redirect(SROOT."admin/categories/index.php?add_subcat_error=".$asce->getCode());
            }
            unset($_POST['submitted_subcategory'], $_POST['subcategory_name'], $_POST['category_id']);
            Utls::Redirect(SROOT."admin/categories/index.php?msg=added_subcat");
        }
    }
?>
