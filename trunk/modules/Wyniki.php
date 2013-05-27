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
class Wyniki implements Module_body{
    private $tekst = "Jestem modulem nr. 10";
    private function Wczytaj()
    {
        $db = DB::getDB();
        $result = $db->query("SELECT Score_Home_Name, Score_Away_Name, Score_Home_Score, 
                     Score_Away_Score, Score_Event_Datetime
                     FROM ".DB_SCORES."
                     ORDER BY Score_Event_DateTime DESC
                     LIMIT 3");
        $data = new ArrayObject();
        while($row = $result->fetch_array()){
            $data[] = $row;
        } 
        return $data;
    }
    private function Wyswietl()
    {
        $data = $this->Wczytaj();
        echo "<ul>";
        /*$home = explode(" ", $data[0]['Score_Home_Name']);
        $away = explode(" ", $data[0]['Score_Away_Name']);
        echo "<li>".$home[0]." - ".$data[0]['Score_Home_Score']." : ".$data[0]['Score_Away_Score']." 
            - ".$away[0]."</li>";
        $home = explode(" ", $data[1]['Score_Home_Name']);
        $away = explode(" ", $data[1]['Score_Away_Name']);
        echo "<li>".$home[0]." - ".$data[1]['Score_Home_Score']." : ".$data[1]['Score_Away_Score']." 
            - ".$away[0]."</li>";
        $home = explode(" ", $data[2]['Score_Home_Name']);
        $away = explode(" ", $data[2]['Score_Away_Name']);
        echo "<li>".$home[0]." - ".$data[2]['Score_Home_Score']." : ".$data[2]['Score_Away_Score']." 
            - ".$away[0]."</li>";*/
        for($i = 0; $i < count($data); $i++)
        {
            $home = explode(" ", $data[$i]['Score_Home_Name']);
            $away = explode(" ", $data[$i]['Score_Away_Name']);
            echo "<li>".$home[0]." - ".$data[$i]['Score_Home_Score']." : ".$data[$i]['Score_Away_Score']." 
                - ".$away[0]."</li>";
        }
        echo "</ul>";
    }
    public function ModuleBody(){
        return $this->Wyswietl();
    }
}
?>
