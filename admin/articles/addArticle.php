<?php
    define('_DP', 1);
    require_once("../../include/core.php");
    require_once(INCLUDEDIR."Admin_Articles.php");
    if(ADMIN)
    {
        if( isset($_POST['submitted_article']) )
        {
            $admin_object = new Admin_Articles();
            //$author_id, $category_id, $article_title, $article_text, $article_image, $live_id
            $author_id = (int)$user->mUser_id;
            $subcategory_id = (int)$_POST['subcategory_id'];
            $article_title = $_POST['article_title'];
            $article_brief = $_POST['article_brief'];
            $article_text = $_POST['article_text'];
            $article_image = "";
            $live_id = 0;
            try {
                $admin_object->AddArticle($author_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image, $live_id);
            }
            catch (AddArticleException $aae){
                Utls::Redirect(SROOT."admin/articles/index.php?add_error=".$aae->getCode());
            }
            unset($_POST['submitted_article'], $_POST['subcategory_id'], $_POST['article_title'], 
                    $_POST['article_brief'], $_POST['article_text']);
            Utls::Redirect(SROOT."admin/articles/index.php?msg=added");
        }
    }
?>
