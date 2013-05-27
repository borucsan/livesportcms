<?php
/**
 * Description of AbstractCategory
 *
 * @author boruc-san
 * @since  2012-11-21
 */
defined('_DP') or die("Direct access not allowed!");
abstract class AbstractCategory {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    protected $mScore_ID;
    protected $mEventTimestamp = 0;
    protected $mHomeName = "";
    protected $mHomeScore = 0;
    protected $mAwayName = "";
    protected $mAwayScore = 0;
    protected $mUser_Login = "";
    protected $mCategory = "";
    protected $mSubcategory = "";
    protected $mSubscores = array();

    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public final function __construct($pLiveID) {
        $this->PopulateData($pLiveID);
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    public abstract function onUpdate();
    public abstract function getSubscoreName($pCount);
    //*************************************
    //           [**METHODS**]
    //*************************************
    private final function PopulateData($pLiveID){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $query = "SELECT S.Score_ID AS SCID, Score_Home_Name, Score_Away_Name, Score_Home_Score, Score_Away_Score, UNIX_TIMESTAMP(Score_Event_Datetime) AS TimeS, Subcategory_Name
                  FROM ".DB_LIVE_COMMENTARY." LC  
                  JOIN ".DB_SCORES." S ON LC.Score_ID=S.Score_ID
                  JOIN ".DB_SUBCATEGORIES." SC ON S.Subcategory_ID=SC.Subcategory_ID
                  WHERE Live_Commentary_ID=".$pLiveID;
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("Error while populate");
           //TODO:Create custom exception
        }
        $row = $result->fetch_object();
        $this->mHomeName = $row->Score_Home_Name;
        $this->mAwayName = $row->Score_Away_Name;
        $this->mHomeScore = $row->Score_Home_Score;
        $this->mAwayScore = $row->Score_Away_Score;
        $this->mEventTimestamp = $row->TimeS;
        $this->mSubcategory = $row->Subcategory_Name;
        $this->mScore_ID = $row->SCID;
        $result->free();
        $query = "SELECT Subscore_ID, Subscore_Home_Subscore, Subscore_Away_Subscore
                  FROM ".DB_SUBSCORES."
                  WHERE Score_ID=".$this->mScore_ID."
                  ORDER BY Subscore_Event_Order";
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("Error while populate");
           //TODO:Create custom exception
        }
        $row = $result->fetch_object();
        while($row){
            array_push($this->mSubscores, array($row->Subscore_ID, $row->Subscore_Home_Subscore, $row->Subscore_Away_Subscore));
            $row = $result->fetch_object();
        }
    }
    public function __get($name) {
        return $this->$name;
    }
    /**
     * @deprecated since 22.12.2012
     */
    public final function AddEmptySubscore() {
        array_push($this->mSubscores, array(0, 0));
    }
    public final function Update($new_main_home, $new_main_away, $new_sub_home, $new_sub_away) {
        if(!is_numeric($new_main_home) || !is_numeric($new_main_away)){
            throw new LiveCommentaryException("Main Score is NaN", LiveCommentaryException::VALUE_IS_NaN);
        }
        $this->mHomeScore = intval($new_main_home);
        $this->mAwayScore = intval($new_main_away);
        $count = count($new_sub_home);
        for($i = 0; $i < $count; ++$i){
            if(!is_numeric($new_sub_home[$i]) || !is_numeric($new_sub_away[$i])){
                throw new LiveCommentaryException("SubScore ".$i." is NaN", LiveCommentaryException::VALUE_IS_NaN);
            }
            $this->mSubscores[$i][1] = intval($new_sub_home[$i]);
            $this->mSubscores[$i][2] = intval($new_sub_away[$i]);
        }
        $this->onUpdate();
        $db = DB::getDB();
        $query = "UPDATE ".DB_SCORES."
                  SET Score_Home_Score=".$this->mHomeScore.", Score_Away_Score=".$this->mAwayScore."
                  WHERE Score_ID=".$this->mScore_ID;
        if($db->query($query) == FALSE){
                throw new LiveCommentaryException("Error while upd main score", LiveCommentaryException::ERROR_UPD_SCORE);
        }
        for($i = 0; $i < $count; ++$i){
            $query = "UPDATE ".DB_SUBSCORES."
                      SET Subscore_Home_Subscore=".$this->mSubscores[$i][1].", Subscore_Away_Subscore=".$this->mSubscores[$i][2]."
                      WHERE Score_ID=".$this->mScore_ID." AND Subscore_ID=".$this->mSubscores[$i][0];
            if($db->query($query) == FALSE){
                throw new LiveCommentaryException("Error while upd subscore ".$i, LiveCommentaryException::ERROR_UPD_SCORE);
            }
        }
                
        
    }
    public final function ScoreTable() {
        ?>
    <div class="score_table">
        <span class="score_table_head"><a href="#"><?php echo $this->mSubcategory; ?></a> >> <?php echo strftime("%A - %e %B %Y, %H:%M", $this->mEventTimestamp); ?></span>
        <table >
            <tbody>
                <tr>
                    <td id="score_table_home_name" class="score_table_teamnames"><?php echo $this->mHomeName; ?></td>
                    <td id="score_table_home_mainscore" class="score_table_score"><?php echo $this->mHomeScore; ?></td>
                    <td id="score_table_away_mainscore" class="score_table_score"><?php echo $this->mAwayScore; ?></td>
                    <td id="score_table_away_name" class="score_table_teamnames"><?php echo $this->mAwayName; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <table id="score_table_subscores" class="score_table_subscores">
                            <?php
                                $count = count($this->mSubscores);
                                for($i = 0; $i < $count; ++$i){
                                    echo "<tr id=\"sub".$this->mSubscores[$i][0]."\"><td class=\"score_table_subscore\">".$this->mSubscores[$i][1]."</td><td class=\"score_table_subscorenames\">"." ".$this->getSubscoreName($i + 1)."</td><td class=\"score_table_subscore\">".$this->mSubscores[$i][2]."</td></tr>";
                                }
                            ?>
                        </table>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
        <?php
    }
}

?>
