<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Categories.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_category']) )
        {
            $admin_object = new Admin_Categories();
            
            $category_name = $_POST['category_name'];
            $category_class = $_POST['category_class'];
            
            try {
                $admin_object->AddCategory($category_name, $category_class);
            }
            catch (AddCategoryException $ace){
                Utls::Redirect(SROOT."admin/categories/index.php?add_cat_error=".$ace->getCode());
            }
            unset($_POST['submitted_category'], $_POST['category_name'], $_POST['category_class']);
            Utls::Redirect(SROOT."admin/categories/index.php?msg=added_cat");
        }
    }
?>
