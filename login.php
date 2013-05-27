<?php
define('_DP', 1);
require_once("./include/core.php");
$document->setSubTitle("Logowanie");
?>
<div class ="main_panel">
    <div class ="title_bar">Logowanie</div>
<?php
if(isset($_GET['error']))
{
    echo "<p>Error: ".$_GET['error']." </p><br />";
    
}
if (USER) {
    ?>
    <ul>
        <li>Witaj <?php echo "$user->mUsername"?>!</li>
        
        <li><a href ="user_profile.php">Profil użytkownika</a></li>
        <li><a href ="index.php?logout=true">Wyloguj</a></li>
    <?php
    if (ADMIN){
        ?>
        <li><a href ="<?php echo ADMINDIR."index.php";?>">Panel Administracyjny</a></li>
        <?php
    }
    ?>
    </ul>
<?php
}

else
{
    ?>
    <form class="main_form" action="login.php" method="post">
        <fieldset>
            <ol>
                <li>
                    <label for="login">Login:</label>
                    <input id="login" type="text" name="login" size="20" maxlength="80" />
                </li>
                <li>
                    <label for="pass">Hasło:</label>
                    <input id="pass" type="password" name="password" size="20" maxlength="20" />
                </li>
                <fieldset>
                    <li>Nie posiadasz konta - <a href ="register.php">Zarejestruj się</a></li>
                    <li>
                        <input type="submit" name="submit" value="Zaloguj" />
                        <input type="reset" name="reset" value="Wyczyść" />
                    </li>
                    
                </fieldset>
            </ol>
        </fieldset>
    </form>
    <?php
}
?>
    </div>
<?php
require_once(THEMETEMP."engine.php");
?>

