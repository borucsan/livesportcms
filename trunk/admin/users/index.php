<?php
define('_DP', 1);
require_once("../../include/core.php");
require_once(INCLUDEDIR."Admin_Users.php");
$document->setSubTitle("Użytkownicy - Administracja");
?>
<div class="main_panel">
    <div class ="title_bar">Administracja - Użytkownicy</div>
<?php if(ADMIN){
    $admin_users = new Admin_Users();
    $users = $admin_users->GetUsers();
    if (isset($_GET['edit_error'])) {     
        switch($_GET['edit_error'])
        {
            case 1: echo "Imię użytkownika max 20 znaków";
                break;
            case 2: echo "Nazwisko użytkownika max 20 znaków";
                break;
            case 3: echo "Niepoprawny email";
                break;
            case 4: echo "Poziom użytkownika nie jest w zakresie";
                break;
            case 5: echo "Status użytkownika nie jest w zakresie";
                break;
            case 6: echo "Błąd bazy danych, nie możnabyło zmienić rekordu";
                break;
            case 7: echo "Użytkownicy nie zostali usunięci";
                break;
        }
    }
    if ( isset($_GET['msg'])) {
        if($_GET['msg'] == "edited"){
            echo "Użytkownik został uaktualniony<br/>";
        }
        if($_GET['msg'] == "deleted"){
            echo "Użytkownicy zostali usunięci<br/>";
        }
    }
    
    echo "<div><table class='admin_users_table'>";
    echo "<thead>
                <th>Login</th>
                <th>Email</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Poziom Dostępu</th>
                <th>Status</th>
                <th></th>
          </thead>";
    if(isset($_GET['id'])){
        $user_edit = $admin_users->GetUser($_GET['id']);
        foreach($users as $user){
            if($user['User_ID'] == $_GET['id'])
            {
                echo "<tr>
                        <form class = 'main_form' action='editUser.php' method='POST'>
                        <td>".$user_edit['User_Login']."</td>
                        <td><input id ='user_mail' name='user_mail' type='text' value='".$user_edit['User_Email']."'/></td>
                        <td><input id ='user_name' name='user_name' type='text' size='5' value='".$user_edit['User_Name']."'/></td>
                        <td><input id ='user_surname' name='user_surname' type='text' size='5' value='".$user_edit['User_Surname']."'/></td>
                      ";
                        
                        if(SUPERADMIN)
                        {
                            echo "<td><select name='user_level' id ='user_level'>";
                            $status=array("Gość","Użytkownik","Administrator","Super Administrator");
                            for($i = 0; $i<=3; ++$i)
                            {
                                if($i == $user_edit['User_Level']){
                                    echo "<option value='".$i."' selected>".$status[$i]."</option>";
                                } else {
                                    echo "<option value='".$i."'>".$status[$i]."</option>";
                                }
                            }

                            echo "</select></td>";

                        }
                        else
                        {
                            switch($user_edit['User_Level'])
                            {
                                case 0: echo "<td>Gość</td>";
                                    break;
                                case 1: echo "<td>Użytkownik</td>";
                                    break;
                                case 2: echo "<td>Administrator</td>";
                                    break;
                                case 3: echo "<td>Super Administrator</td>";
                                    break;
                            }
                            ?>
                            <input type="hidden" name="user_level" value="<?php echo $user_edit['User_Level']; ?>" />
                        <?php
                        }
                                               
                        echo "<td><select name='user_status' id ='user_status'>";
                        
                        $stan=array("Nie Aktywny","Aktywny","Zablokowany","Do Usunięcia");
                        for($i = 0; $i<=3; ++$i)
                        {
                            if($i == $user_edit['User_Status']){
                                echo "<option value='".$i."' selected>".$stan[$i]."</option>";
                            } else {
                                echo "<option value='".$i."'>".$stan[$i]."</option>";
                            }
                        }
                        echo "</select></td>";
                        
                        $dir = SROOT."admin/users/index.php?id=".$user['User_ID'];
                        ?>
                        <input type="hidden" name="user_id" value="<?php echo $user_edit['User_ID']; ?>" />
                        <input type="hidden" name="submitted_edit" value="TRUE" />
                        <?php
                        echo "<td><input type='submit' name='submit' value='Zapisz' /></td>";
                        
                        
                        echo "</form>";
                echo "</tr>";
            }else{
                echo "<tr>
                    <td>".$user['User_Login']."</td>
                    <td>".$user['User_Email']."</td>
                    <td>".$user['User_Name']."</td>
                    <td>".$user['User_Surname']."</td>";
                    switch($user['User_Level'])
                    {
                        case 0: echo "<td>Gość</td>";
                            break;
                        case 1: echo "<td>Użytkownik</td>";
                            break;
                        case 2: echo "<td>Administrator</td>";
                            break;
                        case 3: echo "<td>Super Administrator</td>";
                            break;
                    }
                    switch($user['User_Status'])
                    {
                        case 0: echo "<td>Nie aktywny</td>";
                            break;
                        case 1: echo "<td>Aktywny</td>";
                            break;
                        case 2: echo "<td>Zawieszony</td>";
                            break;
                        case 3: echo "<td>Do Usunięcia</td>";
                            break;
                    }
                    $dir = SROOT."admin/users/index.php?id=".$user['User_Id'];
                    echo "<td><a href=".$dir.">Edytuj</a></td>";
            echo "</tr>";
            }
        }
    
    }
    if(!isset($_GET['id'])){
        foreach($users as $user){
            echo "<tr>
                    <td>".$user['User_Login']."</td>
                    <td>".$user['User_Email']."</td>
                    <td>".$user['User_Name']."</td>
                    <td>".$user['User_Surname']."</td>";
                    switch($user['User_Level'])
                    {
                        case 0: echo "<td>Gość</td>";
                            break;
                        case 1: echo "<td>Użytkownik</td>";
                            break;
                        case 2: echo "<td>Administrator</td>";
                            break;
                        case 3: echo "<td>Super Administrator</td>";
                            break;
                    }
                    switch($user['User_Status'])
                    {
                        case 0: echo "<td>Nie aktywny</td>";
                            break;
                        case 1: echo "<td>Aktywny</td>";
                            break;
                        case 2: echo "<td>Zawieszony</td>";
                            break;
                        case 3: echo "<td>Do Usunięcia</td>";
                            break;
                    }
                    $dir = SROOT."admin/users/index.php?id=".$user['User_ID'];
                    echo "<td><a href=".$dir.">Edytuj</a></td>";
            echo "</tr>";
        }
    }
    echo "</table></div><br/>";
?>
  <form action='deleteUsers.php' method='POST'>
      <input type="hidden" name="submitted_delete" value="TRUE" />
      <input type="submit" name="submit" value="Usuń Użytkowników" />
  </form>
<br/>
</div>
<?php }else { 
    Utls::Redirect(SROOT."index.php"); 
    }
require_once(THEMETEMP."engine.php");
?>
