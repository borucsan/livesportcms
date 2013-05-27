<?php
define('_DP', 1);
require_once("./include/core.php");
require_once(INCLUDEDIR."LiveCommentary.php");

if(isset($_POST['LiveID'])){
   $LiveID = $_POST['LiveID'];
   isset($_POST['DataType']) ? $type = $_POST['DataType'] : $type = "xml";
   isset($_POST['LastUpdate']) ? $LastUpdate = $_POST['LastUpdate'] : $LastUpdate = 0;
   ob_end_clean();
    while(true){
        try{
            $update = LiveCommentary::CheckToken($LiveID, $LastUpdate);
            $template = LiveCommentary::GetTemplate($LiveID);
            require_once(CATEGORIESDIR.$template.".php");
            $live = new LiveCommentary($LiveID, $template, $LastUpdate);
            switch ($type) {
                case "xml":
                    //flush();
                    echo $live->PrintAsXML($update);
                    exit();
                    break;

                default:
                    break;
            }
            break;
        }
        catch (LiveCommentaryTokenException $lcte){
            usleep(100000);
            clearstatcache();
        }
        catch(TemplateNotFoundException $tnfe){
            exit();
        }
        catch(LiveCommentaryNoUpdateException $lcnue){
            usleep(100000);
            clearstatcache();
        }   
    }
}
else{
?><div class="main_panel">
    <div class ="title_bar">Relacje</div>
    <?php
        if(isset($_GET['id'])){
        $id = $_GET['id'];
        ?>
        <audio id="live_notify" height="0" width="0">
            <source src="<?php echo SOUNDSDIR."live_notify.mp3"; ?>" type="audio/mpeg">
            <source src="<?php echo SOUNDSDIR."live_notify.ogg"; ?>" type="audio/ogg">
            <embed height="0" width="0" src="<?php echo SOUNDSDIR."live_notify.mp3"; ?>">
        </audio>
        <?php
        try{
            $template = LiveCommentary::GetTemplate($id);
            require_once(CATEGORIESDIR.$template.".php");
            isset($_GET['page']) && $_GET['page'] > 0 ? $page = $_GET['page'] : $page = 1;
            $live = new LiveCommentary($id, $template, ($page - 1) * Config::getConfig()->live_messages_limit, Config::getConfig()->live_messages_limit);
            $document->setSubTitle($live->mScore->mHomeName." - ".$live->mScore->mAwayName." - Relacje na żywo");
            if($live->mLive){
                $document->addJS("<script type=\"text/javascript\" src=\"".INCLUDEDIR."JS/ajax.js\"></script>");
                $document->addJS("<script type=\"text/javascript\" src=\"".INCLUDEDIR."JS/live.js\"></script>");
                $document->addJS("<script type=\"text/javascript\">
                                  window.onload = function(){
                                  var upd = new LiveUpdater(".$id.", ".$live->mUpdateTimeStamp.", \"xml\");
                                  upd.start();
                                  }
                                  </script>");
            }
            $live->mScore->ScoreTable();
            echo "<noscript><a href=\"live.php?id=".$id."\" class=\"button1\">Odśwież</a></noscript>";
            $count = count($live->messages);
            echo "<ul id=\"live_msg_area\">";
            for($i = 0; $i < $count; ++$i){
                echo "<li class=\"live_messages\"><span class=\"live_messages_head\">".$live->messages[$i]['Live_Message_Title']."</span><span class=\"live_messages_text\">".nl2br($live->messages[$i]['Live_Message_Text'])."</span></li>\n";
            }
            echo "</ul>";
            
        }
        catch(TemplateNotFoundException $tnfe){
            echo "<div class=\"error_box\">Relacja nie istnieje lub błąd pobierania danych!</div>";
        }
        
    }
    else{
        $document->setSubTitle("Relacje na żywo");
        $db = DB::getDB();
      $db->set_charset("utf8");
        $query = "SELECT Live_Commentary_ID, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, DATE_FORMAT(Score_Event_Datetime ,'%d-%m-%Y, %H:%i') AS DATE, Live_Commentary_Live
                  FROM ".DB_LIVE_COMMENTARY." LCOM
                  JOIN ".DB_USERS." USR ON LCOM.User_ID=USR.User_ID
                  JOIN ".DB_SCORES." SCO ON LCOM.Score_ID=SCO.Score_ID
                  ORDER BY Score_Event_Datetime DESC";
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
                <table class="live_reports_table">
                    <tr>
                        <td>Data wydarzenia</td>
                        <td>Gospodarz</td>
                        <td>Wynik</td>
                        <td>Gość</td>
                        <td>Na żywo</td>
                        <td>Relacja</td>
                    </tr>
                    <?php
                    $row = $result->fetch_object();
                    while($row){
                        echo "<tr>";
                        echo "<td>".$row->DATE."</td>";
                        echo "<td>".$row->Score_Home_Name."</td>";
                        echo "<td>".$row->Score_Home_Score." - ".$row->Score_Away_Score."</td>";
                        echo "<td>".$row->Score_Away_Name."</td>";
                        echo "<td>".($row->Live_Commentary_Live == "1" ? "TAK" : "NIE")."</td>";
                        echo "<td><a href=\"live.php?id=".$row->Live_Commentary_ID."\">Otwórz</a>";
                        echo "</tr>";
                        $row = $result->fetch_object();
                    }
                    ?>
                </table> 
                <?php
            }
        }
    }
    echo "</div>";
    require_once(THEMETEMP."engine.php");
}
?>
