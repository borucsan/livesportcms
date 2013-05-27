<?php
define('_DP', 1);
require_once("../include/core.php");
$document->setSubTitle("Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja</div>
<?php if(ADMIN){ 
    
    ?>
<div class="admin_category"><a href="articles"><img src = "icons/articles.png"/>Artykuły</a></div>
<div class="admin_category"><a href="categories"><img src = "icons/categories.png"/>Kategorie</a></div>
<div class="admin_category"><a href="modules"><img src = "icons/modules.png"/>Moduły</a></div>
<div class="admin_category"><a href="panels"><img src = "icons/panels.png"/>Panele</a></div>
<div class="admin_category"><a href="reports"><img src = "icons/reports.png"/>Relacje</a></div>
<div class="admin_category"><a href="users"><img src = "icons/users.png"/>Użytkownicy</a></div>
</div>
<?php }else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
