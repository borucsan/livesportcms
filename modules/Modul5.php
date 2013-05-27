<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Modul5
 *
 * @author Piotr
 */
include_once ("Module_body.php");
class Modul5 implements Module_body{
    private $tekst = "Jestem modulem nr. 5";
    public function ModuleBody(){
        return $this->tekst;
    }
}

?>
