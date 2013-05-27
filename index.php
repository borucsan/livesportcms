<?php
define('_DP', 1);
require_once("./include/core.php");
require_once(INCLUDEDIR."Generate_Articles.php");

if(!isset($_GET['subcategory']) && !isset($_GET['category']))
{
    $articles = Generate_Articles::GetArticles(0, 0);
}
if(isset($_GET['subcategory']))
{
    $subcategory_id = (int)trim($_GET['subcategory']);
    $articles = Generate_Articles::GetArticles($subcategory_id, 0);
}
if(!isset($_GET['subcategory']) && isset($_GET['category']))
{
    $category_id = (int)trim($_GET['category']);
    $articles = Generate_Articles::GetArticles(0, $category_id);
}
foreach($articles as $article){
    echo "<div class='main_panel'>";
    echo "<div class ='title_bar'>(".$article['Categorie_Name'].")".$article['Subcategory_Name']." : ".$article['Article_Title']."</div>";
    echo "<div class='article_content'>".$article['Article_Brief']."</div>";
    if(!empty($article['User_Name']) && !empty($article['User_Surname'])) { 
        $author = $article['User_Name']." ".$article['User_Surname'];
    }
    else
    {
        $author = $article['User_Login'];
    }
        
    echo "<div class='article_footer'>".$article['Article_Creation_Date']." Autor: ".$author." <a href = ' ".SROOT."read_article.php?id=".$article['Article_ID']." '>Rozwiń Artykuł</a></div>";
    echo "</div>";
}   
?>
<div class ="main_panel">
    <div class ="title_bar">Dodaj Artykuł Do Bazy, aby wyświetlić</div>
</div>

<?php
require_once(THEMETEMP."engine.php");
?>