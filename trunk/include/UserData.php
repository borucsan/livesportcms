<?php
/**
 * Description of UserData
 *
 * @author boruc-san
 * @since  2012-11-02
 */
defined('_DP') or die("Direct access not allowed!");
class UserData {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    const GUEST = 0; 
    const USER = 1;
    const ADMIN = 2;
    const SUPERADMIN = 3;
    
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;
    const SUSPENED = 2;
    const MARKED_FOR_DELETE = 3;
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    private $mUsername = ""; 
    private $mUserlevel = UserData::GUEST;
    private $mUserstatus = UserData::NOT_ACTIVE;
    private $mAvatar = "";
    private $mUser_id = 0;
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($plogin, $pAvatar, $pUserlevel, $pUserstatus, $pUser_id) {
        $this->mUserlevel = $pUserlevel;
        $this->mUserstatus = $pUserstatus;
        $this->mUsername = $plogin;
        $this->mAvatar = $pAvatar;
        $this->mUser_id = $pUser_id;
    }

    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    function __get($name) {
        return $this->$name;
    }
}
?>
