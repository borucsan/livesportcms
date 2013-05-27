<?php
define('_DP', 1);
require_once("./include/core.php");
require_once(INCLUDEDIR."Generate_Articles.php");
echo "<div class='main_panel'>";
 if(isset($_GET['id'])) {
     $article = Generate_Articles::GetArticle($_GET['id']);
     $document->setSubTitle($article['Article_Title']);
     echo "<div class ='title_bar'>(".$article['Categorie_Name'].")".$article['Subcategory_Name']." : ".$article['Article_Title']."</div>";
     echo "<div class='article_content'>".$article['Article_Text']."</div>";
    if(!empty($article['User_Name']) && !empty($article['User_Surname'])) { 
        $author = $article['User_Name']." ".$article['User_Surname'];
    }
    else
    {
        $author = $article['User_Login'];
    }
        
    echo "<div class='article_footer'>".$article['Article_Creation_Date']." Autor: ".$author."</div>";
     
 } else {
     Utls::Redirect(SROOT);   
 }
?>

<?php
echo "</div>";
require_once(THEMETEMP."engine.php");
?>
