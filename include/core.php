<?php
defined('_DP') or die("Direct access not allowed!");

$level = ""; $step = 0;
while(!file_exists($level."const.php"))
{
    $level .= "../"; $step++; 
}
define('SROOT', $level);
define("MAGIC_QUOTES", ini_get("magic_quotes_gpc") ? TRUE : FALSE);
require_once(SROOT."const.php");
require_once(INCLUDEDIR."Utls.php");
if(!file_exists(CONFIGDIR."db_config.php")){
    trigger_error("File\"db_config\" not found!", E_USER_ERROR);
}
require_once(CONFIGDIR."db_config.php");
if(!isset($db_config)){
    Utls::Redirect(SROOT."install.php");
}
require_once(INCLUDEDIR."DB.php");

DB::configure($db_config);
unset($db_config);
require_once(INCLUDEDIR."db_names.php");
require_once(CONFIGDIR."Config.php");

require_once(INCLUDEDIR."UserData.php");
require_once(INCLUDEDIR."Authenticate.php");
require_once(INCLUDEDIR."Panel.php");

$config = Config::getConfig();

$theme = $config->theme;
$title = $config->page_title;

if(isset($_POST['login']) && isset($_POST['password'])){
    try {
        $authenticate = new Authenticate($_POST['login'], $_POST['password']);
        $user = $authenticate->getData();
    }
    catch (UnauthorizedAccessException $uae){
        Utls::Redirect(SROOT."login.php?error=".$uae->getCode());
    }
     unset($_POST['login'], $_POST['password'], $authenticate);
}
else if(isset($_GET['logout']) && $_GET['logout'] == "true"){
    Authenticate::logOut();
    Utls::Redirect(SROOT."index.php");
}
else{
    try{
        $user = Authenticate::CheckUser();
    }
    catch (SessionException $se){
        Authenticate::logOut();
        Utls::Redirect(SROOT."login.php?error=".$se->getCode());
    }
    
}
    define ("USER", $user->mUserlevel >= 1 ? TRUE : FALSE);
    define ("ADMIN", $user->mUserlevel >= 2 ? TRUE : FALSE);
    define ("SUPERADMIN", $user->mUserlevel >= 3 ? TRUE : FALSE);

ob_start();

require_once(THEMEDIR.$theme."/Theme.php");
$document = new Theme($title);
?>
