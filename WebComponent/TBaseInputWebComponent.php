<?php

if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');
require_once(PATH_TO_FRAMEWORK_BASECLASS.'TGenericRequest.php');

if (!defined('MAGIC_QUOTE')) define('MAGIC_QUOTE',get_magic_quotes_gpc());

interface IBaseInputWebComponent {

    public function getValue();
    public function setValue($aValue);
    public function issetValue();
    public function setDefaultValue($aValue);
    public function setDataType($aDataType);
    public function getInputRequest();
    public function Input($AutoValidate=false);
    public function Validate();    

}


class TBaseInputWebComponent extends TBaseWebComponent implements IBaseInputWebComponent {

    protected $Value;
    protected $DefaultValue;
    protected $isValid;
    protected $DataType;
    public $ValidateMessage;
    public $FormContainer;
    public $FOnFormatValue;

    public $type;
    public $size;        
    public $disabled;
    public $alt;
    public $tabindex;
    public $accesskey;

    public $onfocus;    //the element got the focus --
    public $onblur;     //the element lost the focus --
    public $onselect;   //some text was selected --
    public $onchange;   //the element value was changed --


    //src         %URI;          #IMPLIED  -- for fields with images --
    //usemap      %URI;          #IMPLIED  -- use client-side image map --
    //ismap       (ismap)        #IMPLIED  -- use server-side image map --
    //  accept      %ContentTypes; #IMPLIED  -- list of MIME types for file upload --

    var $FOnValidate;


    function __construct($aName,$aId,$aClass,$aTitle) {

        if ( (!empty($aId) && (!is_null($aId))) ){
            if (is_array($aId)){
                $aId = str_replace(' ','_',$aId);
                if ($aId{0}=='_'){ 
                    $aId=substr($aId,1); 
                    //} else { 
                    //$aId=$aName; 
                }                  
            }
        }

        $this->id = $aId;
        if (!is_null($aClass)) $this->class = $aClass;
        if (!is_null($aTitle)) $this->title = $aTitle;
        $this->DataType = 'string';
        parent::__construct($aName);            

    }


    public function getValue(){
        if (isset($this->FOnFormatValue)){
            return $this->DispachEvent($this->FOnFormatValue,$this->Value);
        } else {
            return $this->Value;
        }

    }

    public function issetValue(){
        return isset($this->Value);
    }
    public function isEmptyValue(){
        return empty($this->Value);
    }

    public function setValue($aValue){
        $this->Value = $aValue;
    }

    public function unsetValue(){
        unset($this->Value);
    }

    public function setDefaultValue($aValue){
        $this->DefaultValue = $aValue;
        if (!isset($this->Value)) $this->Value = $aValue;
    }

    public function setDataType($aDataType){
        $this->DataType = $aDataType;
    }

    //TODO Dovrebbe supportare la sintassi nome[indice]([indice2]);
    public function getInputRequest(){
        if (TGenericRequest::existParam($this->Name)) {
            $this->Value = TGenericRequest::getRequestValue($this->Name,$this->DataType);
            if (is_null($this->Value)) {
                if (isset($this->DefaultValue)) $this->Value = $this->DefaultValue;
            }
            return true;
        } else {
            if (isset($this->DefaultValue)) $this->Value = $this->DefaultValue;
            return false;
        }

    }

    public function Input($AutoValidate=false){

        if (!$this->getInputRequest()) {            
            $this->doBind();
        } else {
            if ($AutoValidate===true){
                $this->Validate($ValidateMessage);
                $this->ValidateMessage = $ValidateMessage;
            }
        }

    }

    public function Validate(){

        if ($this->isVisible) {

            if ($this->DispachEvent($this->FOnValidate,$ValidateMessage) === false) {
                $this->ValidateMessage = $ValidateMessage;
                $this->isValid = false;
                return false;
            } else {
                $this->isValid = true;
                return true;
            }

        } else {
            $this->isValid = true;
            return true;
        }

    }


    protected function createTagParams(){

        $theParams = array();

        (!empty($this->Name)) ? $theParams['name'] = $this->Name : null;
        (!empty($this->type)) ? $theParams['type'] = $this->type : null;
        (($this->Value!=='') || (!is_null($this->Value))) ? $theParams['value'] = htmlspecialchars($this->getValue()) : null;            //Se value e' un array allora c' da fare qlc considerazione
        (!empty($this->size)) ? $theParams['size'] = $this->size : null;

        (!empty($this->class)) ? $theParams['class'] = $this->class : null;
        (!empty($this->id)) ? $theParams['id'] = $this->id : null;

        (!empty($this->disabled)) ? $theParams['disabled'] = $this->disabled : null;
        (!empty($this->alt)) ? $theParams['alt'] = $this->alt : null;
        (!empty($this->tabindex)) ? $theParams['tabindex'] = $this->tabindex : null;
        (!empty($this->accesskey)) ? $theParams['accesskey'] = $this->accesskey : null;

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

        $cssStyle = parent::createParamStyle();
        (!empty($cssStyle)) ? $theParams['style'] = $cssStyle : null;


        (!empty($this->onfocus)) ? $theParams['onfocus'] = $this->onfocus : null;
        (!empty($this->onblur)) ? $theParams['onblur'] = $this->onblur : null;
        (!empty($this->onselect)) ? $theParams['onselect'] = $this->onselect : null;
        (!empty($this->onchange)) ? $theParams['onchange'] = $this->onchange : null;

        return $theParams;

    }


}
