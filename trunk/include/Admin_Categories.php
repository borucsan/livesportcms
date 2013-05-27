<?php
/**
 * Description of Admin_Categories
 *
 * @author Takezo1115
 */
class Admin_Categories {
    public function __construct(){}
    public function GetSubCategory($subcategory_id){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT Subcategory_Name, Subcategory_ID, SUB.Categorie_ID, Categorie_Name 
              FROM ".DB_SUBCATEGORIES." SUB JOIN ".DB_CATEGORIES." CAT
              ON SUB.Categorie_ID = CAT.Categorie_ID
              WHERE Subcategory_ID = ".$subcategory_id." LIMIT 1";
        $r = $db->query($q);
        $row = $r->fetch_array();
        return $row;
    }
    public function GetCategory($category_id){
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_CATEGORIES." WHERE Categorie_ID = ".$category_id." LIMIT 1";
        $r = $db->query($q);
        $row = $r->fetch_array();
        return $row;
    }
    public function GetMenuCategories()
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT Categorie_ID, Categorie_Name, Categorie_Live_Template 
            FROM ".DB_CATEGORIES." ORDER BY Categorie_ID DESC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function GetMenuSubCategories()
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT Categorie_Name, Categorie_Live_Template, Subcategory_Name, Subcategory_ID 
              FROM ".DB_CATEGORIES." CAT 
              JOIN ".DB_SUBCATEGORIES." SUB 
                     ON CAT.Categorie_ID = SUB.Categorie_ID 
              ORDER BY Subcategory_ID DESC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function getDirectoryList ($directory) 
    {
        $results = array();
        $handler = opendir($directory);
        while ($file = readdir($handler)) {
           if ($file != "." && $file != ".." && $file != "index.html" && $file != "template") {
               $file = str_ireplace(".php", "", $file);
               $results[] = $file;
            }
        }
        closedir($handler);
        return $results;
    }
    public function AddCategory($category_name, $category_class) {
        $category_name = Utls::StipUserInput($category_name);
        $category_class = Utls::StipUserInput($category_class);
        
        $this->CheckAddData($category_name, $category_class);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "INSERT INTO ".DB_CATEGORIES." (Categorie_Name, Categorie_Live_Template)
              VALUES('".$category_name."', '".$category_class."')";
        $r = $db->query($q);
    }
    private function CheckAddData($category_name, $category_class) {
        if(strlen($category_name) > 25){
            throw new AddCategoryException("Category Name is too long", AddCategoryException::NAME_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Categorie_ID FROM ".DB_CATEGORIES." WHERE Categorie_Name = '".$category_name."'";
        $r = $db->query($q);
        if($r->num_rows == 1){
            throw new AddCategoryException("Category Name already used", AddCategoryException::NAME_USED_ERROR);
        }
        if(strlen($category_class) > 25){
            throw new AddCategoryException("Category Class is too long", AddCategoryException::CLASS_SIZE_ERROR);
        }
    }
    public function AddSubCategory($subcategory_name, $category_id) {
        $subcategory_name = Utls::StipUserInput($subcategory_name);
        $category_id = Utls::StipUserInput($category_id);
        
        $this->CheckAddSubData($subcategory_name, $category_id);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "INSERT INTO ".DB_SUBCATEGORIES." (Subcategory_Name, Categorie_ID)
              VALUES('".$subcategory_name."', ".$category_id.")";
        $r = $db->query($q);
    }
    private function CheckAddSubData($subcategory_name, $category_id) {
        if(strlen($subcategory_name) > 25){
            throw new AddSubCategoryException("Subcategory name is too long", AddSubCategoryException::NAME_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Categorie_ID FROM ".DB_CATEGORIES." WHERE Categorie_ID = ".$category_id."";
        $r = $db->query($q);
        if($r->num_rows != 1){
            throw new AddSubCategoryException("Category not found in database", AddSubCategoryException::CATEGORY_NOT_FOUND_ERROR);
        }
        $q = "SELECT Subcategory_ID FROM ".DB_SUBCATEGORIES." WHERE Subcategory_Name = '".$subcategory_name."'";
        $r = $db->query($q);
        if($r->num_rows == 1){
            throw new AddSubCategoryException("This SubCategory name is already used", AddSubCategoryException::NAME_USED_ERROR);
        }
        
    }
    public function EditCategory($category_id, $category_name, $category_class)
    {
        $category_name = Utls::StipUserInput($category_name);
        $category_class = Utls::StipUserInput($category_class);
        $category_id = Utls::StipUserInput($category_id);
        $this->CheckEditCategoryData($category_id, $category_name, $category_class);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "UPDATE ".DB_CATEGORIES." 
              SET Categorie_Name='".$category_name."', Categorie_Live_Template='".$category_class."' 
              WHERE Categorie_ID=".$category_id."";
        $r = $db->query($q);
        if(!$r)
        {
            throw new EditCategoryException("Category database error", EditCategoryException::CATEGORY_NOT_CHANGED);
        }
    }
    public function EditSubCategory($subcategory_id, $subcategory_name, $category_id)
    {
        $subcategory_name = Utls::StipUserInput($subcategory_name);
        $subcategory_id = Utls::StipUserInput($subcategory_id);
        $category_id = Utls::StipUserInput($category_id);
        $this->CheckEditSubCategoryData($subcategory_id, $subcategory_name, $category_id);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "UPDATE ".DB_SUBCATEGORIES." 
              SET Subcategory_Name='".$subcategory_name."', Categorie_ID=".$category_id." 
              WHERE Subcategory_ID=".$subcategory_id."";
        $r = $db->query($q);
        if(!$r)
        {
            throw new EditSubCategoryException("SubCategory database error", EditSubCategoryException::SUBCATEGORY_NOT_CHANGED);
        }
    }
    private function CheckEditCategoryData($category_id, $category_name, $category_class) {
        if(!is_numeric($category_id)) {
            throw new EditCategoryException("Category ID is detected as non integer value", EditCategoryException::CATEGORY_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Categorie_ID FROM ".DB_CATEGORIES." WHERE Categorie_ID = ".$category_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditCategoryException("Category not found in database", EditCategoryException::CATEGORY_NOT_FOUND);
            }
        }
        
        if(strlen($category_name) > 25){
            throw new EditCategoryException("Category name is too long", EditCategoryException::CATEGORY_NAME_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Categorie_ID FROM ".DB_CATEGORIES." WHERE Categorie_Name = '".$category_name."'";
        $r = $db->query($q);
        if($r)
        {
            if($r->num_rows == 1){
                throw new EditCategoryException("Category name already in use", EditCategoryException::CATEGORY_NAME_USED_ERROR);
            }
        }
        if(strlen($category_class) > 25){
            throw new EditCategoryException("Category Class is too long", EditCategoryException::CLASS_SIZE_ERROR);
        }
    }
    private function CheckEditSubCategoryData($subcategory_id, $subcategory_name, $category_id) {
        if(!is_numeric($subcategory_id)) {
            throw new EditSubCategoryException("SubCategory ID is detected as non integer value", EditSubCategoryException::SUBCATEGORY_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Subcategory_ID FROM ".DB_SUBCATEGORIES." WHERE Subcategory_ID = ".$subcategory_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditSubCategoryException("SubCategory not found in database", EditSubCategoryException::SUBCATEGORY_NOT_FOUND);
            }
        }
        if(!is_numeric($category_id)) {
            throw new EditSubCategoryException("Category ID is detected as non integer value", EditSubCategoryException::CATEGORY_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Categorie_ID FROM ".DB_CATEGORIES." WHERE Categorie_ID = ".$category_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditSubCategoryException("Category not found in database", EditSubCategoryException::CATEGORY_NOT_FOUND);
            }
        }
        
        if(strlen($subcategory_name) > 25){
            throw new EditSubCategoryException("SubCategory name is too long", EditSubCategoryException::SUBCATEGORY_NAME_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Subcategory_ID FROM ".DB_SUBCATEGORIES." WHERE Subcategory_Name = '".$subcategory_name."'";
        $r = $db->query($q);
        if($r)
        {
            if($r->num_rows == 1){
                throw new EditSubCategoryException("SubCategory name already in use", EditSubCategoryException::SUBCATEGORY_NAME_USED_ERROR);
            }
        }
    }
}
class AddCategoryException extends RuntimeException
{
    const NAME_SIZE_ERROR          = 1;
    const NAME_USED_ERROR          = 2;
    const CLASS_SIZE_ERROR          = 3;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class AddSubCategoryException extends RuntimeException
{
    const NAME_SIZE_ERROR          = 1;
    const NAME_USED_ERROR          = 2;
    const CATEGORY_NOT_FOUND_ERROR = 3;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class EditCategoryException extends RuntimeException
{
    const CATEGORY_ID_NUMBER_ERROR    = 1;
    const CATEGORY_NOT_FOUND          = 2;
    const CATEGORY_NAME_SIZE_ERROR    = 3;
    const CATEGORY_NAME_USED_ERROR    = 4;
    const CATEGORY_NOT_CHANGED        = 5;
    const CLASS_SIZE_ERROR            = 6;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class EditSubCategoryException extends RuntimeException
{
    const SUBCATEGORY_ID_NUMBER_ERROR    = 1;
    const SUBCATEGORY_NOT_FOUND          = 2;
    const SUBCATEGORY_NAME_SIZE_ERROR    = 3;
    const SUBCATEGORY_NAME_USED_ERROR    = 4;
    const SUBCATEGORY_NOT_CHANGED        = 5;
    const CATEGORY_ID_NUMBER_ERROR    = 6;
    const CATEGORY_NOT_FOUND          = 7;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
?>
