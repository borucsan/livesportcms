<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Panel
 *
 * @author Piotr
 */
defined('_DP') or die("Direct access not allowed!");
$db = DB::getDB();
$db->set_charset("utf8");
$result = $db->query("SELECT Modules_class
                     FROM ".DB_MODULES."
                     WHERE Modules_panel!=0");
if($result != FALSE){
   $row = $result->fetch_object();
   while($row){
       include_once(MODULESDIR.$row->Modules_class.".php");
       $row = $result->fetch_object();
   }
}

class Panel {
    private $moduly = array();
    private $ilosc;
    
    public function __construct($panel) {
        $db = DB::getDB();
        $db->set_charset("utf8");
        if($panel == 1)
        {
            $query = "SELECT Modules_ID, Modules_name, Modules_class, Modules_panel, Modules_hierarchy
                  FROM ".DB_MODULES." WHERE Modules_panel = 1 ORDER BY Modules_hierarchy";           
        }
        else if($panel == 2)
        {
            $query = "SELECT Modules_ID, Modules_name, Modules_class, Modules_panel, Modules_hierarchy
                  FROM ".DB_MODULES." WHERE Modules_panel = 2 ORDER BY Modules_hierarchy";
        }
        $result = $db->query($query);
        if($result == FALSE){
            echo "blad";
        }
        else{
            $this->ilosc = $result->num_rows;
            while($modul = $result->fetch_object()){
                $this->moduly[] = $modul;
            }
        }
    }
    
    public function Count()
    {
        return $this->ilosc;
    }
    public function ViewPanel()
    {
        for($i = 0; $i < $this->ilosc; ++$i){
            $modul = new $this->moduly[$i]->Modules_class;
            $module_title = $this->moduly[$i]->Modules_name;
            echo "<div class=\"panel\"><div class=\"title_bar\">".$module_title."</div>";
            echo $modul->ModuleBody();
            echo "</div>";
        }
    }
}
?>
