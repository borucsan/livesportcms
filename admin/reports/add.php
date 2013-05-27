<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."LiveCommentary.php");
$document->setSubTitle("Dodaj relację - Administracja");
if(ADMIN){
    echo "<div class=\"main_panel\">";
    echo "<div class =\"title_bar\">Administracja - Relacje</div>";
    if(isset($_POST['step'])){
        switch ($_POST['step']) {
            case 2:
                $category = $_POST['cid'];
                $db = DB::getDB();
                $db->set_charset("utf8");
                $query = "SELECT Subcategory_ID, Subcategory_Name
                          FROM ".DB_SUBCATEGORIES." WHERE Categorie_ID=".$category;
                $result = $db->query($query);
                if($result == FALSE){
                    echo "Błąd pobierania danych";
                }
                else{
                    $count = $result->num_rows;
                ?>
                <form class="admin_form_add" action="add.php" method="POST">
                    <fieldset>
                        <input type="submit" value="Dodaj" />
                        <input type="reset" value="Wyczyść" />
                    </fieldset>
                    <fieldset>
                        <legend>Dodaj relację:</legend>
                        <label><input type="radio" name="sub_type" value="0" <?php echo ($count > 0 ? "checked=\"checked\"" : "disabled"); ?> />Wybierz podkategorię</label><br />
                        <select id="add_subcat" name="subcategory" >
                        <?php
                        if($count > 0){
                            $row = $result->fetch_object();
                            while ($row) {
                                       echo "<option value=\"".$row->Subcategory_ID."\">".$row->Subcategory_Name."</option>";
                                       $row = $result->fetch_object();
                                    }
                                    $result->free();
                            
                        }
                        else{
                            echo "<option value=\"0\">Brak</option>";
                        }
                        ?>
                        </select><br />
                        <label><input type="radio" name="sub_type" value="1" <?php echo ($count < 1 ? "checked=\"checked\"" : "");?> />Utwórz nową podkategorię</label><br />
                        <input id="add_new_sub" type="text" name="sub_name" />
                    </fieldset>
                    <fieldset>
                        <label for="add_home_name">Gospodarz spotkania</label><input id="add_home_name" type="text" name="home_name" /><br />
                        <label for="add_home_away">Gość spotkania</label><input id="add_home_away" type="text" name="away_name" /><br />
                        <label for="add_event_date">Data</label><input id="add_event_date" type="text" name="event_date" value="<?php echo Date("Y/m/d") ?>" placeholder="RRRR/MM/DD" maxlength="10" /><br />
                        <label for="add_event_time">Godzina</label><input id="add_event_time" type="text" maxlength="5" name="event_time" value="<?php echo Date("G:i") ?>" placeholder="GG:MM" /><br />
                        <input type="hidden" name="cid" value="<?php echo $category ?>" />
                        <input type="hidden" name="step" value="3" />
                    </fieldset>
                </form> 
                <?php
           }
                break;
            case 3:
                if(isset($_POST['event_date']) && isset($_POST['event_time']) && isset($_POST['home_name']) && isset($_POST['home_name'])){
                    try{
                        if($_POST['sub_type'] == "1"){
                            $id = LiveCommentary::CreateNew($_POST['home_name'], $_POST['away_name'], $_POST['event_date'], $_POST['event_time'], $_POST['subcategory'], $user->mUser_id, true, $_POST['sub_name'], $_POST['cid']);
                        }
                        else{
                            $id = LiveCommentary::CreateNew($_POST['home_name'], $_POST['away_name'], $_POST['event_date'], $_POST['event_time'], $_POST['subcategory'], $user->mUser_id);
                        }
                        ?>
                        <span>Dodano relację</span><br />
                        <a href="report.php?id=<?php echo $id ?>&live=true">Przejdz do relacji</a><br />
                        <a href="index.php">Wróć</a>
                        <?php
                    }
                    catch (Exception $e){
                        echo $e->getMessage();
                    }
                }
                else{
                    Utls::Redirect("add.php");
                }
                break;
            default:
                echo "Błędny krok";
                break;
        }
    }
    else {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $query = "SELECT Categorie_ID, Categorie_Name
                  FROM ".DB_CATEGORIES;
        $result = $db->query($query);
        if($result == FALSE){
            echo "Błąd pobierania danych";
        }
        else if($result->num_rows < 1){
            echo "<span>Brak kategorii.<br />Przed dodaniem relacji musisz utworzyć kategorię - <a href=\"".ADMINDIR."categories/index.php\">tutaj</a>.</span>";
        }
        else{
            ?>
            <form action="add.php" class="admin_form_add" method="POST">
                <input type="submit" value="Dalej" />
                <input type="reset" value="Wyczyść" />
                <fieldset>
                <legend>Wybierz kategorię(Sport) dla relacji</legend>
                <input type="hidden" name="step" value="2" />
                <select name="cid">
                <?php
                    $row = $result->fetch_object();
                    while ($row) {
                        echo "<option value=\"".$row->Categorie_ID."\">".$row->Categorie_Name."</option>";
                        $row = $result->fetch_object();
                    }
                    $result->free();
                    ?>
                </select>
                </fieldset>
            </form>
                <?php
        }
    }
echo "</div>";
}
else{
    Utls::Redirect(SROOT."index.php");
}
require_once(THEMETEMP."engine.php");
?>