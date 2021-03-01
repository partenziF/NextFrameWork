<?php

if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

require_once(PATH_TO_FRAMEWORK_BASECLASS.'TBaseComponent.php');

class TagOutput implements  ArrayAccess {

    public $TagName;
    public $ParamList;
    public $InnerText;
    public $isOpen;
    public $haveChild;
    public $isClosed;
    public $toEntities = true;
    public $CR=true;
    /*     
    public function __construct($aTagName,$aParamList,$aInnerText,$isOpen,$haveChild,$isClosed,$toEntities = true,$CR=true){
    $this->TagName = $aTagName;
    $this->ParamList = $aParamList;
    $this->InnerText = $aInnerText;
    $this->isOpen = $isOpen;
    $this->haveChild = $haveChild;
    $this->isClosed = $isClosed;
    $this->toEntities = $toEntities;
    $this->CR = $CR;     
    }
    */    

    public function offsetSet($offset, $value) {
        if (!is_null($offset)) {
            $this->ParamList[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->ParamList[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->ParamList[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->ParamList[$offset]) ? $this->ParamList[$offset] : null;
    }

    static function implode_tag_params($aSeparator,$aParams) {

        $result = array();

        foreach ($aParams as $Key => $Value) {

            if (is_null($Value)) { $result[] = $Key; } 
            else { $result[] = strtolower($Key).'="'.$Value.'"'; }

        }

        return implode($aSeparator,$result);

    }

    static function implode_css_style($aStyles,$newLine = false) {

        $result = array();

        foreach ($aStyles as $Key => $Value) {

            if (is_null($Value)) { $result[] = $Key; } 
            else { $result[] = $Key.':'.$Value; }

        }
        if ($newLine===true){
            return implode(";\r\n",$result);
        } else {
            return implode(';',$result);
        }

    }

    static function implode_url_params($aParams,$encodeAmp=true) {

        $result = array();

        foreach ($aParams as $Key => $Value) {

            if (is_null($Value)) { $result[] = $Key; } 
            else { $result[] = $Key.'='.urlencode($Value); }

        }
        if ($encodeAmp===true){
            return implode('&amp;',$result);
        } else {
            return implode('&',$result);
        }
    }

    static function CreateUrl($aPage,$aUrlParams,$encodeAmp=true) {


        if (!empty($aUrlParams)) {
            if (is_array($aUrlParams)) {
                return $aPage.'?'.self::implode_url_params($aUrlParams,$encodeAmp);
            } else {
                return $aPage.'?'.$aUrlParams;
            }
        } else {
            return $aPage;
        }

    }        


    private static function echoTag($aTagName,$aParamList,$aInnerText,$isOpen,$haveChild,$isClosed,$toEntities = true,$nl2br=false){

        $result = '';
        if (is_array($aInnerText)) {$aInnerText = join(' ',$aInnerText);}
        $aInnerText = ($toEntities==true)?(htmlentities($aInnerText,ENT_COMPAT,'ISO-8859-1',false)):$aInnerText;
        if ($nl2br==true){
            $aInnerText = nl2br($aInnerText);
        }

        //$aInnerText = str_replace(array('\r\n',"\r\n", "\n", "\r"), '<br />', $aInnerText);

        //$theDefaultTagParams = self::$ParamList;
        $theDefaultTagParams = array();
        $theParams = array_merge((array)$theDefaultTagParams,(array)$aParamList);

        if (!empty($theParams)) {
            $StringParams = self::implode_tag_params(' ',$theParams);
        }

        if ($haveChild) {

            if ($isOpen) {

                if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams}>{$aInnerText}"; }
                else { $result .= "<{$aTagName}>{$aInnerText}"; }

            } else {

                $result .= "{$aInnerText}</{$aTagName}>\r\n";
            }

            if ($isClosed) { $result .= "</{$aTagName}>\r\n";    }
            else { $result .= "\r\n";}

        } else {

            if ($isOpen) {

                if (empty($aInnerText)) {
                    if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams} />\r\n"; }
                    else { $result .= "<{$aTagName} />\r\n";}
                } else {
                    if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams} />{$aInnerText}\r\n"; }
                    else {$result .= "<{$aTagName} />{$aInnerText}\r\n";}
                }

                if ($isClosed) { 
                    $result .= "</{$aTagName}>\r\n";    
                }

            } else {

                if (!empty($aInnerText)) $result .= $aInnerText;
                $result .= "</{$aTagName}>\r\n";

            }
        }



        return $result;            
    }

