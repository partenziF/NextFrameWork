<?php

if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');
require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');
require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponentContainer.php');

class QueryStringParam extends TBaseComponent implements IBaseInputWebComponent,SplObserver {

    protected $Value;
    protected $DataType;
    protected $DefaultValue;
    
    public $isValid;

    public $FOnValidate;


    function __construct($aName,$aDataType,$aDefaultValue=null) {
        
        $this->DataType = $aDataType;
        $this->DefaultValue = $aDefaultValue;
        parent::__construct($aName);            

    }


    public function getValue(){
        return $this->Value;
    }

    public function issetValue(){
        return isset($this->Value);
    }

    public function setValue($aValue){
        $this->Value = $aValue;
    }

    public function setDefaultValue($aValue){
        $this->DefaultValue = $aValue;
        $this->Value = $aValue;
    }

    public function setDataType($aDataType){
        $this->DataType = $aDataType;
    }


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

        if (!self::getInputRequest()) {            
            $this->doBind();
        } else {
            if ($AutoValidate===true){
                $this->Validate($ValidateMessage);
                $this->ValidateMessage = $ValidateMessage;
            }
        }

    }

    public function Validate(){

        if ($this->DispachEvent($this->FOnValidate,$ValidateMessage) === false) {
            $this->ValidateMessage = $ValidateMessage;
            $this->isValid = false;
            return false;
        } else {
            $this->isValid = true;
            return true;
        }


    }
    
    public function update(SplSubject $s){

        if ($s instanceof TGenericMessage){
            $r = $this->DispachEvent($s->Message,$s->Params);            
            if (is_null($s->Params)) { $s->Params[] = $this->Name;}
            else { array_unshift($s->Params,$this->Name); }
            return $r;                        
        }

    }
    

}

class TWebPage extends TBaseWebComponentContainer{

//    private $_messages;    
    private $_event;
    private $_isValid = true;

    public $FOnEventErrorParams;

    public $onload;
    public $onunload;

    function __construct($aName=null,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'body';        
        //$this->_messages = new MessageQueue();
        $this->_event = new WebEventQueue();

        parent::__construct($aName,$aId,$aClass,$aTitle);

        $this->InitializeComponent();

    }

    function Show(){
        if ($this->isVisible) {
            $this->DispachEvent($this->FOnShow);
        }
    }


    public function createTagParams(){   

        $theParams = array();
        (!empty($this->onunload)) ? $theParams['onunload'] = $this->onunload : null;
        (!empty($this->onload)) ? $theParams['onload'] = $this->onload : null;


        $theBaseParams = parent::createTagParams();     
        unset($theBaseParams['title']);//Non esistono
        unset($theBaseParams['lang']);//Non esistono
        return array_merge((array)$theParams,(array)$theBaseParams);

    }


    public function onShow(){

        echo $this->openTag($this->TagName,$this->createTagParams());

        $msgProvider = MessageProviderFinder::getMessageProvider($this,$this);

        if (!is_null($msgProvider)) {
            foreach ($msgProvider as $msg) {
                $msg->ShowMessage($this);
            }
        }

        $counter = 0;

        foreach ($this as $component){

            if (isset($this->FOnBeginIterate)) {$p = array($counter,$component); $this->DispachEvent($this->FOnBeginIterate,$p);}

            $msgProvider = MessageProviderFinder::getMessageProvider($this,$component);

            if (is_null($msgProvider)){
                $component->Show();
            } else {
                $msgProvider->Show($component);
            }

            if (isset($this->FOnEndIterate)) $this->DispachEvent($this->FOnEndIterate,$component);


            $counter++;


        }

        echo $this->closeTag($this->TagName);


    }

    public function Process(){
        $this->_event->Process();
        $this->_messages->Process();
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

    public function setValid($r,$setValue=false){
        if (gettype($r)=='boolean')  {
            if ($setValue){
                $this->_isValid = $r;
            } else {
                $this->_isValid = ($this->_isValid and $r);                
            }
        }
    }

    public function isValid(){
        return $this->_isValid;
    }

    //abstract public function InitializeComponent();

    //se ci fosse overload fatto bene sarebbe più facile!
    public function createEvent($aCallback,$aEvent,$FOnValidateParam,$aRequiredParams1=null,$aRequiredParams2=null,$aRequiredParams3=null,$aRequiredParams4=null,$aRequiredParams5=null){

        if ( (is_null($aRequiredParams1)) and (is_null($aRequiredParams2)) and (is_null($aRequiredParams3)) and (is_null($aRequiredParams4)) and (is_null($aRequiredParams5)) ){
            $msg = new TWebEvent($aCallback,$aEvent);        
        } else if ( (is_null($aRequiredParams2)) and (is_null($aRequiredParams3)) and (is_null($aRequiredParams4)) and (is_null($aRequiredParams5)) ){
                $msg = new TWebEvent($aCallback,$aEvent,$aRequiredParams1);    
            } else if ( (is_null($aRequiredParams3)) and (is_null($aRequiredParams4)) and (is_null($aRequiredParams5)) ){
                    $msg = new TWebEvent($aCallback,$aEvent,$aRequiredParams1,$aRequiredParams2);
                } else if ( (is_null($aRequiredParams4)) and (is_null($aRequiredParams5)) ){
                        $msg = new TWebEvent($aCallback,$aEvent,$aRequiredParams1,$aRequiredParams2,$aRequiredParams3);
                    } else if ( (is_null($aRequiredParams5)) ){
                            $msg = new TWebEvent($aCallback,$aEvent,$aRequiredParams1,$aRequiredParams2,$aRequiredParams3,$aRequiredParams4);
                        } else {
                            $msg = new TWebEvent($aCallback,$aEvent,$aRequiredParams1,$aRequiredParams2,$aRequiredParams3,$aRequiredParams4,$aRequiredParams5);

        }

        $msg->attach($this);
        if (!is_null($FOnValidateParam)) $msg->FOnValidateParam = $FOnValidateParam;
        if (!is_null($this->FOnEventErrorParams)) $msg->CallBack = $this->FOnEventErrorParams;
        $this->EventPost($msg);

    }

    //se ci fosse overload fatto bene sarebbe più facile!
    public function createMessage($aCallback,$aParam1=null,$aParam2=null,$aParam3=null,$aParam4=null,$aParam5=null){

        if ( (is_null($aParam1)) and (is_null($aParam2)) and (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) ){
            $msg = new TGenericMessage($aCallback);        
        } else if ( (is_null($aParam2)) and (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) ){
                $msg = new TGenericMessage($aCallback,$aParam1);    
            } else if ( (is_null($aParam3)) and (is_null($aParam4)) and (is_null($aParam5)) ){
                    $msg = new TGenericMessage($aCallback,$aParam1,$aParam2);
                } else if ( (is_null($aParam4)) and (is_null($aParam5)) ){
                        $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3);
                    } else if ( (is_null($aParam5)) ){
                            $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4);
                        } else {
                            $msg = new TGenericMessage($aCallback,$aParam1,$aParam2,$aParam3,$aParam4,$aParam5);

        }
                

        $msg->attach($this);
        $this->MessagePost($msg);

    }


    public function EventPost(TWebEvent $WebEvent){
        $this->_event->Post($WebEvent);
    }

    public function __toString(){
        $this->Show();
        return '';
    }
    
    
    //abstract function InitializeComponent();  

}
