<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Modules.php");
$document->setSubTitle("Moduły - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Moduły</div>
<?php if(ADMIN){
    if (isset($_GET['add_error'])) {     
        switch($_GET['add_error'])
        {
            case 1: echo "Nazwa nie może przekraczać 25 znaków";
                break;
            case 2: echo "Podana nazwa już istnieje";
                break;
            case 3: echo "Klasa nie może przekraczać 25 znaków";
                break;
        }
    }
    if (isset($_GET['delete_error'])) {
        switch($_GET['delete_error'])
        {
            case 1: echo "Moduł nie znaleziony w bazie";
                break;
            case 2: echo "Moduł nie został usunięty";
                break;
        }
    }
    if ( isset($_GET['msg'])) {
        if($_GET['msg'] == "added"){
            echo "Dodałeś nowy moduł<br/>";
        }
        if($_GET['msg'] == "deleted"){
            echo "Usunąłeś moduł<br/>";
        }
    }
    $admin_modules = new Admin_Modules();
    $modules = $admin_modules->GetMenuModules();
?>
    <form method="GET">
        <select name="module_id">
        <?php
        foreach($modules as $module){

            echo "<option value='".$module['Modules_ID']."'>".$module['Modules_name']."</option>";
        }
        ?>
        </select>
        <input type="submit" name="submit" formaction="deleteModule.php" value="Usuń" />
        <input type="submit" name="submit" formaction="index.php" value="Dodaj" />
    </form>
    
        <?php
    if(isset($_GET['submit']))
    {
        if($_GET['submit'] == "Dodaj"){
            $dir = "".SROOT."modules/";
            $all_modules = $admin_modules->getDirectoryList($dir);
            
        ?>
            <label>Dodawanie Modułów</label>
            <form class ="main_form" action="addModule.php" method="POST">
                <label for="module_name">Nazwa Modułu:</label>
                <input type="text" name="module_name" id="module_name"><br>
                <label for="module_class">Wybierz Moduł:</label>
                <select name = "module_class">
                    <?php
                    foreach($all_modules as $my_module)
                    {
                        echo "<option value='".$my_module."'>".$my_module."</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="submit" value="Dodaj Moduł" />
                <input type="hidden" name="submitted_module" value="TRUE" />
            </form>
            <br/>
        <?php }
    }?>
</div>

<?php }else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
