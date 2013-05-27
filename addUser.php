<?php
define('_DP', 1);
require_once("./include/core.php");
require_once(INCLUDEDIR."Register.php");

if( isset($_POST['submitted_register']) )
    {
        
        $login = trim($_POST['register_login']);
        $pass1 = trim($_POST['register_pass1']);
        $pass2 = trim($_POST['register_pass2']);
        $mail = trim($_POST['mail']);
        $name = trim($_POST['first_name']);
        $surname = trim($_POST['last_name']);
        
        try {
            $register = new Register($login, $pass1, $pass2, $mail, $name, $surname);
            
        }
        catch (InvalidDataException $ide){
            Utls::Redirect(SROOT."register.php?error=".$ide->getCode());
        }
        unset($_POST['submitted_register'], $_POST['register_login'], $_POST['register_pass1'], 
                $_POST['register_pass2'], $_POST['mail'], $_POST['first_name'], $_POST['last_name'], $register);
        Utls::Redirect(SROOT."register.php?msg=1");
    }
?>
