<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Panels.php");
$document->setSubTitle("Panele - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Panele</div>
<?php if(ADMIN){ 
    $admin_panels = new Admin_Panels();
    $modules = $admin_panels->GetModules();
    if (isset($_GET['edit_error'])) {     
        switch($_GET['edit_error'])
        {
            case 1: echo "Podane ID modułu nie jest liczbą";
                break;
            case 2: echo "Podany moduł nie został znaleziony w bazie";
                break;
            case 3: echo "Podany priorytet nie jest liczbą";
                break;
            case 4: echo "Podany status nie jest liczbą";
                break;
            case 5: echo "Podany status nie jest zdefiniowany";
                break;
            case 6: echo "Błąd bazy danych, nie możnabyło zmienić rekordu";
                break;
        }
    }
    if ( isset($_GET['msg'])) {
        if($_GET['msg'] == "edited"){
            echo "Moduł został uaktualniony<br/>";
        }
    }
    echo "<div><table class='admin_modules_table'>";
    echo "<thead>
                <th>Nazwa</th>
                <th>Plik/Klasa</th>
                <th>Poziom Priorytetu</th>
                <th>Status</th>
                <th></th>
          </thead>";
    if(isset($_GET['id'])){
        $module_edit = $admin_panels->GetModule($_GET['id']);
        foreach($modules as $module){
            if($module['Modules_ID'] == $_GET['id'])
            {
                echo "<tr>
                        <form action='editModule.php' method='POST'>
                        <td>".$module_edit['Modules_name']."</td>
                        <td>".$module_edit['Modules_class']."</td>
                        <td><input id ='order' name='order' type='number' value='".$module_edit['Modules_hierarchy']."'/></td>";
                        
                        echo "<td><select name='status' id ='status'>";
                        
                        $status=array("Wyłączony","Włączony - Lewy Panel","Włączony - Prawy Panel");
                        for($i = 0; $i<=2; ++$i)
                        {
                            if($i == $module_edit['Modules_panel']){
                                echo "<option value='".$i."' selected>".$status[$i]."</option>";
                            } else {
                                echo "<option value='".$i."'>".$status[$i]."</option>";
                            }
                        }
                        
                        echo "</select></td>";
                        
                        $dir = SROOT."admin/panels/index.php?id=".$module['Modules_ID'];
                        ?>
                        <input type="hidden" name="module_id" value="<?php echo $module_edit['Modules_ID']; ?>" />
                        <input type="hidden" name="submitted_edit" value="TRUE" />
                        <?php
                        echo "<td><input type='submit' name='submit' value='Zapisz Zmiany' /></td>";
                        
                        
                        echo "</form>";
                echo "</tr>";
            }else{
                echo "<tr>
                        <td>".$module['Modules_name']."</td>
                        <td>".$module['Modules_class']."</td>
                        <td>".$module['Modules_hierarchy']."</td>";
                        switch($module['Modules_panel'])
                        {
                            case 0: echo "<td>Wyłączony</td>";
                                break;
                            case 1: echo "<td>Włączony - Lewy Panel</td>";
                                break;
                            case 2: echo "<td>Włączony - Prawy Panel</td>";
                                break;
                        }
                        $dir = SROOT."admin/panels/index.php?id=".$module['Modules_ID'];
                        echo "<td><a href=".$dir.">Edytuj</a></td>";
                echo "</tr>";
            }
        }
    
    }
    if(!isset($_GET['id'])){
        foreach($modules as $module){
            echo "<tr>
                    <td>".$module['Modules_name']."</td>
                    <td>".$module['Modules_class']."</td>
                    <td>".$module['Modules_hierarchy']."</td>";
                    switch($module['Modules_panel'])
                    {
                        case 0: echo "<td>Wyłączony</td>";
                            break;
                        case 1: echo "<td>Włączony - Lewy Panel</td>";
                            break;
                        case 2: echo "<td>Włączony - Prawy Panel</td>";
                            break;
                    }
                    $dir = SROOT."admin/panels/index.php?id=".$module['Modules_ID'];
                    echo "<td><a href=".$dir.">Edytuj</a></td>";

            echo "</tr>";
        }
    }
    echo "</table></div><br/>";
    
    
    ?>

</div>
<?php }else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
