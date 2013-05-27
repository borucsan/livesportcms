<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Modul4
 *
 * @author Piotr
 */
include_once ("Module_body.php");
class Modul4 implements Module_body{
    private $tekst = "Jestem modulem nr. 4";
    public function ModuleBody(){
        return $this->tekst;
    }
}

?>
