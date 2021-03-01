<?php
    //HTML 4.01 Strict  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    //HTML 4.01 Transitional  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    //HTML 4.01 Frameset  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
    //XHTML 1.0 Strict  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    //XHTML 1.0 Transitional  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    //XHTML 1.0 Frameset  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
    //XHTML 1.1  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

    class HtmlDocument extends TBaseWebComponentContainer{

        public $Url;
        public $DOCTYPE; 

        private $isXHTML = false;
        public $xmlns; //value http://www.w3.org/1999/xhtml Specifies the namespace to use (only for XHTML documents!)
        public $dir;    //value rtl ltr Specifies the text direction for the content in an element
        public $lang;   //Specifies a language code for the content in an element
        //xml:lang Specifies a language code for the content in an element, in XHTML documents

        //xml:lang language_code Specifies a language code for the content in an element, in XHTML documents

        function __construct($aUrl = null,$isXHTML =true,$Version='1.0',$DTDType ='Strict',$aName=null) {

            $this->TagName = 'html';        
            $this->Url = $aUrl;
            $this->isXHTML = $isXHTML;

            if ($isXHTML){

                if ($Version=='1.0'){

                    switch ($DTDType){
                        case 'Strict':
                            $this->DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                            break;                        
                        case 'Transitional':
                            $this->DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                            break;
                        case 'Frameset':
                            $this->DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                            break;
                        default:

                    }

                } else if ($Version == '1.1'){
                        $this->DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
                    }

            } else {

                if ($Version=='4.01'){

                    switch ($DTDType){
                        case 'Strict':
                            $this->DOCTYPE = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
                            break;
                        case 'Transitional':
                            $this->DOCTYPE = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                            break;
                        case 'Frameset':
                            $this->DOCTYPE = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
                            break;
                        default:

                    }
                }

            }

            parent::__construct($aName,$aId,$aClass,$aTitle);

            //$this->InitializeComponent();

        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }


        public function createTagParams(){   

            $theParams = array();
            if($this->isXHTML){ (!empty($this->xmlns)) ? $theParams['xmlns'] = $this->xmlns : null; }
            (!empty($this->dir)) ? $theParams['dir'] = $this->dir : null;
            if($this->isXHTML){ 
                (!empty($this->lang)) ? $theParams['xml:lang'] = $this->lang : null;
            } else {
                (!empty($this->lang)) ? $theParams['lang'] = $this->lang : null;
            }

            return $theParams;
        }


        public function onShow(){

            echo $this->DOCTYPE."\r\n";
            echo $this->openTag($this->TagName,$this->createTagParams());

            $counter = 0;

            foreach ($this as $component){

                if (isset($this->FOnBeginIterate)) {$p = array($counter,$component); $this->DispachEvent($this->FOnBeginIterate,$p);}

                $component->Show();

                if (isset($this->FOnEndIterate)) $this->DispachEvent($this->FOnEndIterate,$component);


                $counter++;


            }

            echo $this->closeTag($this->TagName);


        }

    }

    // <meta>

    class Title extends TBaseWebComponent{

        public $Title;
        public $dir;
        public $lang;

        function __construct($aTitle=null,$aName=null) {

            $this->Title = $aTitle;
            $this->TagName = 'title';
            parent::__construct($aName);

        }

        public function setTitle($aTitle,$append=false){
            if ($append === true){
                $this->Title .= $aTitle;
            } else {
                $this->Title = $aTitle;
            }
        }


        public function createTagParams(){   

            $theParams = array();

            (!empty($this->dir)) ? $theParams['dir'] = $this->dir : null;
            if($this->isXHTML){  //Controllare se HTML padre è XHTML
                (!empty($this->lang)) ? $theParams['xml:lang'] = $this->lang : null;
            } else {
                (!empty($this->lang)) ? $theParams['lang'] = $this->lang : null;
            }
            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        public function onShow(){


            echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->Title);

            //echo $this->closeTag($this->TagName);


        }


    }

    class ExternalLink extends TBaseWebComponent {

        public $charset;    //Specifies the character encoding of the linked document
        public $href;       //Specifies the location of the linked document
        public $hreflang;   //Specifies the language of the text in the linked document
        public $media;      //Specifies on what device the linked document will be displayed  {screen,tty,tv,projection,handheld,print,braille,aural,all}
        public $rel;        //Specifies the relationship between the current document and the linked document {alternate,appendix,bookmark,chapter,contents,copyright,glossary,help,home,index,next,prev,section,start,stylesheet,subsection}
        public $rev;        //Specifies the relationship between the linked document and the current document {alternate,appendix,bookmark,chapter,contents,copyright,glossary,help,home,index,next,prev,section,start,stylesheet,subsection}
        public $target;     //Specifies where the linked document is to be loaded {     _blank _self _top _parent frame_name}  
        public $type;       //Specifies the MIME type of the linked document MIME_type

        function __construct($aUrl,$aMedia,$aType,$aTarget=null,$aName=null) {

            //$this->charset
            $this->href = $aUrl;
            //hreflang
            $this->media = $aMedia;
            //$this->rel
            //$this->rev
            $this->target = $aTarget;
            $this->type = $aType;


            $this->TagName = 'link';
            parent::__construct($aName);

        }
        public function createTagParams(){   

            $theParams = array();

            (!empty($this->charset)) ? $theParams['charset'] = $this->charset : null;
            (!empty($this->href)) ? $theParams['href'] = $this->href : null;
            (!empty($this->hreflang)) ? $theParams['hreflang'] = $this->hreflang : null;
            (!empty($this->media)) ? $theParams['media'] = $this->media : null;
            (!empty($this->rel)) ? $theParams['rel'] = $this->rel : null;
            (!empty($this->rev)) ? $theParams['rev'] = $this->rev : null;
            (!empty($this->target)) ? $theParams['target'] = $this->target : null;
            (!empty($this->type)) ? $theParams['type'] = $this->type : null;

            (!empty($this->lang)) ? $theParams['xml:lang'] = $this->lang : null;

            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        public function onShow(){


            echo $this->opencloseTag($this->TagName,$this->createTagParams());

            //echo $this->closeTag($this->TagName);


        }


    }

    class StyleSheetLink extends ExternalLink {

        function __construct($aUrl,$aMedia='all',$aType='text/css',$aTarget=null,$aName=null) {

            $this->rel = 'stylesheet';
            parent::__construct($aUrl,$aMedia,$aType,$aTarget,$aName);

        }

    }

    class StyleSheet extends TBaseWebComponent {

        public $media;      //Specifies on what device the linked document will be displayed  {screen,tty,tv,projection,handheld,print,braille,aural,all}
        public $type;       //Specifies the MIME type of the linked document MIME_type
        public $StyleSheet;

        public $CssRules;

        function __construct($ainLineStyleSheet=null,$aMedia = 'all') {

            $this->media = $aMedia;
            $this->type = 'text/css';
            $this->StyleSheet = $ainLineStyleSheet;
            $this->CssRules = array();

            $this->TagName = 'style';
            parent::__construct($aName);

        }
        public function createTagParams(){   

            $theParams = array();

            (!empty($this->media)) ? $theParams['media'] = $this->media : null;
            (!empty($this->type)) ? $theParams['type'] = $this->type : null;

            (!empty($this->lang)) ? $theParams['xml:lang'] = $this->lang : null;

            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        function addStyle($aSelector,TBaseStyleTag $StyleDeclaration){

            $this->CssRules[$aSelector] = $StyleDeclaration;

        }

        public function onShow(){

            $innerText = "\r\n{$this->StyleSheet}\r\n";

            //$innerText .= $this->createParamStyle(true);
            if (!empty($this->CssRules)){
                foreach ($this->CssRules as $Selector=>$StyleDeclaration){
                    $innerText .= "{$Selector}{\r\n".$StyleDeclaration->createParamStyle(true)."\r\n}\r\n";
                }
            }

            echo $this->opencloseTag($this->TagName,$this->createTagParams(),"\r\n{$innerText}\r\n");

            //echo $this->closeTag($this->TagName);


        }


    }

    class JavaScriptLink extends TBaseWebComponent{

        public $type;       //Specifies the MIME type of a script
        public $charset;    //Specifies the character encoding used in an external script file
        public $defer;      //Specifies that the execution of a script should be deferred (delayed) until after the page has been loaded
        public $src;        //Specifies the URL of an external script file
        //xml:space preserve      Specifies whether whitespace in code should be preserved

        function __construct($aSrc,$aCharset=null,$aDefer=null,$aType = 'text/javascript', $aName=null) {

            $this->src = $aSrc;
            $this->charset = $aCharset;
            $this->defer = $aDefer;
            $this->type = $aType;
            $this->TagName = 'script';
            parent::__construct($aName);



        }

        public function createTagParams(){   

            $theParams = array();

            (!empty($this->charset)) ? $theParams['charset'] = $this->charset : null;
            (!empty($this->src)) ? $theParams['src'] = $this->src : null;
            (!empty($this->defer)) ? $theParams['defer'] = $this->defer : null;
            (!empty($this->type)) ? $theParams['type'] = $this->type : null;

            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        public function onShow(){


            echo $this->opencloseTag($this->TagName,$this->createTagParams());

            //echo $this->closeTag($this->TagName);


        }

    }

    class Base extends TBaseWebComponent {

        public $href;       //Specifies a base URL for all relative URLs on a page. Note: The base URL must be an absolute URL! 
        public $target;    //Specifies where to open all the links on a page

        function __construct($aHref,$aTarget=null,$aName=null) {

            $this->href = $aHref;
            $this->target = $aTarget;
            $this->TagName = 'base';
            parent::__construct($aName);

        }

        public function createTagParams(){   

            $theParams = array();

            (!empty($this->href)) ? $theParams['href'] = $this->href : null;
            (!empty($this->target)) ? $theParams['target'] = $this->target : null;

            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        public function onShow(){

            echo $this->emptyTag($this->TagName,$this->createTagParams());

        }    
    }

    //http://www.w3schools.com/tags/att_meta_http_equiv.asp
    //http://www.w3schools.com/tags/att_meta_scheme.asp
    class Meta extends TBaseWebComponent {

        public $content;    //Specifies the content of the meta information
        public $httpequiv;  //Provides an HTTP header for the information in the content attribute {content-type,content-style-type,expires,set-cookie,others}
        public $name;       //Provides a name for the information in the content attribute {author,description,keywords,generator,revised,others}
        public $scheme;     //Specifies a scheme to be used to interpret the value of the content attribute
        
        function __construct($content,$name,$httpequiv=null,$scheme=null,$aName=null) {

            $this->content = $content;
            $this->name = $name;
            $this->httpequiv = $httpequiv;
            $this->scheme = $scheme;
            $this->TagName = 'meta';
            parent::__construct($aName);

        }

        public function createTagParams(){   

            $theParams = array();

            (!empty($this->content)) ? $theParams['content'] = $this->content : null;
            (!empty($this->name)) ? $theParams['name'] = $this->name : null;
            (!empty($this->httpequiv)) ? $theParams['http-equiv'] = $this->httpequiv : null;
            (!empty($this->scheme)) ? $theParams['scheme'] = $this->scheme : null;

            return $theParams;
        }

        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }

        public function onShow(){

            echo $this->emptyTag($this->TagName,$this->createTagParams());

        }    
    }

    class HtmlHeader extends TBaseWebComponentContainer{

        public $Title;
        public $Scripts = array();
        public $Links = array();

        function __construct($aTitle=null,$aName=null) {

            $this->Title = $aTitle;
            $this->TagName = 'head';
            parent::__construct($aName);

        }

        public function setTitle($aTitle,$append=false){
            if ($append === true){
                $this->Title .= $aTitle;
            } else {
                $this->Title = $aTitle;
            }
        }


        function Show(){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow);
            }
        }


        public function createTagParams(){   

            /*            $theParams = array();
            if($this->isXHTML){ (!empty($this->xmlns)) ? $theParams['xmlns'] = $this->xmlns : null; }
            (!empty($this->dir)) ? $theParams['dir'] = $this->dir : null;
            if($this->isXHTML){ 
            (!empty($this->lang)) ? $theParams['xml:lang'] = $this->lang : null;
            } else {
            (!empty($this->lang)) ? $theParams['lang'] = $this->lang : null;
            }
            */
        }


        public function onShow(){


            echo $this->openTag($this->TagName,$this->createTagParams());
            if (!is_null($this->Title)){
                echo $this->opencloseTag('title',null,$this->Title);
            } 

            $counter = 0;

            foreach ($this as $component){

                if (isset($this->FOnBeginIterate)) {$p = array($counter,$component); $this->DispachEvent($this->FOnBeginIterate,$p);}

                $component->Show();

                if (isset($this->FOnEndIterate)) $this->DispachEvent($this->FOnEndIterate,$component);


                $counter++;


            }

            echo $this->closeTag($this->TagName);


        }



    }



?>
