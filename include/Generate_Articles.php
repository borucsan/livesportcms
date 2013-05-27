<?php
/**
 * Description of Generate_Articles
 *
 * @author Takezo1115
 */
final class Generate_Articles {
    private function __construct(){}
    public static function GetArticles($subcategory_id, $category_id){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_ARTICLES."";
        if($subcategory_id == 0)
        {
            if($category_id == 0)
            {
                $q = "SELECT ART.Article_ID, ART.Article_Title, ART.Article_Brief, ART.Article_Text, ART.Article_Creation_Date, ART.Article_Image, 
                            USR.User_Name, USR.User_Surname, USR.User_Login,
                            SUB.Subcategory_Name, CAT.Categorie_Name
                    FROM ".DB_ARTICLES." ART 
                    JOIN ".DB_USERS." USR ON ART.User_ID = USR.User_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON ART.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID ORDER BY ART.Article_ID DESC";
            }
            else
            {
                $q = "SELECT ART.Article_ID, ART.Article_Title, ART.Article_Brief, ART.Article_Text, ART.Article_Creation_Date, ART.Article_Image, 
                            USR.User_Name, USR.User_Surname, USR.User_Login,
                            SUB.Subcategory_Name, CAT.Categorie_Name
                    FROM ".DB_ARTICLES." ART 
                    JOIN ".DB_USERS." USR ON ART.User_ID = USR.User_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON ART.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID 
                    WHERE SUB.Categorie_ID = ".$category_id." ORDER BY ART.Article_ID DESC";
            }
        }
        else
        {
            $q = "SELECT ART.Article_ID, ART.Article_Title, ART.Article_Brief, ART.Article_Text, ART.Article_Creation_Date, ART.Article_Image, 
                            USR.User_Name, USR.User_Surname, USR.User_Login,
                            SUB.Subcategory_Name, CAT.Categorie_Name
                    FROM ".DB_ARTICLES." ART 
                    JOIN ".DB_USERS." USR ON ART.User_ID = USR.User_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON ART.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID 
                    WHERE ART.Subcategory_ID = ".$subcategory_id." ORDER BY ART.Article_ID DESC";
        }
        $r = $db->query($q);
        if(!$r){echo $q;}
        
        $data = new ArrayObject();
        if($r)
        {
            while($row = $r->fetch_array())
            {
                $data[] = $row;
            }
        }
        /*
        $my_row = array();
        $my_data = new ArrayObject();
        foreach($data as $article){
            $keys = array('Article_Title', 'Article_Text', 'Article_Creation_Date', 'Category_ID', 'User_ID');
            $author = Generate_Articles::GetAuthor($article['User_ID']);
            $values = array($article['Article_Title'], $article['Article_Text'], $article['Article_Creation_Date'], 'Piłka', $author);
            $my_row = array_combine($keys, $values);
            
            $my_data[] = $my_row;
        }
         * 
         */
        return $data;
    }
    
    public static function GetArticle($id){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT ART.Article_ID, ART.Article_Title, ART.Article_Brief, ART.Article_Text, ART.Article_Creation_Date, ART.Article_Image, 
                            USR.User_Name, USR.User_Surname, USR.User_Login,
                            SUB.Subcategory_Name, CAT.Categorie_Name
                    FROM ".DB_ARTICLES." ART 
                    JOIN ".DB_USERS." USR ON ART.User_ID = USR.User_ID
                    JOIN ".DB_SUBCATEGORIES." SUB ON ART.Subcategory_ID = SUB.Subcategory_ID
                    JOIN ".DB_CATEGORIES." CAT ON SUB.Categorie_ID = CAT.Categorie_ID 
                        WHERE Article_ID=".$id." LIMIT 1";
        $r = $db->query($q);
        if(!$r){echo $q;}
        
        if($r)
        {
            $row = $r->fetch_array();
            
        }
        return $row;
    }
    private static function GetAuthor($user_id){
        $user_id = (int)$user_id;
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT User_Login, User_Name, User_Surname FROM ".DB_USERS." WHERE User_ID = ".$user_id." LIMIT 1";
        $r = $db->query($q);
        if($r->num_rows != 1)
        {
            return "Brak autora";
        }
        else
        {
            $row = $r->fetch_array();
            if(!empty($row['User_Name']) && !empty($row['User_Surname'])){
                $author = $row['User_Name']." ".$row['User_Surname'];
                return $author;
            }
            else
            {
                return $row['User_Login'];
            }
        }
    }
}

?>
