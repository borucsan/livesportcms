<?php
/**
 * Description of AbstractTheme
 *
 * @author boruc-san
 * @since  2012-10-31
 */
defined('_DP') or die("Direct access not allowed!");
abstract class AbstractTheme {
    //*************************************
    //           [**CONSTANTS**]
    //*************************************
    //*************************************
    //           [**CLASS FILEDS**]
    //*************************************
    
    protected $mContent = "";
    protected $mPanel;
    protected $mTitle = "";
    protected $mSubTitle = "";
    protected $mJS = array();
    protected $mCSS = array();
    protected $mLeftPanel;
    protected $mRightPanel;

    //*************************************
    //           [**CONSTRUCTOR**]
    //*************************************
    public function __construct($pTitle) {
        $this->mTitle = $pTitle;
    }

    //*************************************
    //           [**ABSTRACT METHODS**]
    //*************************************
    
    abstract protected function onCascadianStyleSheetLoad();
    abstract protected function onJavaScriptLoad();
    abstract protected function onInnerBody();
    //*************************************
    //           [**METHODS**]
    //*************************************
    
    protected function onHeaderClose() {}
    protected function onFooter() {}
  
    /**
     * Metoda pobiera zawartość bufora strony i kasuje go.
     */
    public final function flushContent() {
        $this->mContent = ob_get_contents();
        ob_end_clean();
    }
    /**
     * Metoda ustawia tytuł strony.
     * @param string $title Tekst tytułu strony. 
     */
    public final function setTitle($pTitle) {
        $this->mTitle = $pTitle;
    }
    /**
     * Metoda dodaje tekst bezpośrednio do tytułu.
     * @param string $addon Tekst dodawany do tytułu. 
     */
    public final function addToTitle($pAddon) {
        $this->mTitle .= $pAddon;
    }
    /**
     * Sets the page subtitle.
     * @param string $title Tekst tytułu strony. 
     */
    public final function setSubTitle($pSubTitle) {
        $this->mSubTitle = $pSubTitle;
    }
    /**
     * Adds text to subtitle.
     * @param string $addon Tekst dodawany do tytułu. 
     */
    public final function addToSubTitle($pAddon) {
        $this->mSubTitle .= $pAddon;
    }
    /**
     * Metoda dodaje znaczniki z skryptami do strony.
     * @param array, string $js
     */
    public final function addJS($pJS) {
        if(is_array($pJS)){
            array_merge($this->mJS, $pJS);
        }
        else if(is_string($pJS)) array_push ($this->mJS, $pJS);
    }
    /**
     * Metoda dodaje znaczniki z stylami do strony.
     * @param array, string $css
     */
    public final function addCSS($pCSS) {
        if(is_array($pCSS)){
            array_merge($this->mJS, $pCSS);
        }
        else if(is_string($pCSS)) array_push ($this->mJS, $pCSS);
    }
    /**
     * Metoda z tekstem licencji strony.
     */
    private final function Copyright() {
        ?>
            <div id = "footer"><span><strong>Powered by <a href="#">LiveSportCMS</a> <?php echo WERSJA; ?></strong> Copyright &COPY; 2012 Opublikowano na licencji GNU GPL v3</span></div>
			
        <?php
    }
    /**
     * Metoda od paneli.
     */
    public final function setPanels($pLeft, $pRight) {
       $this->mLeftPanel = $pLeft;
       $this->mRightPanel = $pRight;
   }
    /**
     * Metoda generująca właściwą strone
     */
    public final function generatePage(){
        ?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo ($this->mSubTitle != "" ? $this->mSubTitle." - " : "").$this->mTitle; ?></title>
        <?php foreach ($this->mCSS as $value) {
                    echo $value."\n";
                }    
            ?>
        <?php   $this->onCascadianStyleSheetLoad(); ?>
        <?php   $this->onJavaScriptLoad(); ?>
         <?php foreach ($this->mJS as $value) {
                    echo $value."\n";
                }    
            ?>
        <?php   $this->onHeaderClose(); ?>
    </head>
    <body>
        <?php   $this->onInnerBody(); ?>
        <div>
            <?php $this->onFooter(); 
                $this->Copyright();
            ?>
        </div>
    </body>
</html>
    <?php
    }
    /*
     * Metody generujące menu
     */
    protected final function GetMenuCategories()
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
    protected final function GetMenuSubCategories($parent_id)
    {
        $db = DB::getDB();
        $db->set_charset("utf8");
        $q = "SELECT Subcategory_Name, Subcategory_ID 
              FROM ".DB_SUBCATEGORIES."
              WHERE Categorie_ID = ".$parent_id."
              ORDER BY Subcategory_ID DESC";
        $r = $db->query($q);
        $data = new ArrayObject();
        while($row = $r->fetch_array())
        {
            $data[] = $row;
        }
        return $data;
    }
}
?>