    static function opencloseTag($aTagName,$aParamList=null,$aInnerText=null,$toEntities = true,$nl2br=false){

        return self::echoTag($aTagName,$aParamList,$aInnerText,true,true,true,$toEntities,$nl2br);
    }

    static function emptyTag($aTagName,$aParamList=null,$aInnerText=null,$toEntities = true){

        return self::echoTag($aTagName,$aParamList,$aInnerText,true,false,false,$toEntities,false);
    }


    static function openTag($aTagName,$aParamList=null,$aInnerText=null,$haveChild=true,$isClosed=false,$toEntities = true,$nl2br=false) {

        return self::echoTag($aTagName,$aParamList,$aInnerText,true,$haveChild,$isClosed,$toEntities,$nl2br);
    }

    static function closeTag($aTagName,$aInnerText = null,$nl2br=false) {

        return self::echoTag($aTagName,null,$aInnerText,false,false,false,true,$nl2br);
    }

    function __toString(){

    }

}


//class TagOutputType extends SplEnum{
class TagOutputType{
    //const __default = 1;
    const toECHO  = 1;
    const toNULL  = 2;
    const toVAR   = 3;

    public function __construct(){

    }


}

class TBaseStyleTag {

    //Box Model IE includes padding and border in the width, when the width property is set, unless a DOCTYPE is declared.
    //<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    public $Height;
    public $Width;
    public $Padding;
    public $Margin;

    public $Border;
    public $BorderBottom;
    public $BorderLeft;
    public $BorderRight;
    public $BorderTop;

    public $BorderColor;    //     color_name hex_number rgb_number transparent inherit
    public $BorderStyle;    //  none hidden dotted dashed solid double groove ridge inset outset inherit
    public $BorderWidth;    //  thin medium thick length inherit

    public $Top;
    public $Left;
    public $Right;
    public $Bottom;
    public $Position; //absolute fixed relative static inherit
    public $Display;

    public $Float;
    public $Clear;

    public $Cursor; //Specifies the type of cursor to be displayed

    public $TextAlign;
    #color
    #direction
    #line-height
    #letter-spacing
    #text-decoration
    #text-indent
    #text-shadow
    #text-transform
    #unicode-bidi
    #vertical-align
    #white-space
    #word-spacing

    public $Background;
    public $BackgroundAttachment;
    public $BackgroundColor;
    public $BackgroundImage;
    public $BackgroundPosition;
    public $BackgroundRepeat;


    public $FontFamily;
    public $FontStyle;
    public $FontSize;
    public $FontVariant;
    public $FontWeight;

    // List
    #list-style-type
    #list-style-image
    #list-style-position

    public $ZIndex;

    public function __construct(array $style=array()){
        if (!empty($style)){
            foreach ($style as $k=>$v){
                $this->$k = $v;
            }
        }

    }

    public function copyStyle(TBaseWebComponent $aComponent){

        foreach ($this as $property=>$value){
            if (!is_null($value)){
                if (property_exists($aComponent,$property)){
                    $aComponent->$property = $value;
                }
            }
        }

    }

