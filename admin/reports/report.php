<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."LiveCommentary.php");
$document->setSubTitle("Relacja - Administracja");
if(ADMIN){
    echo "<div class=\"main_panel\">";
    echo "<div class =\"title_bar\">Administracja - Relacje</div>";
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $needUpdate = false;
        if(isset($_GET['delsscid'])){
            LiveCommentary::RemoveSubscore($_GET['delsscid']);
            $needUpdate = true;
        }
        if(isset($_GET['live'])){
            LiveCommentary::SetLiveUpdates($id, $_GET['live'] == "true" ? true : false);
        }
        if(isset($_POST['add_sub']) && isset($_POST['add_sub_count'])){
            try{
                $added =  LiveCommentary::AddSubscore($id, $_POST['add_sub_count']);
                $needUpdate = true;
                echo "<div class=\"operation_success_box\">Dodano ".$added." nowych podwyników</div>";
            }
            catch (Exception $exc) {
                echo "<div class=\"error_box\">Błąd podczas dodawania podwyniku:<br />".$exc->getTraceAsString()."</div>";
            }
        }
        try{
            $template = LiveCommentary::GetTemplate($id);
            require_once(CATEGORIESDIR.$template.".php");
            $live = new LiveCommentary($id, $template);
        }
        catch(TemplateNotFoundException $tnfe){
            echo "<div class=\"error_box\">Relacja nie istnieje lub błąd pobierania danych!</div>";
        }
    }
    if(isset($live)){
        if((isset($_POST['send_score']) || isset($_POST['send_all'])) && isset($_POST['main_home']) && isset($_POST['main_away']) && isset($_POST['home_subs']) && isset($_POST['away_subs'])){
            try {
                $live->mScore->Update($_POST['main_home'], $_POST['main_away'], $_POST['home_subs'], $_POST['away_subs']);
                $needUpdate = true;
                echo "<div class=\"operation_success_box\">Wynik uaktualniono pomyślnie!</div>";
            }
            catch (Exception $exc) {
                echo "<div class=\"error_box\">Błąd podczas uaktualniania wyniku:<br />".$exc->getTraceAsString()."</div>";
            }
        }
        if((isset($_POST['send_message']) || isset($_POST['send_all'])) && isset($_POST['message_title']) && isset($_POST['message_text'])){
            try {
                $live->UpdateMessages($_POST['message_title'], $_POST['message_text']);
                $needUpdate = true;
                echo "<div class=\"operation_success_box\">Wysłano wiadomość!</div>";
            } 
            catch (Exception $exc) {
                echo "<div class=\"error_box\">Błąd podczas dodawania wiadomości:<br />".$exc->getMessage()."</div>";
            }
        }
        if($needUpdate === true){
            $live->UpdateToken();
        }
        ?>
            <form class="report_score_form" action="report.php?id=<?php echo $id; ?>" method="POST">
                <input type="submit" name="send_score" value="Uaktualnij tabelę" />
                <input type="submit" name="send_message" value="Wyślij wiadomość" />
                <input type="submit" name="send_all" value="Wyślij wiadomość i uaktualnij tabelę" />
                <input type="reset" value="Przywróć" /><br />
                <?php
                    if($live->mLive){
                        echo "<a class=\"button1\" href=\"report.php?id=".$id."&live=false\">Wyłącz autoaktualizację relacji</a>";
                    }
                    else{
                        echo "<a class=\"button1\" href=\"report.php?id=".$id."&live=true\">Włącz autoaktualizację relacji</a>";
                    }
                ?>
                <fieldset>
                    <span class="report_score_form_tn"><?php echo $live->mScore->mHomeName; ?></span>
                    <input type="number" name="main_home" class="report_score_form_ms" value="<?php echo $live->mScore->mHomeScore; ?>" />
                    <input type="number" name="main_away" class="report_score_form_ms" value="<?php echo $live->mScore->mAwayScore; ?>" />
                    <span class="report_score_form_tn"><?php echo $live->mScore->mAwayName; ?></span><br />
                    <?php
                    $count = count($live->mScore->mSubscores);
                                for($i = 0; $i < $count; ++$i) {
                                    ?>
                                    <input type="number" name="home_subs[<?php echo $i; ?>]" class="report_score_form_subs" value="<?php echo $live->mScore->mSubscores[$i][1]; ?>" />
                                    <span class="report_score_form_subname"><?php echo $live->mScore->getSubscoreName($i + 1); ?></span>
                                    <input type="number" name="away_subs[<?php echo $i; ?>]" class="report_score_form_subs" value="<?php echo $live->mScore->mSubscores[$i][2]; ?>" />
                                    <?php  if($count > 1)echo "<a href=\"report.php?id=".$id."&delsscid=".$live->mScore->mSubscores[$i][0]."\">Usuń</a>"; ?>
                                    <br />
                                    <?php
                                }
                    ?>
                                    <label><input type="checkbox" name="add_sub" />Dodaj</label>
                                    <select name="add_sub_count" class="report_score_add_count" >
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    Podwyników.
                </fieldset>
            <br />
            <fieldset>
                    <label for="report_message_form_title">Tytuł:</label><input id="report_message_form_title" type="text" name="message_title" <?php if(isset($_POST['message_title'])) echo "value=\"".$_POST['message_title']."\""; ?> maxlenght="64" /><br />
                    <textarea rows="5" cols="60" name="message_text"></textarea>
                </fieldset>
            </form>
            <?php
            $count = count($live->messages);
            echo "<ul id=\"live_msg_area\">";
            for($i = 0; $i < $count; ++$i){
                echo "<li class=\"live_messages\"><span class=\"live_messages_head\">".$live->messages[$i]['Live_Message_Title']."</span><span class=\"live_messages_text\">".nl2br($live->messages[$i]['Live_Message_Text'])."</span></li>\n";
            }
            echo "</ul>";
    }
    
    echo "</div>";
}
else{
    Utls::Redirect(SROOT."index.php");
}
require_once(THEMETEMP."engine.php");
?>