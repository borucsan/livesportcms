<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Modul2
 *
 * @author Piotr
 */
include_once ("Module_body.php");
class Modul2 implements Module_body{
    private $tekst = "Jestem modulem nr. 2";
    public function ModuleBody(){
        return $this->tekst;
    }
}

?>