    public function createParamStyle($newLine=false){

        $cssStyle = array();
        ### CSS STYLE ##########################################################################################

        (!empty($this->Height)) ? $cssStyle['height'] = $this->Height : null;
        (!empty($this->Width)) ? $cssStyle['width'] = $this->Width : null;

        (!empty($this->Padding)) ? $cssStyle['padding'] = $this->Padding : null;
        (!empty($this->Margin)) ? $cssStyle['margin'] = $this->Margin : null;


        (!empty($this->Border)) ? $cssStyle['border'] = $this->Border : null;
        (!empty($this->BorderBottom)) ? $cssStyle['border-bottom'] = $this->BorderBottom : null;
        (!empty($this->BorderLeft)) ? $cssStyle['border-left'] = $this->BorderLeft : null;
        (!empty($this->BorderRight)) ? $cssStyle['border-right'] = $this->BorderRight : null;
        (!empty($this->BorderTop)) ? $cssStyle['border-top'] = $this->BorderTop : null;

        (!empty($this->BorderColor)) ? $cssStyle['border-color'] = $this->BorderColor : null;
        (!empty($this->BorderStyle)) ? $cssStyle['border-style'] = $this->BorderStyle : null;
        (!empty($this->BorderWidth)) ? $cssStyle['border-width'] = $this->BorderWidth : null;


        (!empty($this->Top)) ? $cssStyle['top'] = $this->Top : null;
        (!empty($this->Left)) ? $cssStyle['left'] = $this->Left : null;
        (!empty($this->Right)) ? $cssStyle['right'] = $this->Right : null;
        (!empty($this->Bottom)) ? $cssStyle['bottom'] = $this->Bottom : null;
        (!empty($this->Position)) ? $cssStyle['position'] = $this->Position : null; //absolute fixed relative static inherit
        (!empty($this->Display)) ? $cssStyle['display'] = $this->Display : null;

        (!empty($this->Float)) ? $cssStyle['float'] = $this->Float : null;
        (!empty($this->Clear)) ? $cssStyle['clear'] = $this->Clear : null;

        (!empty($this->Cursor)) ? $cssStyle['cursor'] = $this->Cursor : null; //Specifies the type of cursor to be displayed

        (!empty($this->TextAlign)) ? $cssStyle['text-align'] = $this->TextAlign : null;

        (!empty($this->FontFamily)) ? $cssStyle['font-family'] = $this->FontFamily : null;
        (!empty($this->FontStyle)) ? $cssStyle['font-style'] = $this->FontStyle : null;
        (!empty($this->FontSize)) ? $cssStyle['font-size'] = $this->FontSize : null;
        (!empty($this->FontVariant)) ? $cssStyle['font-variant'] = $this->FontVariant : null;
        (!empty($this->FontWeight)) ? $cssStyle['font-weight'] = $this->FontWeight : null;

        (!empty($this->Background)) ? $cssStyle['background'] = $this->Background : null;
        (!empty($this->BackgroundAttachment)) ? $cssStyle['background-attachment'] = $this->BackgroundAttachment : null;
        (!empty($this->BackgroundColor)) ? $cssStyle['background-color'] = $this->BackgroundColor : null;
        (!empty($this->BackgroundImage)) ? $cssStyle['background-image'] = $this->BackgroundImage : null;
        (!empty($this->BackgroundPosition)) ? $cssStyle['background-position'] = $this->BackgroundPosition : null;
        (!empty($this->BackgroundRepeat)) ? $cssStyle['background-repeat'] = $this->BackgroundRepeat : null;


        (!empty($this->ZIndex)) ? $cssStyle['zindex'] = $this->ZIndex : null;


        return (!empty($cssStyle)) ? TagOutput::implode_css_style($cssStyle,$newLine) : null;
    }

}

class GenericComponent extends TBaseComponent implements SplObserver {

    protected $_messages;

    public $isVisible; 
    public $ParentComponent;   

    public $FOnShow = 'onShow';


    function __construct($aName) {
        $this->isVisible = true;      
        parent::__construct($aName);        
        $this->_messages = new MessageQueue();  

    }

    public function Process(){

        $this->_messages->Process();

    }

    public function update(SplSubject $s){

        if ($s instanceof TGenericMessage){
            $r = $this->DispachEvent($s->Message,$s->Params);            
            if (is_null($s->Params)) { $s->Params[] = $this->Name;}
            else { array_unshift($s->Params,$this->Name); }
            return $r;                        
        }

    }

    public function setVisible($aIsVisible=true){
        $this->isVisible = $aIsVisible;
    }

    function Show(){
        if ($this->isVisible) {
            $this->DispachEvent($this->FOnShow);
        }
    }

    public function MessagePush(TGenericMessage $aTGenericMessage) {

        $this->_messages->Push($aTGenericMessage);

    }

    public function MessagePost(TGenericMessage $aTGenericMessage){

        $this->_messages->Post($aTGenericMessage);

    }

    public function MessagePushInOrder(TGenericMessage $aTGenericMessage) {

        $this->_messages->PushInOrder($aTGenericMessage);

    }

    public function createMessage($aCallback,$aParam1=null,$aParam2=null,$aParam3=null,$aParam4=null,$aParam5=null,$aParam6=null,$aParam7=null,$aParam8=null,$aParam9=null,$aParam10=null){

        if ( (is_null($aParam1)) and (is_null($aParam2)) and (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) and (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
            $msg = new TGenericMessage($aCallback);        
        } else if ( (is_null($aParam2)) and (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) and (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                $msg = new TGenericMessage($aCallback,$aParam1);    
            } else if ( (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) and (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                    $msg = new TGenericMessage($aCallback,$aParam1,$aParam2);
                } else if ( (is_null($aParam4)) and (is_null($aParam5)) and (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                        $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3);
                    } else if ( (is_null($aParam5)) and (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                            $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4);
                        } else if ( (is_null($aParam6)) and (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                                $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5);
                            } else if ( (is_null($aParam7)) and (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                                    $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5,$aParam6);
                                } else if ( (is_null($aParam8)) and (is_null($aParam9)) and (is_null($aParam10)) ){
                                        $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5,$aParam6,$aParam7);
                                    } else if ( (is_null($aParam9)) and (is_null($aParam10)) ){
                                            $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5,$aParam6,$aParam7,$aParam8);
                                        } else if ( (is_null($aParam10)) ){
                                                $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5,$aParam6,$aParam7,$aParam8,$aParam9);
                                            } else {
                                                $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5,$aParam6,$aParam7,$aParam8,$aParam9,$aParam10);

        }

