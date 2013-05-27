<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User_Module
 *
 * @author Takezo1115
 */
include_once ("Module_body.php");
class User_Module implements Module_body{
    private $tekst = "Jestem modulem nr. 5";
    private function GenerateLogin()
    {
        if(isset($_GET['error']))
        {
            echo "<p>Error: ".$_GET['error']." </p><br />";

        }
        if (USER) {
            ?>
            <ul>
                

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
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
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
                            <li><input type="submit" name="submit" value="Zaloguj" /> <a href ="register.php">Rejestracja</a></li>
                    </ol>
                </fieldset>
            </form>
            <?php
        }
    }
    public function ModuleBody(){
        return $this->GenerateLogin();
    }
    
}

?>
