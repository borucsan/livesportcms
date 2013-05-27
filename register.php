<?php
define('_DP', 1);
require_once("./include/core.php");
/**
 * Description of register
 *
 * @author Takezo1115
 */
$document->setSubTitle("Rejestracja");
?>
<div class ="main_panel">
    <div class ="title_bar">Rejestracja</div>
<?php
if (isset($_GET['login_info'])){
    echo "Poprawny login powinien składać się z następującej kombinacji:\n 
          3-12 znaków a-z (0-9 _-)";
}
if (isset($_GET['pass_info'])){
    echo "Poprawne hasło powinno składać się z następującej kombinacji:\n 
          8-24 znaków a-z A-Z 0-9 (_#@)";
}
if (isset($_GET['error'])) {
    switch($_GET['error'])
    {
        case 1: echo "Nieprawidłowy login!"; break;
        case 2: echo "Nieprawidłowe hasło!"; break;
        case 3: echo "Za długie Imię!"; break;
        case 4: echo "Za długie Nazwisko!"; break;
        case 5: echo "Nieprawidłowy Email!"; break;
        case 6: echo "Nazwa użytkownika zajęta!"; break;
        case 7: echo "Hasła się nie zgadzają!"; break;
    }
}
if (isset($_GET['msg'])) {
    if($_GET['msg'] == 1){
        echo "Możesz się teraz zalogować";
    }
} 
if(USER){
    echo "Jesteś zalogowany, nie można utworzyć konta.</div>";
}else {
?>
<form class="main_form" action="addUser.php" method="post">
    <fieldset>
        <ol>
            <li>
                <label for="login">*Login:<a href="register.php?login_info"><img src="<?php echo IMGDIR."question_mark.png"?>" alt="3-12 znaków a-z (0-9 _-)"/></a></label>
                <input id="login" type="text" name="register_login" size="30" maxlength="12" value="<?php if (isset($_POST['register_login'])) echo $_POST['register_login']; ?>" />
                
            </li>
            <li>
                <label for="pass">*Hasło:<a href="register.php?pass_info"><img src="<?php echo IMGDIR."question_mark.png"?>" alt="8-24 znaków a-z A-Z 0-9 (_#@)"/></a></label>
                <input id="pass" type="password" name="register_pass1" size="30" maxlength="24" />
                
            </li>
            <li>
                <label for="pass2">*Potwierdź hasło:</label>
                <input id="pass2" type="password" name="register_pass2" size="30" maxlength="20" />
            </li>
            <li>
                <label for="mail">*Adres e-mail:</label>
                <input id="mail" type="text" name="mail" size="30" maxlength="20" value="<?php if (isset($_POST['mail'])) echo $_POST['mail']; ?>" />
            </li>
            <li>
                <label for="name">Imię:</label>
                <input id="name" type="text" name="first_name" size="30" maxlength="20" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" />    
            </li>
            <li>
                <label for="surname">Nazwisko:</label>
                <input id="surname" type="text" name="last_name" size="30" maxlength="40" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" />
            </li>
            <li>
                <fieldset>
                    *Pola obowiązkowe
                    <input type="submit" name="submit" value="Zarejestruj" />
                    <input type="reset" name="reset" value="Wyczyść" />
                    <input type="hidden" name="submitted_register" value="TRUE" />
                </fieldset>
            </li>
        </ol>
    </fieldset>
</form>
</div>
<?php
} //end else statement
require_once(THEMETEMP."engine.php");
?>