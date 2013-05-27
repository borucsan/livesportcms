<?php
defined('_DP') or die("Direct access not allowed!");
require_once(CATEGORIESTEMPDIR."AbstractCategory.php");
class Football extends AbstractCategory{
    public function getSubscoreName($pCount) {
        switch ($pCount) {
            case 3:
                return "1 Dogrywka";
                break;
            case 4:
                return "2 Dogrywka";
                break;
             case 5:
                return "Rzuty karne";
                break;
            default:
                return $pCount." Połowa";
                break;
        }
    }

    public function onUpdate() {
        $tempH = 0;
        $tempA = 0;
        for($i = 0; $i < 4; ++$i){
            if(isset($this->mSubscores[$i])){
                $tempH += $this->mSubscores[$i][1];
                $tempA += $this->mSubscores[$i][2];
            }
        }
        if($this->mHomeScore != $tempH){
            $this->mHomeScore = $tempH;
            //echo "<div class=\"warning_box\">Główny wynik dla gospodarza różni się od podwyników!<br />Ustawiam sumę podwyników</div>";
        }
         if($this->mAwayScore != $tempA){
             $this->mAwayScore = $tempA;
             //echo "<div class=\"warning_box\">Główny wynik dla gościa różni się od podwyników!<br />Ustawiam sumę podwyników</div>";
         }
         
    }
    
}
?>
