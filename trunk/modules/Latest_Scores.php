<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Latest_Scores
 *
 * @author Takezo1115
 */
include_once ("Module_body.php");
class Latest_Scores implements Module_body{
    private function GenerateScores(){
        $db = DB::getDB();
        $db->set_charset("utf8");
        if(!isset($_GET['subcategory']) && !isset($_GET['category']))
        {
            $query = "SELECT Live_Commentary_ID, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, DATE_FORMAT(Score_Event_Datetime ,'%d-%m-%Y, %H:%i') AS DATE, Live_Commentary_Live
                    FROM ".DB_LIVE_COMMENTARY." LCOM
                    JOIN ".DB_USERS." USR ON LCOM.User_ID=USR.User_ID
                    JOIN ".DB_SCORES." SCO ON LCOM.Score_ID=SCO.Score_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON SCO.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID
                    ORDER BY Score_Event_Datetime DESC LIMIT 10";
            $result = $db->query($query);
        }
        if(isset($_GET['subcategory']))
        {
            $subcategory_id = (int)trim($_GET['subcategory']);
            $query = "SELECT Live_Commentary_ID, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, DATE_FORMAT(Score_Event_Datetime ,'%d-%m-%Y, %H:%i') AS DATE, Live_Commentary_Live
                    FROM ".DB_LIVE_COMMENTARY." LCOM
                    JOIN ".DB_USERS." USR ON LCOM.User_ID=USR.User_ID
                    JOIN ".DB_SCORES." SCO ON LCOM.Score_ID=SCO.Score_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON SCO.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID
                    WHERE SCO.Subcategory_ID = ".$subcategory_id."
                    ORDER BY Score_Event_Datetime DESC LIMIT 10";
            $result = $db->query($query);
        }
        if(!isset($_GET['subcategory']) && isset($_GET['category']))
        {
            $category_id = (int)trim($_GET['category']);
            
            $query = "SELECT Live_Commentary_ID, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, DATE_FORMAT(Score_Event_Datetime ,'%d-%m-%Y, %H:%i') AS DATE, Live_Commentary_Live
                    FROM ".DB_LIVE_COMMENTARY." LCOM
                    JOIN ".DB_USERS." USR ON LCOM.User_ID=USR.User_ID
                    JOIN ".DB_SCORES." SCO ON LCOM.Score_ID=SCO.Score_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON SCO.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID
                    WHERE SUB.Category_ID = ".$category_id."
                    ORDER BY Score_Event_Datetime DESC LIMIT 10";
            $result = $db->query($query);
        }
        if($result === FALSE){
            echo "Błąd pobierania danych";
        }
        else{
            $count = $result->num_rows;
            if($count == 0){
                echo "Brak relacji";
            }
            else{
                ?>
                <table class="live_reports_table">
                    
                    <?php
                    $row = $result->fetch_object();
                    while($row){
                        echo "<table class='admin_users_table'>";
                        echo "<thead><th colspan='2'>".$row->DATE."</th></thead>";
                        echo "<tr>";
                        echo "<td>".$row->Score_Home_Name."</td>";
                        echo "<td>".$row->Score_Away_Name."</td>";
                        echo "</tr>";
                        echo "<tr><td colspan='2'>".$row->Score_Home_Score." - ".$row->Score_Away_Score."</td></tr>";
                        
                        echo "<tr><td colspan='2'><a href=\"live.php?id=".$row->Live_Commentary_ID."\">Otwórz Relacje</a></td></tr>";
                        echo "</table>";
                        $row = $result->fetch_object();
                    }
                    echo "<tr><td colspan='2'><a href=\"live.php\">Otwórz wszystkie wyniki</a></td></tr>";
                    ?>
                    
                </table> 
                <?php
            }
        }
    }
    public function ModuleBody(){
        return $this->GenerateScores();
    }
}

?>