        $msg->attach($this);
        $this->MessagePost($msg);

    }

}

//http://www.w3.org/TR/html401/sgml/dtd.html#pre.exclusion
//class TBaseWebComponent extends TBaseComponent implements SplObserver { 
class TBaseWebComponent extends GenericComponent { 

    public $TagName;
    protected $TagOutput;

    //<!ENTITY % coreattrs
    public $class;  //Specifies a classname for an element #IMPLIED  -- document-wide unique id --
    public $id;     //Specifies a unique id for an element #IMPLIED  -- space-separated list of classes --
    public $title;  //Specifies extra information about an element #IMPLIED  -- advisory title --
    public $style = array();    //Specifies an inline style for an element #IMPLIED  -- associated style info --

    //<!ENTITY % i18n
    public $dir; //Specifies the text direction for the content in an element #IMPLIED  -- language code -- 
    public $lang; //Specifies a language code for the content in an element. http://www.w3schools.com/TAGS/ref_language_codes.asp  #IMPLIED  -- direction for weak/neutral text --


    //Box Model IE includes padding and border in the width, when the width property is set, unless a DOCTYPE is declared.
    //<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    public $Height;
    public $Width;
    public $Padding;
    public $Margin;

    public $Border;
    public $BorderBottom;
    public $BorderLeft;
    public $BorderRight;
    public $BorderTop;

    public $BorderColor;    //     color_name hex_number rgb_number transparent inherit
    public $BorderStyle;    //  none hidden dotted dashed solid double groove ridge inset outset inherit
    public $BorderWidth;    //  thin medium thick length inherit


    public $Top;
    public $Left;
    public $Right;
    public $Bottom;
    public $Position; //absolute fixed relative static inherit
    public $Display;

    public $Float;
    public $Clear;

    public $Cursor; //Specifies the type of cursor to be displayed

    public $TextAlign;
    #color
    #direction
    public $LineHeight;
    #letter-spacing
    #text-decoration
    #text-indent
    #text-shadow
    #text-transform
    #unicode-bidi
    public $VerticalAlign;
    public $WhiteSpace;#white-space
    #word-spacing


    public $FontFamily;
    public $FontStyle;
    public $FontSize;
    public $FontVariant;
    public $FontWeight;

    // List
    #list-style-type
    #list-style-image
    #list-style-position

    public $ZIndex;

    public $Background;
    public $BackgroundAttachment;
    public $BackgroundColor;
    public $BackgroundImage;
    public $BackgroundPosition;
    public $BackgroundRepeat;

    //<!ENTITY % events

    // Keyboard Events
    public $onkeydown;
    public $onkeypress;
    public $onkeyup;

    //Mouse Events
    public $onclick;
    public $ondblclick;
    public $onmousedown;
    public $onmousemove;
    public $onmouseout;
    public $onmouseover;
    public $onmouseup;

    // Class Events


    function __construct($aName) {

        $this->TagOutput = TagOutputType::toECHO;
        parent::__construct($aName);            
    }        


    public function setTagOutput($aOutputType){
        $this->TagOutput = $aOutputType;        
    }

