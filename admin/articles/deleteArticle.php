<?php
 define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Articles.php");
    if(ADMIN)
    {
        if( isset($_GET['article_id']) )
        {
            $admin_object = new Admin_Articles();
            $article_id = (int)$_GET['article_id'];
            try {
                $admin_object->DeleteArticle($article_id);
                }
            catch (DeleteArticleException $dae){
                Utls::Redirect(SROOT."admin/articles/index.php?delete_error=".$dae->getCode());
            }
            unset($_GET['article_id']);
            Utls::Redirect(SROOT."admin/articles/index.php?msg=deleted");
        }
    }

?>
