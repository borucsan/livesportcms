<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Theme
 *
 * @author boruc-san, Takezo1115
 * @since  2012-11-01
 */
defined('_DP') or die("Direct access not allowed!");
require_once(THEMETEMP."AbstractTheme.php");

class Theme extends AbstractTheme {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    //*************************************
    //           [**METHODS**]
    //*************************************
    protected function onCascadianStyleSheetLoad(){
        ?>
        <link rel="stylesheet" type="text/css" media="all" href=<?php echo THEMEDIR."default/css/basic.css";?> />
        <link rel="stylesheet" type="text/css" media="all" href=<?php echo THEMEDIR."default/css/style.css";?> />
        <link rel="stylesheet" type="text/css" media="all" href=<?php echo THEMEDIR."default/css/admin.css";?> />
        <?php
    }
    protected function onInnerBody(){
    ?>
        <div class="page_header">
            <h3><?php echo $this->mTitle; ?></h3>
            <img src="<?php echo THEMEDIR."/default/css/images/website_logo.png";?>"/>
        </div>
        
        <div id="page_nav">
        <div id="nav">
            <ul>
                <li><a href="<?php echo SROOT."index.php";?>">Strona Główna</a></li>
                <?php 
                    if(ADMIN){
                        echo "<li><a href='".SROOT."admin/index.php'>Administracja</a>";
                            echo "<ul>";
                                echo "<li><a href='".SROOT."admin/articles/'>Artykuły</a></li>";
                                echo "<li><a href='".SROOT."admin/categories/'>Kategorie</a></li>";
                                echo "<li><a href='".SROOT."admin/modules/'>Moduły</a></li>";
                                echo "<li><a href='".SROOT."admin/panels/'>Panele</a></li>";
                                echo "<li><a href='".SROOT."admin/reports/'>Relacje</a></li>";
                                if(SUPERADMIN){ echo "<li><a href='".SROOT."admin/settings/'>Ustawienia</a></li>"; }
                                echo "<li><a href='".SROOT."admin/users/'>Użytkownicy</a></li>";
                            echo "</ul>";
                        echo "</li>";
                    }
                ?>

                <li>
                    <a href="#">Użytkownik</a>
                    <ul>
                        <li><a href="<?php echo SROOT."login.php";?>">Logowanie</a></li>
                        <li><a href="<?php echo SROOT."register.php";?>">Rejestracja</a></li>
                        <?php 
                        if(USER){
                            echo "<li><a href='".SROOT."index.php?logout=true'>Wyloguj</a></li>";
                        }?>
                    </ul>
                </li>

                <li>
                    <a href="#">Kategoria</a>
                    <ul>
                        <?php 
                            $categories = $this->GetMenuCategories();
                            foreach($categories as $category)
                            {
                                ?>
                                    <li>
                                        <a href='<?php echo SROOT."index.php?category=".$category['Categorie_ID']; ?>'>
                                         <?php echo $category['Categorie_Name'];?>
                                        </a>
                                        <ul>
                                        <?php
                                           $subcategories = $this->GetMenuSubCategories($category['Categorie_ID']);
                                           foreach($subcategories as $subcategory)
                                           {
                                               ?>
                                               <li>
                                                 <a href='<?php echo SROOT."index.php?subcategory=".$subcategory['Subcategory_ID']; ?>'>
                                                  <?php echo $subcategory['Subcategory_Name'];?>
                                                 </a>
                                               </li>
                                               <?php
                                           }
                                        ?>
                                        </ul>
                                    </li>
                                <?php
                                
                            }
                            
                        ?>
                        
                    </ul>
                </li>
                
            </ul>
        </div>
            <div id = "Timer"></div>
            
        </div>
        
        <div class="colmask threecol">
                <div class="colmid">
                    
                        <div class="colleft">
                            
                                <div class="col1">
                                        <!-- Column 1 start -->
                                        
                                        <?php echo $this->mContent?>
                                        
                                        <!-- Column 1 end -->
                                </div>
                                <div class="col2">
                                        <!-- Column 2 start -->
                                         <?php echo $this->mLeftPanel->ViewPanel(); ?>
                                        <!-- Column 2 end -->
                                </div>
                                <div class="col3">
                                        <!-- Column 3 start -->
                                        <?php echo $this->mRightPanel->ViewPanel(); ?>
                                        <!-- Column 3 end -->
                                </div>
                        </div>
                </div>
        </div>
    <?php
    }
    protected function onJavaScriptLoad(){
        ?>
            <script type="text/javascript" src=<?php echo THEMEDIR."default/JS/date_time.js";?> ></script>
            <noscript> </noscript>
        <?php
    }
}
?>