    protected function createParamStyle($newLine=false){

        $cssStyle = array();
        ### CSS STYLE ##########################################################################################

        (!empty($this->Height)) ? $cssStyle['height'] = $this->Height : null;
        (!empty($this->Width)) ? $cssStyle['width'] = $this->Width : null;

        (!empty($this->Padding)) ? $cssStyle['padding'] = $this->Padding : null;
        (!empty($this->Margin)) ? $cssStyle['margin'] = $this->Margin : null;

        (!empty($this->Border)) ? $cssStyle['border'] = $this->Border : null;
        (!empty($this->BorderBottom)) ? $cssStyle['border-bottom'] = $this->BorderBottom : null;
        (!empty($this->BorderLeft)) ? $cssStyle['border-left'] = $this->BorderLeft : null;
        (!empty($this->BorderRight)) ? $cssStyle['border-right'] = $this->BorderRight : null;
        (!empty($this->BorderTop)) ? $cssStyle['border-top'] = $this->BorderTop : null;

        (!empty($this->BorderColor)) ? $cssStyle['border-color'] = $this->BorderColor : null;
        (!empty($this->BorderStyle)) ? $cssStyle['border-style'] = $this->BorderStyle : null;
        (!empty($this->BorderWidth)) ? $cssStyle['border-width'] = $this->BorderWidth : null;


        (!empty($this->Top)) ? $cssStyle['top'] = $this->Top : null;
        (!empty($this->Left)) ? $cssStyle['left'] = $this->Left : null;
        (!empty($this->Right)) ? $cssStyle['right'] = $this->Right : null;
        (!empty($this->Bottom)) ? $cssStyle['bottom'] = $this->Bottom : null;
        (!empty($this->Position)) ? $cssStyle['position'] = $this->Position : null; //absolute fixed relative static inherit
        (!empty($this->Display)) ? $cssStyle['display'] = $this->Display : null;

        (!empty($this->Float)) ? $cssStyle['float'] = $this->Float : null;
        (!empty($this->Clear)) ? $cssStyle['clear'] = $this->Clear : null;

        (!empty($this->Cursor)) ? $cssStyle['cursor'] = $this->Cursor : null; //Specifies the type of cursor to be displayed

        (!empty($this->TextAlign)) ? $cssStyle['text-align'] = $this->TextAlign : null;
        (!empty($this->LineHeight)) ? $cssStyle['line-height'] = $this->LineHeight : null;
        (!empty($this->VerticalAlign)) ? $cssStyle['vertical-align'] = $this->VerticalAlign : null;
        (!empty($this->WhiteSpace)) ? $cssStyle['white-space'] = $this->WhiteSpace : null;

        (!empty($this->FontFamily)) ? $cssStyle['font-family'] = $this->FontFamily : null;
        (!empty($this->FontStyle)) ? $cssStyle['font-style'] = $this->FontStyle : null;
        (!empty($this->FontSize)) ? $cssStyle['font-size'] = $this->FontSize : null;
        (!empty($this->FontVariant)) ? $cssStyle['font-variant'] = $this->FontVariant : null;
        (!empty($this->FontWeight)) ? $cssStyle['font-weight'] = $this->FontWeight : null;
        (!empty($this->FontWeight)) ? $cssStyle['font-weight'] = $this->FontWeight : null;

        (!empty($this->Background)) ? $cssStyle['background'] = $this->Background : null;
        (!empty($this->BackgroundAttachment)) ? $cssStyle['background-attachment'] = $this->BackgroundAttachment : null;
        (!empty($this->BackgroundColor)) ? $cssStyle['background-color'] = $this->BackgroundColor : null;
        (!empty($this->BackgroundImage)) ? $cssStyle['background-image'] = $this->BackgroundImage : null;
        (!empty($this->BackgroundPosition)) ? $cssStyle['background-position'] = $this->BackgroundPosition : null;
        (!empty($this->BackgroundRepeat)) ? $cssStyle['background-repeat'] = $this->BackgroundRepeat : null;


        (!empty($this->ZIndex)) ? $cssStyle['zindex'] = $this->ZIndex : null;


        return (!empty($cssStyle)) ? TagOutput::implode_css_style($cssStyle,$newLine) : null;
    }

