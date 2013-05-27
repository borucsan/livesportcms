<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Articles.php");
require_once(INCLUDEDIR."Admin_Categories.php");
$document->setSubTitle("Artykuły - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Artykuły</div>
<?php if(ADMIN){ 
    //
    if (isset($_GET['add_error'])) {     
        switch($_GET['add_error'])
        {
            case 1: echo "ID Autora nie jest liczbą";
                break;
            case 2: echo "Podany autor nie znaleziony w bazie";
                break;
            case 3: echo "ID subkategorii nie jest liczbą";
                break;
            case 4: echo "Podana subkategoria nie znaleziona w bazie";
                break;
            case 5: echo "ID Relacji nie jest liczbą";
                break;
            case 6: echo "Podana relacja nie znaleziona w bazie";
                break;
            case 7: echo "Tytuł może mieć maksymalnie 48 znaków";
                break;
            case 8: echo "Tytuł już istnieje";
                break;
            case 9: echo "Streszczenie powinno mieć max 512 znaków";
                break;
        }
    }
    if (isset($_GET['delete_error'])) {
        switch($_GET['delete_error'])
        {
            case 1: echo "Artykuł nie znaleziony w bazie";
                break;
            case 2: echo "Artykuł nie został usunięty";
                break;
        }
    }
    if (isset($_GET['edit_error'])) {
        switch($_GET['edit_error'])
        {
            case 1: echo "ID Artykułu nie jest liczbą";
                break;
            case 2: echo "Artykuł nie znaleziony w bazie";
                break;
            case 3: echo "ID subkategorii nie jest liczbą";
                break;
            case 4: echo "Subkategoria nie znaleziona w bazie";
                break;
            case 5: echo "Tytuł może mieć maksymalnie 48 znaków";
                break;
            case 6: echo "Tytuł już istnieje";
                break;
            case 7: echo "Artykuł nie został zmieniony";
                break;
            case 8: echo "Streszczenie powinno mieć max 512 znaków";
                break;
            
        }
    }
    if ( isset($_GET['msg'])) {
        if($_GET['msg'] == "added"){
            echo "Dodałeś nowy artykuł<br/>";
        }
        if($_GET['msg'] == "deleted"){
            echo "Usunąłeś artykuł<br/>";
        }
        if($_GET['msg'] == "edited"){
            echo "Edytowanie artykułu zakończone powodzeniem<br/>";
        }
    }
    $admin_articles = new Admin_Articles();
    $admin_categories = new Admin_Categories();
    $articles = $admin_articles->GetMenuArticles();
    $categories = $admin_categories->GetMenuCategories();
    $subcategories = $admin_categories->GetMenuSubCategories();
    
?>
    <form method="GET">
        <select name="article_id">
        <?php
        foreach($articles as $arti){

            echo "<option value='".$arti['Article_ID']."'>".$arti['Article_Title']."</option>";
        }
        ?>
        </select>
        <input type="submit" name="submit" formaction="index.php" value="Edytuj" />
        <input type="submit" name="submit" formaction="deleteArticle.php" value="Usuń" />
        <input type="submit" name="submit" formaction="index.php" value="Dodaj" />
    </form>
    
    <br/>
<?php
    if(isset($_GET['submit']))
    {
        if($_GET['submit'] == "Dodaj"){
        ?>
            <label>Dodawanie Artykułów</label>
            <form action="addArticle.php" method="POST">
                <br/>
                <label for="subcat">Wybierz Subkategorie</label>
                <select id="subcat" name="subcategory_id">
                    <?php
                    foreach($subcategories as $subcate){
                        
                        echo "<option value='".$subcate['Subcategory_ID']."'>(".$subcate['Categorie_Name'].") ".$subcate['Subcategory_Name']."</option>";
                    }
                    ?>
                </select>
                
                <br/><label for="title">Tytuł</label>
                <input id="title" type="text" name="article_title" size="30" maxlength="48" value=""/>
                <br/><label for ="article_brief">Streszczenie(MAX 512 znaków)</label><br/>
                <textarea id="article_brief" name="article_brief" rows="10" cols="50"></textarea>
                <br/><label for ="article">Artykuł</label><br/>
                <textarea id="article" name="article_text" rows="10" cols="50"></textarea>
                <br/>
                <input type="submit" name="submit" value="Dodaj Artykuł" />
                <input type="hidden" name="submitted_article" value="TRUE" />
            </form>
            <br/>
            
        <?php
        }
        if($_GET['submit'] == "Edytuj")
        {
            if(isset($_GET['article_id']))
            {
                $edit_id = $_GET['article_id'];
                $edit_article = $admin_articles->GetArticle($edit_id);
              
            ?>
                <label>Edytowanie Artykułów</label> 
                <form action="editArticle.php" method="POST">
                    
                    <br/><label>Wybierz Subkategorie</label>
                    <select name="subcategory_id">
                        <?php
                        foreach($subcategories as $cate){
                            if($cate['Subcategory_ID'] == $edit_article['Subcategory_ID'])
                            {
                                echo "<option value='".$cate['Subcategory_ID']."' selected>(".$cate['Categorie_Name'].") ".$cate['Subcategory_Name']."</option>";
                            }
                            else
                            {
                                echo "<option value='".$cate['Subcategory_ID']."'>(".$cate['Categorie_Name'].") ".$cate['Subcategory_Name']."</option>";
                            }
                        }
                        ?>
                    </select>
                    <br/><label>Tytuł</label>
                    <input type="text" name="article_title" size="30" maxlength="48" value="<?php echo "".$edit_article['Article_Title'].""; ?>"/>
                    <br/><label>Streszczenie(MAX 512 znaków)</label><br/>
                    <textarea name="article_brief" rows="10" cols="50"><?php echo "".trim($edit_article['Article_Brief']).""; ?></textarea>
                    <br/>
                    <br/><label>Artykuł</label><br/>
                    <textarea name="article_text" rows="10" cols="50"><?php echo "".trim($edit_article['Article_Text']).""; ?></textarea>
                    <br/>
                    
                    <input type="hidden" name="article_id" value="<?php echo "".$edit_article['Article_ID'].""; ?>" />
                    <input type="submit" name="submit" value="Zapisz Artykuł" />
                    <input type="hidden" name="submitted_edit" value="TRUE" />
                    
                </form>
                
                <br/>
                
            <?php
            }
        }
    }
?>
    
    
</div>
<?php }else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
