<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."LiveCommentary.php");
$document->setSubTitle("Relacje na żywo - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Relacje</div>
<?php if(ADMIN){ 
      ?>  <a class="button1" href="add.php">Dodaj</a><br /><?php
      $db = DB::getDB();
      $db->set_charset("utf8");
        $query = "SELECT Live_Commentary_ID, User_Login, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, DATE_FORMAT(Score_Event_Datetime ,'%d-%m-%Y, %H:%i') AS DATE, Live_Commentary_Live
                  FROM ".DB_LIVE_COMMENTARY." LCOM
                  JOIN ".DB_USERS." USR ON LCOM.User_ID=USR.User_ID
                  JOIN ".DB_SCORES." SCO ON LCOM.Score_ID=SCO.Score_ID";
        $result = $db->query($query);
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
                <table class="admin_data_table">
                    <tr>
                        <td>ID</td>
                        <td>Data wydarzenia</td>
                        <td>Gospodarz</td>
                        <td>Wynik</td>
                        <td>Gość</td>
                        <td>User</td>
                        <td>Na żywo</td>
                        <td>#</td>
                        <td>#</td>
                    </tr>
                    <?php
                    $row = $result->fetch_object();
                    while($row){
                        echo "<tr>";
                        echo "<td>".$row->Live_Commentary_ID."</td>";
                        echo "<td>".$row->DATE."</td>";
                        echo "<td>".$row->Score_Home_Name."</td>";
                        echo "<td>".$row->Score_Home_Score." - ".$row->Score_Away_Score."</td>";
                        echo "<td>".$row->Score_Away_Name."</td>";
                        echo "<td>".$row->User_Login."</td>";
                        echo "<td>".($row->Live_Commentary_Live == "1" ? "TAK" : "NIE")."</td>";
                        echo "<td><a href=\"report.php?id=".$row->Live_Commentary_ID."&live=true\">Relacjnouj</a></td>";
                        echo "<td>Usun</td>";
                        echo "</tr>";
                        $row = $result->fetch_object();
                    }
                    ?>
                </table> 
                <?php
            }
        }
?>
</div>
<?php }
else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
