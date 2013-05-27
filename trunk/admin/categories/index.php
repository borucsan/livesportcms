<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Categories.php");
$document->setSubTitle("Kategorie - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Kategorie</div>
<?php if(ADMIN){ 
    if (isset($_GET['add_cat_error'])) {     
        switch($_GET['add_cat_error'])
        {
            case 1: echo "Nazwa Kategorii MAX 25 znaków";
                break;
            case 2: echo "Podana nazwa Kategorii jest już zajęta";
                break;
            case 3: echo "Nazwa szablonu MAX 25 znaków";
                break;
        }
    }
    if (isset($_GET['add_subcat_error'])) {     
        switch($_GET['add_subcat_error'])
        {
            case 1: echo "Nazwa SubKategorii MAX 25 znaków";
                break;
            case 2: echo "Podana nazwa SubKategorii jest już zajęta";
                break;
            case 3: echo "Podana kategoria nie istnieje";
                break;
        }
    }
    if (isset($_GET['edit_category_error'])) {     
        switch($_GET['edit_category_error'])
        {
            case 1: echo "ID Kategorii nie jest liczbą";
                break;
            case 2: echo "Podana kategoria nie istnieje";
                break;
            case 3: echo "Nazwa kategorii MAX 25 znaków";
                break;
            case 4: echo "Podana nazwa kategorii jest już zajęta";
                break;
            case 5: echo "Błąd zapytania bazy danych";
                break;
            case 6: echo "Nazwa szablonu MAX 25 znaków";
                break;
        }
    }
    if (isset($_GET['edit_subcat_error'])) {     
        switch($_GET['edit_subcat_error'])
        {
            case 1: echo "ID subkategorii nie jest liczbą";
                break;
            case 2: echo "Podana subkategoria nie istnieje";
                break;
            case 3: echo "Nazwa subkategorii MAX 25 znaków";
                break;
            case 4: echo "Podana nazwa subkategorii jest już zajęta";
                break;
            case 5: echo "Błąd zapytania bazy danych";
                break;
            case 6: echo "ID kategorii nie jest liczbą";
                break;
            case 7: echo "Podana kategoria nie istnieje";
                break;
        }
    }
    if ( isset($_GET['msg'])) {
        if($_GET['msg'] == "added_cat"){
            echo "Dodałeś nową kategorie<br/>";
        }
        if($_GET['msg'] == "added_subcat"){
            echo "Dodałeś nową subkategorie<br/>";
        }
        if($_GET['msg'] == "edited_cat"){
            echo "Zmodyfikowałeś kategorie<br/>";
        }
        if($_GET['msg'] == "edited_subcat"){
            echo "Zmodyfikowałeś subkategorie<br/>";
        }
    }
    ?>
    
    <?php
     $admin_categories = new Admin_Categories();
     $categories = $admin_categories->GetMenuCategories();
     $subcategories = $admin_categories->GetMenuSubCategories();
     $dir = SROOT."categories/";
     $templates = $admin_categories->getDirectoryList($dir);
    ?>
    <form method="GET">
        <label>Nazwa : Szablon</label><br/>
        <select name="cat_id">
        <?php
        foreach($categories as $category){

            echo "<option value='".$category['Categorie_ID']."'>
                 ".$category['Categorie_Name'].":".$category['Categorie_Live_Template']."</option>";
        }
        ?>
        </select>
        <input type="submit" name="submit" formaction="index.php" value="Dodaj Kategorie" />
        <input type="submit" name="submit" formaction="index.php" value="Edytuj Kategorie" />
        <input type="submit" name="submit" formaction="index.php" value="Dodaj Subkategorie" />
    </form>
    
    <form method="GET">
        <label>Kategoria : Subkategoria</label><br/>
        <select name="subcat_id">
        <?php
        foreach($subcategories as $subcategory){

            echo "<option value='".$subcategory['Subcategory_ID']."'>
                 ".$subcategory['Categorie_Name']." : ".$subcategory['Subcategory_Name']."</option>";
        }
        ?>
        </select>
        <input type="submit" name="submit" formaction="index.php" value="Edytuj Subkategorie" />
    </form>
    
    <br/>
    <?php
    if(isset($_GET['submit']))
    {
        if($_GET['submit'] == "Dodaj Kategorie"){
            ?>
            <label>Dodawanie Kategorii</label>
            <form action="addCategory.php" method="POST">
                <br/><label for="template">Szablon</label>
                <select id="template" name="category_class">
                    <?php
                    foreach($templates as $template){
                        
                        echo "<option value='".$template."'>".$template."</option>";
                    }
                    ?>
                </select>
                <br/><label for="category_name">Nazwa Kategorii</label>
                <input id="category_name" type="text" name="category_name" size="30" maxlength="25" value=""/>
                <br/>
                <input type="submit" name="submit" value="Dodaj Kategorie" />
                <input type="hidden" name="submitted_category" value="TRUE" />
            </form>
            <br/>
            
        <?php
        }
        if($_GET['submit'] == "Edytuj Kategorie"){
            
            if(isset($_GET['cat_id']))
            {
                $edit_id = $_GET['cat_id'];
                $edit_category = $admin_categories->GetCategory($edit_id);              
            ?>
                <div><label>Edytowanie Kategorii</label></div> 
                <form action="editCategory.php" method="POST">
                    <div class="forms">
                    <br/><label>Szablon</label>
                    <select name="category_class">
                        <?php
                        foreach($templates as $temp){
                            if($temp == $edit_category['Categorie_Live_Template'])
                            {
                                echo "<option value='".$temp."' selected>".$temp."</option>";
                            }
                            else
                            {
                                echo "<option value='".$temp."'>".$temp."</option>";
                            }
                        }
                        ?>
                    </select>
                    <br/><label>Nazwa Kategorii</label>
                    <input type="text" name="category_name" size="30" maxlength="25" value="<?php echo "".$edit_category['Categorie_Name'].""; ?>"/>
                    <br/>
                    </div>
                    <div>
                    <input type="hidden" name="category_id" value="<?php echo "".$edit_category['Categorie_ID'].""; ?>" />
                    <input type="submit" name="submit" value="Zapisz Kategorie" />
                    <input type="hidden" name="submitted_edit" value="TRUE" />
                    </div>
                </form>
                <br/>
            <?php
            }
        }
        if($_GET['submit'] == "Dodaj Subkategorie"){
            ?>
            <label>Dodawanie Kategorii</label>
            <form action="addSubCategory.php" method="POST">
                <br/><label for="category_id">Kategoria</label>
                <select id="category_id" name="category_id">
                    <?php
                    foreach($categories as $category){
                        
                        echo "<option value='".$category['Categorie_ID']."'>".$category['Categorie_Name']."</option>";
                    }
                    ?>
                </select>
                <br/><label for="subcategory_name">Nazwa Subkategorii</label>
                <input id="subcategory_name" type="text" name="subcategory_name" size="30" maxlength="25" value=""/>
                <br/>
                <input type="submit" name="submit" value="Dodaj Subkategorie" />
                <input type="hidden" name="submitted_subcategory" value="TRUE" />
            </form>
            <br/>
            
        <?php
        }
        if($_GET['submit'] == "Edytuj Subkategorie"){
            
            if(isset($_GET['subcat_id']))
            {
                $edit_sub_id = $_GET['subcat_id'];
                $edit_subcategory = $admin_categories->GetSubcategory($edit_sub_id);              
            ?>
                <div><label>Edytowanie Subkategorii</label></div> 
                <form action="editSubCategory.php" method="POST">
                    <div class="forms">
                    <br/><label>Kategoria</label>
                    <select name="category_id">
                        <?php
                        foreach($categories as $cat){
                            if($cat['Categorie_ID'] == $edit_subcategory['Categorie_ID'])
                            {
                                echo "<option value='".$cat['Categorie_ID']."' selected>".$cat['Categorie_Name']."</option>";
                            }
                            else
                            {
                                echo "<option value='".$cat['Categorie_ID']."'>".$cat['Categorie_Name']."</option>";
                            }
                        }
                        ?>
                    </select>
                    <br/><label>Nazwa Subkategorii</label>
                    <input type="text" name="subcategory_name" size="30" maxlength="25" value="<?php echo "".$edit_subcategory['Subcategory_Name'].""; ?>"/>
                    <br/>
                    </div>
                    <div>
                    <input type="hidden" name="subcategory_id" value="<?php echo "".$edit_subcategory['Subcategory_ID'].""; ?>" />
                    <input type="submit" name="submit" value="Zapisz Subkategorie" />
                    <input type="hidden" name="submitted_edit" value="TRUE" />
                    </div>
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
