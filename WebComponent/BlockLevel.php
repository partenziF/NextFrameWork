<?php
if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');

class BlockLevel extends TBaseWebComponentContainer{

    public $InnerText;

    function __construct($aName=null,$aInnerText=null,$aId=null,$aClass=null) {
        $this->TagName = 'div';
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }

    public function onShow(){
        echo $this->openTag($this->TagName,$this->createTagParams(),$this->InnerText);
        parent::onShow();
        echo $this->closeTag($this->TagName);
    }
    
}

class Inline extends TBaseWebComponentContainer{

    public $InnerText;

    function __construct($aName=null,$aInnerText=null,$aId=null,$aClass=null) {
        $this->TagName = 'span';
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }



    public function onShow(){
        echo $this->openTag($this->TagName,$this->createTagParams(),$this->InnerText);
        parent::onShow();
        echo $this->closeTag($this->TagName);
    }
    
}

class Paragraph extends TBaseWebComponentContainer{

    public $InnerText;

    function __construct($aInnerText=null,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'p';
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }



    public function onShow(){
        echo $this->openTag($this->TagName,$this->createTagParams(),$this->InnerText);
        parent::onShow();
        echo $this->closeTag($this->TagName);
    }
    
}

class Address extends TBaseWebComponentContainer{

    public $InnerText;

    function __construct($aInnerText=null,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'address';
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }

    public function onShow(){
        echo $this->openTag($this->TagName,$this->createTagParams(),$this->InnerText);
        parent::onShow();
        echo $this->closeTag($this->TagName);
    }
    
}


class ScriptCode extends TBaseWebComponentContainer{

    public $InnerText;
    public $type="text/javascript";

    function __construct($aLanguage,$aInnerText=null,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'script';
        $this->language = $aLanguage;
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }

    protected function createTagParams(){   

        $theParams = array();
        $theParams['type'] = $this->language; 
        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }

    public function onShow(){
        
        echo $this->openTag($this->TagName,$this->createTagParams(),"\r\n{$this->InnerText}\r\n",true,false,false,false);
        parent::onShow();
        echo $this->closeTag($this->TagName);
    }
    
}

class JavaScriptCode extends ScriptCode{
/*
HTML 4 and XHTML deal different with the content inside scripts:

    * In HTML 4, the content type is declared as CDATA, which means that entities will not be parsed.
    * In XHTML, the content type is declared as (#PCDATA), which means that entities will be parsed.

This means that in XHTML, all special characters should be encoded or all content should be wrapped inside a CDATA section.

To ensure that a script parses correctly in an XHTML document, use the following syntax:

*/    
    function __construct($aInnerText=null,$aId=null,$aClass=null,$aName=null) {
        parent::__construct('text/javascript',$aInnerText,$aId,$aClass,$aName);        
    }
    
}



class PreFormatText extends TBaseWebComponent{
    public $InnerText;

    function __construct($aInnerText,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'pre';
        if (!is_null($aInnerText)) $this->InnerText = $aInnerText;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
    }



    public function onShow(){

        echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->InnerText);

    }

}

class LineBreak extends TBaseWebComponent {

    function __construct($aName=null,$style=null,$aId=null,$aClass=null) {
        $this->TagName = 'br';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        if (!empty($style)){
            foreach ($style as $k=>$v){
                $this->$k = $v;
            }
        }
    }

    public function onShow(){

        echo $this->emptyTag($this->TagName,$this->createTagParams());

    }
}

class Link extends TBaseWebComponent {

    private $TextLink;

    public $charset;
    public $coords;
    public $href;
    public $hreflang;
    public $name;
    public $rel;
    public $rev;
    public $target;

    function __construct($aTextLink,$aHref,array $Params=array(),$aTarget=null,$style=null,$aClass=null,$aId=null,$aName=null) {


        $this->TagName = 'a';
        $this->TextLink = $aTextLink;
        $this->href = $this->getUrl($aHref,$Params);
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        if (!is_null($style)){
            foreach ($style as $k=>$v){
                $this->$k = $v;
            }
        }
    }
    function setTextLink($aTextLink){
        $this->TextLink = $aTextLink;
    }

    function setUrl($aHref,array $Params=array()){
        $this->href = TagOutput::CreateUrl($aHref,$Params);
    }

    function getUrl($aHref,array $Params=array()){
        return TagOutput::CreateUrl($aHref,$Params);
    }

    protected function createTagParams(){   

        $theParams = array();
        (!empty($this->charset)) ? $theParams['charset'] = $this->charset : null;
        (!empty($this->coords)) ? $theParams['coords'] = $this->coords : null;
        (!empty($this->href)) ? $theParams['href'] = $this->href : null;
        (!empty($this->hreflang)) ? $theParams['hreflang'] = $this->hreflang : null;
        (!empty($this->name)) ? $theParams['name'] = $this->name : null;
        (!empty($this->rel)) ? $theParams['rel'] = $this->rel : null;
        (!empty($this->rev)) ? $theParams['rev'] = $this->rev : null;
        (!empty($this->target)) ? $theParams['target'] = $this->target : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }


    public function onShow(){
        if (is_null($this->TextLink)){
            echo $this->opencloseTag($this->TagName,$this->createTagParams());
        } else {
            echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->TextLink);
        }

    }


}

class Image extends TBaseWebComponent {

    public $alt;
    public $src;
    public $height;
    public $ismap;
    public $longdesc;
    public $usemap;
    public $width;

    
    function __construct($aSrc,$aAltText="",$style=null,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'img';
        
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        $this->src = $aSrc;
        $this->alt = $aAltText;
        if (!is_null($style)){
            foreach ($style as $k=>$v){
                $this->$k = $v;
            }
        }
    }


    protected function createTagParams(){   

        $theParams = array();
        $theParams['alt'] = $this->alt; //alt è un attributo richiesto per validare la pagina!
        (!empty($this->src)) ? $theParams['src'] = $this->src : null;
        (!empty($this->height)) ? $theParams['height'] = $this->height : null;
        (!empty($this->ismap)) ? $theParams['ismap'] = $this->ismap : null;
        (!empty($this->longdesc)) ? $theParams['longdesc'] = $this->longdesc : null;
        (!empty($this->usemap)) ? $theParams['usemap'] = $this->usemap : null;
        (!empty($this->width)) ? $theParams['width'] = $this->width : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }

    public function onShow(){

        echo $this->emptyTag($this->TagName,$this->createTagParams());

    }
}

class TextNode extends GenericComponent {
    
    public $TextContent;
        
    function __construct($aTextContent) {
        if (!is_null($aTextContent)) $this->TextContent = $aTextContent;
        parent::__construct($aName);
    }
    
    public function setTextContent($aTextContent){
        $this->TextContent = $aTextContent;
    }

    public function onShow(){

        echo $this->TextContent;

    }

    
}
