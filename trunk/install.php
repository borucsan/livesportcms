<?php
define("VER", "0.3");
define("_DP", 1);
define("MAGIC_QUOTES", ini_get("magic_quotes_gpc") ? TRUE : FALSE);
require_once("./Installation/Installation.php");
require_once("./Installation/Utls.php");
isset($_POST['install_step']) && $_POST['install_step'] != "" && is_numeric($_POST['install_step']) ? $step = $_POST['install_step'] : $step = 1;

$install = new Installation($step);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Instalator LiveSportCMS <?php echo VER; ?></title>
        <link rel="stylesheet" type="text/css" media="all" href="./Installation/setup.css" />
    </head>
    <body>
        <div class="page_header">
            <h3>LiveSportCMS - Instalator</h3>
        </div>
        <div class="main_panel">
            <div class ="title_bar"><?php echo $install->getStepTitle(); ?></div>
            <?php if($step == 1){
                echo"<h4>Witamy w instalatorze aplikacji LiveSportCMS.</h4>";
            } ?>
            <form class="install_form" action="install.php" method="POST">
                
        <?php
                switch ($step) {
                    default:
                    case 1:
                        echo "<fieldset>";
                        echo "<legend>Upewnij się że następujące pliki mają ustawione uprawnienia na zapis(777):</legend><br />";
                        $files = $install->verifyFileRights();
                        echo "<table class=\"inner_table\">";
                        echo "<tr><td>Plik</td><td>Zapisywalny</td></tr>";
                        foreach ($files as $key => $value) {
                            echo "<tr><td>".$key."</td><td class=\"".($value == true ? "positive" : "negative")."\">".($value == true ? "TAK" : "NIE")."</td></tr>";
                        }
                        echo "</table>";
                        
                        if(in_array(false, $files)){
                            echo "<a class=\"install_button\" href=\"install.php\" >Odśwież</a>";
                        }
                        else{
                            echo "<input type=\"hidden\" name=\"install_step\" value=\"2\" />";
                            echo "<input type=\"submit\" name=\"install_next\" value=\"Dalej\" />";
                        }
                        echo "</fieldset>";
                        break;
                    case 2:
                        if(isset($_POST['install_prev'])){
                            $db_host = Utls::StipUserInput(trim($_POST['install_db_host']));
                            $db_port = Utls::StipUserInput(trim($_POST['install_db_port']));
                            $db_name = Utls::StipUserInput(trim($_POST['install_db_name']));
                            $db_user = Utls::StipUserInput(trim($_POST['install_db_user']));
                            $db_prefix = Utls::StipUserInput(trim($_POST['install_db_prefix']));
                        }
                        ?>
                        <fieldset>
                            <legend>Podaj dane bazy MySQL:</legend><br />
                            <label for="install_db_host">Host bazy danych:</label><input type="text" id="install_db_host" name="install_db_host" value="<?php echo (isset($db_host) ? $db_host : "localhost"); ?>" /><br />
                            <label for="install_db_port">Port bazy danych:</label><input type="text" id="install_db_port" name="install_db_port" value="<?php echo (isset($db_port) ? $db_port : "3306"); ?>" /><br />
                            <label for="install_db_name">Nazwa bazy danych:</label><input type="text" id="install_db_name" name="install_db_name" value="<?php echo (isset($db_name) ? $db_name : ""); ?>"  /><br />
                            <label for="install_db_user">Użytkownik bazy danych:</label><input type="text" id="install_db_user" name="install_db_user" value="<?php echo (isset($db_user) ? $db_user : "root"); ?>" /><br />
                            <label for="install_db_password">Hasło użytkownika bazy danych:</label><input type="password" id="install_db_password" name="install_db_password" /><br />
                            <label for="install_db_prefix">Prefix tabeli bazy danych:</label><input type="text" id="install_db_prefix" name="install_db_prefix" value="<?php echo (isset($db_prefix) ? $db_prefix : "lsc_"); ?>" />
                            <input type="hidden" name="install_step" value="3" />
                            <input type="submit" name="install_next" value="Dalej" />
                        </fieldset>
                        <?php
                        break;
                    case 3:
                        $db_host = Utls::StipUserInput(trim($_POST['install_db_host']));
                        $db_port = Utls::StipUserInput(trim($_POST['install_db_port']));
                        $db_name = Utls::StipUserInput(trim($_POST['install_db_name']));
                        $db_user = Utls::StipUserInput(trim($_POST['install_db_user']));
                        $db_password = Utls::StipUserInput(trim($_POST['install_db_password']));
                        $db_prefix = Utls::StipUserInput(trim($_POST['install_db_prefix']));
                        if(isset($_POST['install_prev'])){
                            $pageTitle = Utls::StipUserInput($_POST['install_page_title']);
                            $theme = Utls::StipUserInput($_POST['install_page_theme']);
                            $algo = Utls::StipUserInput($_POST['install__hash_algo']);
                            $algoStr = Utls::StipUserInput($_POST['install_hash_algo_str']);
                            $expire = Utls::StipUserInput($_POST['install_session_expire']);
                            $actLimit = Utls::StipUserInput($_POST['install_last_activity_limit']);
                            $liveLimit = Utls::StipUserInput($_POST['install_live_messages_limit']);
                        }
                        
                        try{
                            $install->checkDBSettings($db_host, $db_port, $db_name, $db_user, $db_password, $db_prefix);
                            $install->SaveConfig($db_host, $db_port, $db_name, $db_user, $db_password, $db_prefix);
                            $install->CreateTables($db_prefix);
                            echo "<div class=\"operation_success_box\">Zapisano konfigurację i utworzono tabele bazy danych MySQL.</div>";
                            ?>
                            <fieldset>
                                <input type="hidden" name="install_step" value="4" />
                                <input type="submit" name="install_next" value="Dalej" />
                            </fieldset>
                            <?php
                        }
                        catch(InstallationException $ie){
                            switch ($ie->getCode()) {
                                case 1:
                                    echo "<div class=\"error_box\">Nie można utworzyć testowej tabeli w bazie danych!<br />Sprawdż podane dane lub uprawnienienia użytkownika bazy danych MySQL.</div>";
                                    break;
                                case 2:
                                    echo "<div class=\"error_box\">Nie można usunąć testowej tabeli w bazie danych!<br />Sprawdż podane dane lub uprawnienienia użytkownika bazy danych MySQL.</div>";
                                    break;
                                case 3:
                                    echo "<div class=\"error_box\">Nie można zapisać pliku konfiguracyjnego \"config\\db_config.php\"!</div>";
                                    break;
                                case 4:
                                    echo "<div class=\"error_box\">Nie można utworzyć tabeli w bazie danych!<br />Sprawdż podane dane lub uprawnienienia użytkownika bazy danych MySQL.</div>";
                                    break;
                                case 5:
                                    echo "<div class=\"error_box\">Nie można usunąć tabeli w bazie danych!<br />Sprawdż podane dane lub uprawnienienia użytkownika bazy danych MySQL.</div>";
                                    break;
                                case 6:
                                    echo "<div class=\"error_box\">Wystąpił błąd podłączenia do bazy danych!<br />Sprawdż podane dane.</div>";
                                    break;
                            }
                            ?>
                            <fieldset>
                                <input type="hidden" name="install_step" value="2" />
                                <input type="submit" name="install_prev" value="Wstecz" />
                                <input type="hidden" id="install_db_host" name="install_db_host" value="<?php echo $db_host; ?>" />
                                <input type="hidden" id="install_db_port" name="install_db_port" value="<?php echo $db_port; ?>" />
                                <input type="hidden" id="install_db_name" name="install_db_name" value="<?php echo $db_name; ?>" />
                                <input type="hidden" id="install_db_user" name="install_db_user" value="<?php echo $db_user; ?>" />
                                <input type="hidden" id="install_db_prefix" name="install_db_prefix" value="<?php echo $db_prefix; ?>" />
                            </fieldset>
                            <?php
                        }
                        break;
                    case 4:
                        if(isset($_POST['install_prev'])){
                            $pageTitle = Utls::StipUserInput($_POST['install_page_title']);
                            $theme = Utls::StipUserInput($_POST['install_page_theme']);
                            $algo = Utls::StipUserInput($_POST['install_hash_algo']);
                            $algoStr = Utls::StipUserInput($_POST['install_hash_algo_str']);
                            $expire = Utls::StipUserInput($_POST['install_session_expire']);
                            $actLimit = Utls::StipUserInput($_POST['install_last_activity_limit']);
                            $liveLimit = Utls::StipUserInput($_POST['install_live_messages_limit']);
                        }
                        ?>
                        <fieldset>
                                <input type="submit" name="install_next" value="Dalej" />
                                <label for="install_page_title">Tytuł strony:</label><input type="text" id="install_page_title" name="install_page_title" maxlength="256" value="<?php echo (isset($pageTitle) ? $pageTitle : ""); ?>" /><br />
                                <label for="install_page_theme">Motyw strony:</label>
                                <select id="install_page_theme" name="install_page_theme">
                                <?php
                                $themes = Utls::getFolderList("./theme/", array(".", "..", "template"), Utls::DIRS);
                                $count = count($themes);
                                for($i = 0; $i < $count; ++$i){
                                    echo "<option value=\"".$themes[$i]."\" ".(isset($theme) && $themes[$i] == $theme ? "selected=\"selected\"" : ($themes[$i] == "default" ? "selected=\"selected\"" : "")).">".$themes[$i]."</option>\n";
                                }
                                ?>
                                </select><br />
                                <label for="install_hash_algo">Algorytm skrótu:</label>
                                <select id="install_hash_algo" name="install__hash_algo">
                                <?php
                                $algos = array("MD5", "Blowfish", "SHA256", "SHA512");
                                $count = count($algos);
                                for($i = 0; $i < $count; ++$i){
                                    echo "<option value=\"".$algos[$i]."\" ".(isset($algo) && $algos[$i] == $algo ? "selected=\"selected\"" : ($algos[$i] == "Blowfish" ? "selected=\"selected\"" : "")).">".$algos[$i]."</option>\n";
                                }
                                ?>
                                </select><br />
                                <label for="install_hash_algo_str">Siła algorytmu:</label><input type="text" id="install_hash_algo_str" name="install_hash_algo_str" value="<?php echo (isset($algoStr) ? $algoStr : "10"); ?>" /><div class="form_hint" ><img src="<?php echo "./Installation/question_mark.png"?>" /><p>Parametr określający siłę funkcji skrótu.<br />MD5: parametr jest ignorowany;<br />Blowfish: MIN - 4, MAX - 31;<br />SHA256: MIN - 1000, MAX - 999999999;<br />SHA512: MIN - 1000, MAX - 999999999;<br />Zobacz: <a href="http://php.net/manual/en/function.crypt.php" target="_blank">crypt</a></p></div><br />
                                <label for="install_session_expire">Czas wygaśniecia sesji(sek.):</label><input type="text" id="install_session_expire" name="install_session_expire" value="<?php echo (isset($expire) ? $expire : "43200"); ?>" /><br />
                                <label for="install_last_activity_limit">Maksymalny czas nieaktywności(sek.):</label><input type="text" id="install_last_activity_limit" name="install_last_activity_limit" value="<?php echo (isset($actLimit) ? $actLimit : "900"); ?>" /><br />
                                <label for="install_live_messages_limit">Maksymalna ilość wiadomości na strone(live):</label><input type="text" id="install_live_messages_limit" name="install_live_messages_limit" value="<?php echo (isset($liveLimit) ? $liveLimit : "10"); ?>" /><br />
                                <input type="hidden" name="install_step" value="5" />
                            </fieldset>
                            <?php
                        break;
                    case 5:
                       try{
                        $pageTitle = Utls::StipUserInput($_POST['install_page_title']);
                        $theme = Utls::StipUserInput($_POST['install_page_theme']);
                        $algo = Utls::StipUserInput($_POST['install__hash_algo']);
                        $algoStr = Utls::StipUserInput($_POST['install_hash_algo_str']);
                        $expire = Utls::StipUserInput($_POST['install_session_expire']);
                        $actLimit = Utls::StipUserInput($_POST['install_last_activity_limit']);
                        $liveLimit = Utls::StipUserInput($_POST['install_live_messages_limit']);
                        require_once("./config/db_config.php");
                        require_once("./Installation/DB.php");
                        DB::configure($db_config);
                        unset($db_config);
                        $install->SaveSettings($pageTitle, $theme, $algo, $algoStr, $expire, $actLimit, $liveLimit);
                        echo "<div class=\"operation_success_box\">Zapisano ustawienia serwisu.</div>";
                        ?>
                        <fieldset>
                            <input type="hidden" name="install_step" value="6" />
                            <input type="submit" name="install_next" value="Dalej" />
                        </fieldset>
                        <?php
                       }
                       catch (InstallationException $ie){
                           switch ($ie->getCode()) {
                               default:
                               case 7:
                                   echo "<div class=\"error_box\">Wystąpił błąd podczas zapisu ustawień do bazy danych MySQL!</div>";
                                   break;
                               case 12:
                                   echo "<div class=\"error_box\">Podano nieprawidłowe dane.<br />Siła algorytmu, czasy i limit wiadomości powinny być liczbą całkowitą.</div>";
                                   break;
                               case 13:
                                   echo "<div class=\"error_box\">Podana siła algorytmu \"Blowfish\" jest nieprawidłowa.<br />Algorytm obsługuje wartości z przedziału od 4 do 31.</div>";
                                   break;
                               case 14:
                                   echo "<div class=\"error_box\">Podana siła algorytmu \"SHA256\" lub \"SHA512\" jest nieprawidłowa.<br />Algorytm obsługuje wartości z przedziału od 1000 do 999999999.</div>";
                                   break;
                           }
                           ?>
                           <fieldset>
                                <input type="hidden" name="install_step" value="4" />
                                <input type="submit" name="install_prev" value="Wstecz" />
                                <input type="hidden" name="install_page_title" value="<?php echo $pageTitle; ?>" />
                                <input type="hidden" name="install_page_theme" value="<?php echo $theme; ?>" />
                                <input type="hidden" name="install_hash_algo" value="<?php echo $algo; ?>" />
                                <input type="hidden" name="install_hash_algo_str" value="<?php echo $algoStr; ?>" />
                                <input type="hidden" name="install_session_expire" value="<?php echo $expire; ?>" />
                                <input type="hidden" name="install_last_activity_limit" value="<?php echo $actLimit; ?>" />
                                <input type="hidden" name="install_live_messages_limit" value="<?php echo $liveLimit; ?>" /><br />
                            </fieldset>
                           <?php
                       }
                        break;
                    case 6:
                        if(isset($_POST['install_prev'])){
                           $login = Utls::StipUserInput(trim($_POST['install_user']));
                            $email = Utls::StipUserInput(trim($_POST['install_email']));
                            $name = Utls::StipUserInput(trim($_POST['install_name']));
                            $surname = Utls::StipUserInput(trim($_POST['install_surname']));
                        }
                        ?>
                        <fieldset>
                                <legend>Podaj dane superadministratora strony:</legend>
                                <input type="submit" name="install_next" value="Dalej" />
                                <label for="install_user">Login:</label><input type="text" id="install_user" name="install_user" maxlength="32" value="<?php echo (isset($login) ? $login : ""); ?>" placeholder="3-12 znaków a-z0-9_-" /><div class="form_hint" ><img src="<?php echo "./Installation/question_mark.png"?>" /><p>Poprawny login powinien składać się z następującej kombinacji:<br />3-12 znaków a-z (0-9 _-)</p></div><br />
                                <label for="install_password">Hasło:</label><input type="password" id="install_password" name="install_password" placeholder="8-24 znaków a-zA-Z0-9_#@" /><div class="form_hint" ><img src="<?php echo "./Installation/question_mark.png"?>" /><p>Poprawne hasło powinno składać się z następującej kombinacji:<br />8-24 znaków a-z A-Z 0-9 (_#@)</p></div><br />
                                <label for="install_password_rep">Powtórz hasło:</label><input type="password" id="install_password_rep" name="install_password_rep" /><br />
                                <label for="install_email">E-Mail:</label><input type="text" id="install_email" name="install_email" maxlength="100" value="<?php echo (isset($email) ? $email : ""); ?>" /><br />
                                <label for="install_name">Imię:</label><input type="text" id="install_name" name="install_name" maxlength="20" value="<?php echo (isset($name) ? $name : ""); ?>" /><br />
                                <label for="install_surname">Nazwisko:</label><input type="text" id="install_surname" name="install_surname" maxlength="20" value="<?php echo (isset($surname) ? $surname : ""); ?>" /><br />
                                <input type="hidden" name="install_step" value="7" />
                            </fieldset>
                        <?php
                        break;
                    case 7:
                        $login = Utls::StipUserInput(trim($_POST['install_user']));
                        $pass = Utls::StipUserInput(trim($_POST['install_password']));
                        $pass_rep = Utls::StipUserInput(trim($_POST['install_password_rep']));
                        $email = Utls::StipUserInput(trim($_POST['install_email']));
                        $name = Utls::StipUserInput(trim($_POST['install_name']));
                        $surname = Utls::StipUserInput(trim($_POST['install_surname']));
                        require_once("./config/db_config.php");
                        require_once("./Installation/DB.php");
                        DB::configure($db_config);
                        unset($db_config);
                        require_once("./Installation/db_names.php");
                        require_once("./Installation/Config.php");
                        require_once("./Installation/Register.php");
                        try {
                            $register = new Register($login, $pass, $pass_rep, $email, $name, $surname);
                             echo "<div class=\"operation_success_box\">Dodano użytkownika.</div>";
                             echo "<div class=\"install_fin\">";
                             echo "<h3>Instalacja została ukończona.</h3>";
                             echo "<a href=\"index.php\">Przejdz do strony głównej</a><br />";
                             echo "<p>Dla bezpieczeństwa usuń plik install.php i folder Installation wraz z zawartością z serwera oraz zmień uprawnienia pliku \"db_config.php\" w folderze Config na 666.</p>";
                             echo "</div>";
                             ?>
     
                            <?php
                        } catch (InvalidDataException $ide) {
                            switch($ide->getCode()){
                                case 1: echo "<div class=\"error_box\">Nieprawidłowy login!</div>"; break;
                                case 2: echo "<div class=\"error_box\">Nieprawidłowe hasło!</div>"; break;
                                case 3: echo "<div class=\"error_box\">Za długie Imię!</div>"; break;
                                case 4: echo "<div class=\"error_box\">Za długie Nazwisko!</div>"; break;
                                case 5: echo "<div class=\"error_box\">Nieprawidłowy Email!</div>"; break;
                                case 6: echo "<div class=\"error_box\">Nazwa użytkownika zajęta!</div>"; break;
                                case 7: echo "<div class=\"error_box\">Hasła się nie zgadzają!</div>"; break;
                            }
                            ?>
                           <fieldset>
                                <input type="submit" name="install_prev" value="Wstecz" />
                                <input type="hidden" name="install_user" value="<?php echo $login ?>" /><br />
                                <input type="hidden" name="install_email" value="<?php echo $email ?>" />
                                <input type="hidden" name="install_name" value="<?php echo $name ?>" />
                                <input type="hidden" name="install_surname" value="<?php echo $surname ?>" />
                                <input type="hidden" name="install_step" value="6" />
                            </fieldset>
                            <?php
                        }
                        break;
                    
                }
        ?>      
                </form>
        </div>
    <div id = "footer"><span><strong>Powered by <a href="#">LiveSportCMS</a> <?php echo VER; ?></strong> Copyright &COPY; 2012 Opublikowano na licencji GNU GPL v3</span></div>
    </body>
</html>