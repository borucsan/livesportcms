<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Modul1
 *
 * @author Piotr
 */
include_once ("Module_body.php");
class Modul1 implements Module_body{
    private $tekst = "Jestem modulem nr. 1";
    public function ModuleBody(){
        return $this->tekst;
    }
}

?>
