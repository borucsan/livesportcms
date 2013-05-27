<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Settings.php");
$document->setSubTitle("Ustawienia - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Ustawienia</div>
<?php if(SUPERADMIN){ 
    if(isset($_POST['settings_save'])){
        try{
            $set = new Admin_Settings($_POST['settings_page_title'], $_POST['settings_page_theme'], $_POST['settings_host'], $_POST['settings_path'], $_POST['settings_hash_algo'], $_POST['settings_hash_algo_str'], $_POST['settings_session_expire'], $_POST['settings_last_activity_limit'], $_POST['settings_live_messages_limit']);
        }
        catch (SettingsUpdateException $sue){
            
        }
    }
    else{
        $config = Config::getConfig();
        $themes = Utls::getFolderList(THEMEDIR, array(".", "..", "template"), Utls::DIRS);
        $document->addCSS("<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".INCLUDEDIR."css/form_hint.css\" />");
        $algos = Admin_Settings::getAvailableHash();
        $count = count($themes);
        ?>
        <form class="admin_form_add" action="index.php" method="POST">
            <input type="submit" name="settings_save" value="Zapisz" />
            <input type="reset" value="Reset" /><br />
            <fieldset>
                <label for="settings_page_title">Tytuł strony:</label><input type="text" id="settings_page_title" name="settings_page_title" value="<?php echo $config->page_title; ?>" /><br />
                <label for="settings_page_theme">Motyw strony:</label>
                <select id="settings_page_theme" name="settings_page_theme">
                    <?php
                    for($i = 0; $i < $count; ++$i){
                        echo "<option value=\"".$themes[$i]."\" ".($themes[$i] == $config->theme ? "selected=\"selected\"" : "").">".$themes[$i]."</option>\n";
                    }
                    ?>
                </select><br />
                <label for="settings_host">Host:</label><input type="text" id="settings_host" name="settings_host" value="<?php echo $config->script_host; ?>" /><div class="form_hint"><img src="<?php echo IMGDIR."question_mark.png"?>" /><p>Parametr określający host na którym znajduje się skrypt.<br />Zmiana na nieprawidłową wartość może zablokować możliwość logowania</p></div><br />
                <label for="settings_path">Ścieżka:</label><input type="text" id="settings_path" name="settings_path" value="<?php echo $config->script_path; ?>" /><div class="form_hint"><img src="<?php echo IMGDIR."question_mark.png"?>" /><p>Parametr określający ścieżkę do skryptu na serwerze.<br />Zmiana na nieprawidłową wartość może zablokować możliwość logowania</p></div><br />
            </fieldset>
            <fieldset>
                <label for="settings_hash_algo">Algorytm skrótu:</label>
                <select id="settings_hash_algo" name="settings_hash_algo">
                    <?php
                    $count = count($algos);
                    for($i = 0; $i < $count; ++$i){
                        echo "<option value=\"".$algos[$i]."\" ".($algos[$i] == $config->hash_algo ? "selected=\"selected\"" : "").">".$algos[$i]."</option>\n";
                    }
                    ?>
                </select><br />
                <label for="settings_hash_algo_str">Siła algorytmu:</label><input type="text" id="settings_hash_algo_str" name="settings_hash_algo_str" value="<?php echo $config->hash_str; ?>" /><div class="form_hint"><img src="<?php echo IMGDIR."question_mark.png"?>" /><p>Parametr określający siłę funkcji skrótu.<br />MD5: parametr jest ignorowany;<br />Blowfish: MIN - 4, MAX - 31;<br />SHA256: MIN - 1000, MAX - 999999999;<br />SHA512: MIN - 1000, MAX - 999999999;<br />Zobacz: <a href="http://php.net/manual/en/function.crypt.php" target="_blank">crypt</a></p></div><br />
                <label for="settings_session_expire">Czas wygaśniecia sesji(sek.):</label><input type="text" id="settings_session_expire" name="settings_session_expire" value="<?php echo $config->session_expire; ?>" /><br />
                <label for="settings_last_activity_limit">Maksymalny czas nieaktywności(sek.):</label><input type="text" id="settings_last_activity_limit" name="settings_last_activity_limit" value="<?php echo $config->last_activity_limit; ?>" /><br />
            </fieldset>
            <fieldset>
                <label for="settings_live_messages_limit">Maksymalna ilość wiadomości na strone(live):</label><input type="text" id="settings_live_messages_limit" name="settings_live_messages_limit" value="<?php echo $config->live_messages_limit; ?>" /><br />
            </fieldset>

        </form>
    </div>
<?php }

}
else if(ADMIN){
    Utls::Redirect(ADMINDIR."index.php"); 
}
else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
