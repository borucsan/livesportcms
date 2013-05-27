<?php
/**
 * Description of Admin_Articles
 *
 * @author Takezo1115
 */
class Admin_Articles {
   
    public function __construct(){}
    
    /*ARTICLES*/
    public function GetMenuArticles(){
        $db = DB::getDB();
        //$db->query("SET NAMES 'utf8'");
        $db->set_charset("utf8");
        $q = "SELECT Article_ID, Article_Title FROM ".DB_ARTICLES." ORDER BY Article_ID DESC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function GetArticle($article_id){
        $db = DB::getDB();
        //$db->query("SET NAMES 'utf8'");
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_ARTICLES." WHERE Article_ID = ".$article_id." LIMIT 1";
        $r = $db->query($q);;
        $row = $r->fetch_array();
        return $row;
    }
    public function GetArticles(){
        $db = DB::getDB();
        //$db->query("SET NAMES 'utf8'");
        $db->set_charset("utf8");
        $q = "SELECT * FROM ".DB_ARTICLES."";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
    public function AddArticle($author_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image, $live_id) {
        $article_title = Utls::StipUserInput($article_title);
        $article_brief = Utls::StipUserInput($article_brief);
        $article_text = Utls::StipUserInput($article_text);
        $article_image = Utls::StipUserInput($article_image);
        $subcategory_id = Utls::StipUserInput($subcategory_id);
        $commentary_id = 0;
        
        $this->CheckAddData($author_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image, $live_id);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "INSERT INTO ".DB_ARTICLES." (User_ID, Subcategory_ID, Commentary_ID, Live_ID, Article_Title, Article_Creation_Date, Article_Brief, Article_Text, Article_Image)
              VALUES(".$author_id.", ".$subcategory_id.", ".$commentary_id.", ".$live_id.", '".$article_title."', NOW(), '".$article_brief."', '".$article_text."', '".$article_image."')";
        $r = $db->query($q);
    }
    public function EditArticle($article_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image)
    {
        $article_title = Utls::StipUserInput($article_title);
        $article_brief = Utls::StipUserInput($article_brief);
        $article_text = Utls::StipUserInput($article_text);
        $article_image = Utls::StipUserInput($article_image);
        $article_id = Utls::StipUserInput($article_id);
        $subcategory_id = Utls::StipUserInput($subcategory_id);
        $this->CheckEditData($article_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image, 0);
        $db = DB::getDB();
        $db->query("SET NAMES 'utf8'");
        $q = "UPDATE ".DB_ARTICLES." 
              SET Article_Brief='".$article_brief."', Article_Text='".$article_text."', Article_Title='".$article_title."', 
                  Subcategory_ID=".$subcategory_id." 
              WHERE Article_ID=".$article_id."";
        $r = $db->query($q);
        if(!$r)
        {
            throw new EditArticleException("Article not edited", EditArticleException::ARTICLE_NOT_CHANGED);
        }
    }
    public function DeleteArticle($article_id) {
        $db = DB::getDB();
        $q = "SELECT Article_ID FROM ".DB_ARTICLES." WHERE Article_ID = ".$article_id."";
        $r = $db->query($q);
        if($r->num_rows != 1){
                throw new DeleteArticleException("Article not found in database", DeleteArticleException::ARTICLE_NOT_FOUND);
        }
        $q = "DELETE FROM ".DB_ARTICLES." WHERE Article_ID = ".$article_id."";
        $r = $db->query($q);
        if($r->affected_rows < 0){
            throw new DeleteArticleException("Article not deleted", DeleteArticleException::ARTICLE_NOT_DELETED);
        }
    }
    private function CheckAddData($author_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image, $live_id) {
        if(!is_numeric($author_id)) {
            throw new AddArticleException("Author ID is detected as non integer value", AddArticleException::AUTHOR_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT User_ID FROM ".DB_USERS." WHERE User_ID = ".$author_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new AddArticleException("Author not found in database", AddArticleException::AUTHOR_NOT_FOUND);
            }
        }
        if(!is_numeric($subcategory_id)) {
            throw new AddArticleException("SubCategory ID is detected as non integer value", AddArticleException::SUBCATEGORY_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Subcategory_ID FROM ".DB_SUBCATEGORIES." WHERE Subcategory_ID = ".$subcategory_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new AddArticleException("SubCategory not found in database", AddArticleException::SUBCATEGORY_NOT_FOUND);
            }
        }
        /*
        if(!empty($live_id)){
            if(!is_int($live_id)) {
                throw new AddArticleException("Live ID is detected as non integer value", 5);
            } else {
                $db = DB::getDB();
                $q = "SELECT Live_ID FROM ".DB_LIVE_STREAMS." WHERE Live_ID = ".$live_id."";
                $r = $db->query($q);
                if($r->num_rows != 1){
                    throw new AddArticleException("Live not found in database", 6);
                }
            }
        }
         * 
         */
        if(strlen($article_title) > 48){
            throw new AddArticleException("Title is too long", AddArticleException::TITLE_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Article_ID FROM ".DB_ARTICLES." WHERE Article_Title = ".$article_title."";
        $r = $db->query($q);
        if($r->num_rows == 1){
            throw new AddArticleException("Article Title already used", AddArticleException::TITLE_USED_ERROR);
        }
        if(strlen($article_brief) > 512){
            throw new AddArticleException("Article Brief is too long", AddArticleException::BRIEF_SIZE_ERROR);
        }
    }
    private function CheckEditData($article_id, $subcategory_id, $article_title, $article_brief, $article_text, $article_image) {
        if(!is_numeric($article_id)) {
            throw new EditArticleException("Article ID is detected as non integer value", EditArticleException::ARTICLE_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Article_ID FROM ".DB_ARTICLES." WHERE Article_ID = ".$article_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditArticleException("Article not found in database", EditArticleException::ARTICLE_NOT_FOUND);
            }
        }
        if(!is_numeric($subcategory_id)) {
            throw new EditArticleException("SubCategory ID is detected as non integer value", EditArticleException::SUBCATEGORY_ID_NUMBER_ERROR);
        } else {
            $db = DB::getDB();
            $q = "SELECT Subcategory_ID FROM ".DB_SUBCATEGORIES." WHERE Subcategory_ID = ".$subcategory_id."";
            $r = $db->query($q);
            if($r->num_rows != 1){
                throw new EditArticleException("SubCategory not found in database", EditArticleException::SUBCATEGORY_NOT_FOUND);
            }
        }
        
        if(strlen($article_title) > 48){
            throw new EditArticleException("Title is too long", EditArticleException::TITLE_SIZE_ERROR);
        }
        $db = DB::getDB();
        $q = "SELECT Article_ID FROM ".DB_ARTICLES." WHERE Article_Title = ".$article_title."";
        $r = $db->query($q);
        if($r)
        {
            if($r->num_rows == 1){
                throw new EditArticleException("Article Title already used", EditArticleException::TITLE_USED_ERROR);
            }
        }
        if(strlen($article_brief) > 512){
            throw new EditArticleException("Brief is too long", EditArticleException::BRIEF_SIZE_ERROR);
        }
    }
    
}
class AddArticleException extends RuntimeException
{
    const AUTHOR_ID_NUMBER_ERROR    = 1;
    const AUTHOR_NOT_FOUND          = 2;
    const SUBCATEGORY_ID_NUMBER_ERROR  = 3;
    const SUBCATEGORY_NOT_FOUND        = 4;
    const LIVE_ID_NUMBER_ERROR      = 5;
    const LIVE_NOT_FOUND            = 6;
    const TITLE_SIZE_ERROR          = 7;
    const TITLE_USED_ERROR          = 8;
    const BRIEF_SIZE_ERROR          = 9;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class EditArticleException extends RuntimeException
{
    const ARTICLE_ID_NUMBER_ERROR    = 1;
    const ARTICLE_NOT_FOUND          = 2;
    const SUBCATEGORY_ID_NUMBER_ERROR  = 3;
    const SUBCATEGORY_NOT_FOUND        = 4;
    const TITLE_SIZE_ERROR          = 5;
    const TITLE_USED_ERROR          = 6;
    const ARTICLE_NOT_CHANGED       = 7;
    const BRIEF_SIZE_ERROR          = 8;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
class DeleteArticleException extends RuntimeException
{
    const ARTICLE_NOT_FOUND    = 1;
    const ARTICLE_NOT_DELETED  = 2;
    
    public function __construct($message, $code, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}

?>