    protected function createTagParams(){

        $theParams = array();


        (!empty($this->class)) ? $theParams['class'] = $this->class : null;
        (!empty($this->id)) ? $theParams['id'] = $this->id : null;
        (!empty($this->title)) ? $theParams['title'] = $this->title : null;    
        (!empty($this->dir)) ? $theParams['dir'] = $this->title : null;
        (!empty($this->lang)) ? $theParams['lang'] = $this->title : null;

        (!empty($this->onkeydown)) ? $theParams['onkeydown'] = $this->onkeydown : null;
        (!empty($this->onkeypress)) ? $theParams['onkeypress'] = $this->onkeypress : null;
        (!empty($this->onkeyup)) ? $theParams['onkeyup'] = $this->onkeyup : null;


        (!empty($this->onclick)) ? $theParams['onclick'] = $this->onclick : null;
        (!empty($this->ondblclick)) ? $theParams['ondblclick'] = $this->ondblclick : null;

        (!empty($this->onmousedown)) ? $theParams['onmousedown'] = $this->onmousedown : null;
        (!empty($this->onmousemove)) ? $theParams['onmousemove'] = $this->onmousemove : null;
        (!empty($this->onmouseout)) ? $theParams['onmouseout'] = $this->onmouseout : null;
        (!empty($this->onmouseover)) ? $theParams['onmouseover'] = $this->onmouseover : null;
        (!empty($this->onmouseup)) ? $theParams['onmouseup'] = $this->onmouseup : null;

        $cssStyle = $this->createParamStyle();
        (!empty($cssStyle)) ? $theParams['style'] = $cssStyle : null;

        return $theParams;

    }
    /*
    function implode_tag_params($aSeparator,$aParams) {

    $result = array();

    foreach ($aParams as $Key => $Value) {

    if (is_null($Value)) { $result[] = $Key; } 
    else { $result[] = strtolower($Key).'="'.$Value.'"'; }

    }

    return implode($aSeparator,$result);

    }

    function implode_css_style($aStyles) {

    $result = array();

    foreach ($aStyles as $Key => $Value) {

    if (is_null($Value)) { $result[] = $Key; } 
    else { $result[] = $Key.':'.$Value; }

    }

    return implode(';',$result);

    }

    function implode_url_params($aParams) {

    $result = array();

    foreach ($aParams as $Key => $Value) {

    if (is_null($Value)) { $result[] = $Key; } 
    else { $result[] = strtolower($Key).'='.urlencode($Value); }

    }

    return implode('&amp;',$result);
    }

    function CreateUrl($aPage,$aUrlParams) {


    if (!empty($aUrlParams)) {
    if (is_array($aUrlParams)) {
    return $aPage.'?'.self::implode_url_params($aUrlParams);
    } else {
    return $aPage.'?'.$aUrlParams;
    }
    } else {
    return $aPage;
    }

    }        


    private function echoTag($aTagName,$aParamList,$aInnerText,$isOpen,$haveChild,$isClosed,$toEntities = true,$CR=true){

    $result = '';

    $aInnerText = ($toEntities==true)?(htmlentities($aInnerText)):$aInnerText;

    $aInnerText = str_replace(array('\r\n',"\r\n", "\n", "\r"), '<br />', $aInnerText);

    $theDefaultTagParams = self::createTagParams();
    $theParams = array_merge((array)$theDefaultTagParams,(array)$aParamList);

    if (!empty($theParams)) {
    $StringParams = self::implode_tag_params(' ',$theParams);
    }

    if ($haveChild) {

    if ($isOpen) {

    if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams}>{$aInnerText}"; }
    else { $result .= "<{$aTagName}>{$aInnerText}"; }

    } else {

    $result .= "{$aInnerText}</{$aTagName}>\r\n";
    }

    if ($isClosed) { $result .= "</{$aTagName}>\r\n";    }
    else { $result .= "\r\n";}

    } else {

    if ($isOpen) {

    if (empty($aInnerText)) {
    if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams} />\r\n"; }
    else { $result .= "<{$aTagName} />\r\n";}
    } else {
    if (!empty($StringParams)) { $result .= "<{$aTagName} {$StringParams} />{$aInnerText}\r\n"; }
    else {$result .= "<{$aTagName} />{$aInnerText}\r\n";}
    }

    if ($isClosed) { 
    $result .= "</{$aTagName}>\r\n";    
    }

    } else {

    $result .= "</{$aTagName}>\r\n";

    }
    }



    return $result;            
    }
    */
    function opencloseTag($aTagName,$aParamList=null,$aInnerText=null,$toEntities = true,$nl2br=false){

        return TagOutput::opencloseTag($aTagName,$aParamList,$aInnerText,$toEntities,$nl2br);
    }

    function emptyTag($aTagName,$aParamList=null,$aInnerText=null,$toEntities = true){

        return TagOutput::emptyTag($aTagName,$aParamList,$aInnerText,$toEntities);
    }

    function openTag($aTagName,$aParamList=null,$aInnerText=null,$haveChild=true,$isClosed=false,$toEntities = true,$nl2br=false) {

        return TagOutput::openTag($aTagName,$aParamList,$aInnerText,$haveChild,$isClosed,$toEntities,$nl2br);
    }

    function closeTag($aTagName,$aInnerText = null,$nl2br=false) {

        return TagOutput::closeTag($aTagName,$aInnerText,$nl2br);
    }





}


