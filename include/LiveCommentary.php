<?php
/**
 * Description of LiveCommentary
 *
 * @author boruc-san
 */
class LiveCommentary {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    private $mLiveID = 0;
    private $mTemplate = "";
    private $mScore;
    private $mLive = false;
    private $messages = array();
    private $mAllmessagesCount = 0;
    private $mUpdateTimeStamp;
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($pLiveID, $pTemplate, $pLastUpdate = 0, $pLiveMsgPage = 0, $pLimit = 0) {
        $this->mLiveID = $pLiveID;
        $this->mScore = new $pTemplate($pLiveID);
        $this->mTemplate = $pTemplate;
        $db = DB::getDB();
        $result =  $db->query("SELECT Live_Commentary_Live, User_ID
                               FROM ".DB_LIVE_COMMENTARY."
                               WHERE Live_Commentary_ID=".$pLiveID);
         if($result == FALSE){
            throw new Exception("");
           //TODO:Create custom exception
        }
        $data = $result->fetch_object();
        $this->mLive = $data->Live_Commentary_Live > 0 ? true : false;
        $result->free();
        $this->PopulateMessages($pLiveID, $pLastUpdate, $pLiveMsgPage, $pLimit);
        $this->mUpdateTimeStamp = time();
    }
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    
    public function __get($pName){
        return $this->$pName;
    }
    private function PopulateMessages($pLiveID, $pLastUpdate = 0, $pLiveMsgPage = 0, $pLimit = 0) {
        $db = DB::getDB();
        $query = "SELECT Live_Message_Title, Live_Message_Text
                  FROM ".DB_LIVE_MESSAGES."
                  WHERE Live_Commentary_ID=".$pLiveID." AND UNIX_TIMESTAMP(Live_Message_Update)>".$pLastUpdate."
                  ORDER BY Live_Message_Order DESC";
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("Error while populate");
           //TODO:Create custom exception
        }
        for($i = 0; $i < $result->num_rows; ++$i){
            $row = $result->fetch_array();
            array_push($this->messages, $row);
        }
        $this->mAllmessagesCount = $result->num_rows;
        $result->free();
        if($pLimit != 0){
            $result = $db->query("SELECT COUNT(Live_Message_ID) AS MSGCOUNT
                                  FROM ".DB_LIVE_MESSAGES."
                                  WHERE Live_Commentary_ID=".$pLiveID);
            $this->mAllmessagesCount = $result->fetch_object()->MSGCOUNT;
            $result->free();
        }
    }
    public function UpdateMessages($pMessageTitle, $pMessageText){
        if($pMessageTitle == "" || $pMessageText == ""){
            throw new LiveCommentaryException("Message Title and text could not be empty!", 3);
        }
        $pMessageTitle = Utls::StipUserInput(trim($pMessageTitle));
        $pMessageText = Utls::StipUserInput(trim($pMessageText));
        $db = DB::getDB();
        $query = "SELECT MAX(Live_Message_Order) AS MAX
                  FROM ".DB_LIVE_MESSAGES."
                  WHERE Live_Commentary_ID=".$this->mLiveID;
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("Error while downloading data");
            //TODO:Create custom exception
        }
        $row = $result->fetch_object();
        $max = intval($row->MAX);
        $query = "INSERT INTO ".DB_LIVE_MESSAGES."
                  (Live_Commentary_ID, Live_Message_Update, Live_Message_Title, Live_Message_Text, Live_Message_Order)
                  VALUES(".$this->mLiveID.", NOW(), '".$pMessageTitle."', '".$pMessageText."', ".++$max.")";
        if($db->query($query) == FALSE){
            throw new LiveCommentaryException("Error while sending new message", LiveCommentaryException::ERROR_SND_NEW_MSG);
        }
        array_unshift($this->messages, array("Live_Message_Title" => $pMessageTitle, "Live_Message_Text" => $pMessageText));
    }
    public function UpdateToken() {
        if($this->mLive){
            $name = md5($this->mLiveID);
            $file = fopen(DATADIR.$name, 'wb');
            fwrite($file, date("Y-m-d H:i:s"));
            fclose($file);
        }
    }
    public function PrintAsXML($update){
        $xml = new DOMDocument("1.0", "UTF-8");
        $root = $xml->createElement("Update");
        $root->setAttribute('status', $this->mLive);
        $root->setAttribute("timestamp", $update);
        $xml->appendChild($root);
        $ms = $xml->createElement("MainScore");
        $root->appendChild($ms);
        $home = $xml->createElement("Home", $this->mScore->mHomeScore);
        $home->setAttribute("name", $this->mScore->mHomeName);
        $away = $xml->createElement("Away", $this->mScore->mAwayScore);
        $away->setAttribute("name", $this->mScore->mAwayName);
        $ms->appendChild($home);
        $ms->appendChild($away);
        
        $ssc = $xml->createElement("Subscores");
        foreach ($this->mScore->mSubscores as $key => $value) {
           $temp = $xml->createElement("Subscore");
           $temp->setAttribute("name", $this->mScore->getSubscoreName($key+1));
           $temp->setAttribute("id", $value[0]);
           $hssc = $xml->createElement("Home", $value[1]);
           $assc = $xml->createElement("Away", $value[2]);
           $temp->appendChild($hssc);
           $temp->appendChild($assc);
           $ssc->appendChild($temp);
        }
        $root->appendChild($ssc);
        $messages = $xml->createElement("Messages");
        foreach ($this->messages as $key => $value) {
            $msg = $xml->createElement("Message");
            $msg->appendChild($xml->createTextNode($value['Live_Message_Text']));
            $msg->setAttribute("title", $value['Live_Message_Title']);
            $messages->appendChild($msg);
        }
        $root->appendChild($messages);
        header('HTTP/1.1 200 OK');
        header('Content-type: text/xml');
        return $xml->saveXML();
    }
    //*************************************
    //           [**STATIC METHODS**]
    //*************************************
    public static function SetLiveUpdates($pLiveID, $pState) {
        $db = DB::getDB();
            $query = "UPDATE ".DB_LIVE_COMMENTARY."
                      SET Live_Commentary_Live=".($pState ? "1" : "0")."
                      WHERE Live_Commentary_ID=".$pLiveID;
            if($db->query($query) == FALSE){
                throw new Exception("error while upd live status");
                //TODO:Create custom exception
            }
    }
    public static function CheckToken($pLiveID, $pLastUpdate) {
        $name = md5($pLiveID);
        if(!file_exists(DATADIR.$name)){
            throw new LiveCommentaryTokenException("Live Token file not found");
        }
        $filemod = filemtime(DATADIR.$name);
        if($filemod <= $pLastUpdate){
            throw new LiveCommentaryNoUpdateException("No new data");
        }
        return $filemod;
    }
    public static function AddSubscore($pLiveID, $pCount) {
        if(!is_numeric($pCount)){
            throw new LiveCommentaryException("Subscores add value is NaN", LiveCommentaryException::VALUE_IS_NaN);
        }
        $db = DB::getDB();
        $query = "SELECT MAX(Subscore_Event_Order) AS COUNTN, SC.Score_ID AS SCID
                  FROM ".DB_LIVE_COMMENTARY." LC
                  JOIN ".DB_SCORES." SC ON LC.Score_ID=SC.Score_ID
                  JOIN ".DB_SUBSCORES." SSC ON SC.Score_ID=SSC.Score_ID
                  WHERE Live_Commentary_ID=".$pLiveID;
        $result = $db->query($query);
        if($result == FALSE){
            throw new Exception("Error while downloading data");
            //TODO:Create custom exception
        }
        $row = $result->fetch_object();
        $data = intval($row->COUNTN);
        $pCount = intval($pCount);
        $scoreID = $row->SCID;
        $result->free();
        $query = "INSERT INTO ".DB_SUBSCORES."(Score_ID, Subscore_Event_Order)
                  VALUES";
        for($i = 0; $i < $pCount; ++$i){
            $query .= "(".$scoreID.", ".(++$data).")";
            if($i != $pCount - 1){
                $query .= ",\n";
            }
        }
        $db->query($query);
        return $db->affected_rows;
    }
    public static function RemoveSubscore($id) {
        $db = DB::getDB();
        $query = "DELETE FROM ".DB_SUBSCORES."
                  WHERE Subscore_ID=".$id;
        if($db->query($query) == FALSE){
            echo "<div class=\"error_box\">Błąd podczas usuwania podwyniku.</div>";
            return;
        }
        else{
            echo "<div class=\"operation_success_box\">Usunięto podwynik o ID=".$id."</div>";
        }
    }
    public static function CreateNew($home, $away, $date, $time, $sub_id, $user_id, $sub_add = false, $subcategory_name ="", $categoryID = NULL){
       if($home == "" || $away == ""){
           throw new Exception("Dane nie mogą być puste.");
           //TODO:Create custom exception
       }
        
       $home = Utls::StipUserInput($home);
       $away = Utls::StipUserInput($away);
       $date = Utls::StipUserInput($date);
       $time = Utls::StipUserInput($time);
       
       //TODO: Date and time validation;
       $db = DB::getDB();
       $db->set_charset("utf8");
       $db->autocommit(FALSE);
       if($sub_add){
           if($subcategory_name == ""){
               throw new Exception("Należy podać nazwę podkategorii");
           }
           $subcategory_name = Utls::StipUserInput($subcategory_name);
           $sub_id = self::AddSubcategory($subcategory_name, $categoryID);
       }
       list($y, $m, $d) = explode("/", $date);
       list($h, $min) = explode(":", $time);
       
       $timestamp = mktime(intval($h), intval($min), 0, intval($m), intval($d), intval($y));
       $query = "INSERT INTO ".DB_SCORES."(Subcategory_ID, Score_Home_Name, Score_Away_Name, Score_Event_Datetime)
                 VALUES(".$sub_id.", '".$home."', '".$away."', '".date("Y-m-d H:i:s", $timestamp)."')";
       if($db->query($query) == FALSE){
           throw new Exception("Error in st1");
           //TODO:Create custom exception
       }
       $score_id = $db->insert_id;
       $query = "INSERT INTO ".DB_SUBSCORES."(Score_ID, Subscore_Event_Order)
                 VALUES(".$score_id.", 1)";
       if($db->query($query) == FALSE){
           throw new Exception("Error in st2");
           //TODO:Create custom exception
       }
       
       $query = "INSERT INTO ".DB_LIVE_COMMENTARY."(Score_ID, User_ID, Live_Commentary_Live)
                 VALUES(".$score_id.", ".$user_id.", 0)";
       if($db->query($query) == FALSE){
           throw new Exception("Error in st3");
           //TODO:Create custom exception
       }
       $rel = $db->insert_id;
       $db->commit();
       $db->autocommit(TRUE);
       return $rel;
   }
   public static function AddSubcategory($name, $category){
       $db = DB::getDB();
       $query = "INSERT INTO ".DB_SUBCATEGORIES."(Subcategory_Name, Categorie_ID)
                 VALUES('".$name."', ".$category.")";
       if($db->query($query)== FALSE){
           throw new Exception("Error in add subcategory");
           //TODO:Create custom exception
       }
       return $db->insert_id;
   }
   public static function GetTemplate($pLiveID) {
       $db = DB::getDB();
       $db->set_charset("utf8");
       $query = "SELECT Categorie_Live_Template
                 FROM ".DB_LIVE_COMMENTARY." LC
                 JOIN ".DB_SCORES." S ON LC.Score_ID=S.Score_ID
                 JOIN ".DB_SUBCATEGORIES." SC ON S.Subcategory_ID=SC.Subcategory_ID
                 JOIN ".DB_CATEGORIES." C ON SC.Categorie_ID=C.Categorie_ID
                 WHERE Live_Commentary_ID=".$pLiveID;
        $result = $db->query($query);
        if($result == FALSE || $result->num_rows !== 1){
            throw new TemplateNotFoundException("Live Template is not found");
        }
        return $result->fetch_object()->Categorie_Live_Template;
   }
}
class TemplateNotFoundException extends RuntimeException{

    public function __construct($message, $code = 1, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class LiveCommentaryException extends RuntimeException{
    const VALUE_IS_NaN = 1;
    const ERROR_UPD_SCORE = 2;
    const ERROR_SND_NEW_MSG = 3;
     public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class LiveCommentaryNoUpdateException extends RuntimeException{
    public function __construct($message) {
        parent::__construct($message, $code = 1, NULL);
    }
}
class LiveCommentaryTokenException extends RuntimeException{
    public function __construct($message) {
        parent::__construct($message, $code = 2, NULL);
    }
}
?>