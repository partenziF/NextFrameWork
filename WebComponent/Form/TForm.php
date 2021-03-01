<?php

if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponentContainer.php');


abstract class TForm extends TBaseWebComponentContainer {

    //private $_messages;
    private $_event;
    private $_isValid = true;

    public $action; //-- server-side form handler --
    public $method;  //    (GET|POST)     GET       -- HTTP method used to submit the form--
    public $enctype;//    %ContentType;  "application/x-www-form-urlencoded"
    public $accept; //     %ContentTypes; #IMPLIED  -- list of MIME types for file upload --
    //public $name;//        CDATA          #IMPLIED  -- name of form for scripting --
    public $onsubmit;//    %Script;       #IMPLIED  -- the form was submitted --
    public $onreset;//     %Script;       #IMPLIED  -- the form was reset --
    public $acceptCharset;// %Charsets;  #IMPLIED  -- list of supported charsets --


    public $FOnEventErrorParams;

    function __construct($aName,$aAction,$aMethod='post',$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'form';        
        $this->action = $aAction;
        $this->method = $aMethod;
        //$this->_messages = new MessageQueue();
        $this->_event = new WebEventQueue();

        parent::__construct($aName,$aId,$aClass,$aTitle);

        $this->InitializeComponent();

    }

    public function createTagParams(){   

        $theParams = array();
        (!empty($this->action)) ? $theParams['action'] = $this->action : null;
        (!empty($this->method)) ? $theParams['method'] = strtolower($this->method) : null;

        (!empty($this->enctype)) ? $theParams['enctype'] = $this->enctype : null;
        (!empty($this->accept)) ? $theParams['accept'] = $this->accept : null;
        //(!empty($this->Name)) ? $theParams['name'] = $this->Name : null;
        (!empty($this->onsubmit)) ? $theParams['onsubmit'] = $this->onsubmit : null;
        (!empty($this->onreset)) ? $theParams['onreset'] = $this->onreset : null;
        (!empty($this->acceptCharset)) ? $theParams['accept-charset'] = $this->acceptCharset : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }



    function Show(){
        if ($this->isVisible) {
            $this->DispachEvent($this->FOnShow);
        }
    }

    public function onShow(){

        //if (!is_null($this->TagOutput))
        if ($this->TagOutput === TagOutputType::toECHO)
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

        //if (!is_null($this->TagOutput))
        if ($this->TagOutput === TagOutputType::toECHO)
        echo $this->closeTag($this->TagName);

    }


    public function Process(){
        $this->_event->Process();
        $this->_messages->Process();
    }
    

    public function setValid($r){
        if (gettype($r)=='boolean')  {
            $this->_isValid = ($this->_isValid and $r);
        }
    }

    public function isValid(){
        return $this->_isValid;
    }

    abstract public function InitializeComponent();

    //se ci fosse overload fatto bene sarebbe più facile!
    public function createEvent($aCallback,$aEvent,$aRequiredParams1=null,$aRequiredParams2=null,$aRequiredParams3=null,$aRequiredParams4=null,$aRequiredParams5=null){

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
        if (!is_null($this->FOnEventErrorParams)) $msg->CallBack = $this->FOnEventErrorParams;
        $this->EventPost($msg);

    }

    //se ci fosse overload fatto bene sarebbe più facile!

    public function EventPost(TWebEvent $WebEvent){
        
        $this->_event->Post($WebEvent);
        
    }

    public function __toString(){
        $this->Show();
        return '';
    }

}

class TLegend extends TBaseWebComponent {

    public $Caption;

    function __construct($aName,$aCaption,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'legend';            
        $this->Caption = $aCaption;
        parent::__construct($aName,$aId,$aClass,$aTitle);
    }

    public function onShow(){
        //if (!is_null($this->TagOutput))
        if ($this->TagOutput === TagOutputType::toECHO)
        echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->Caption);
    }
}

class TFieldset extends TBaseWebComponentContainer {
    public $Legend;
    public $FormContainer;

    function __construct($aName,$aLegendCaption=null,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'fieldset';            
        parent::__construct($aName,$aId,$aClass,$aTitle);
        if (!is_null($aLegendCaption)){
            $this->Legend = new TLegend($this->Name.'_legend',$aLegendCaption);
        }
    }

    function Show(){

        if ($this->isVisible) {
            $this->DispachEvent($this->FOnShow);
        }
    }

    public function onShow(){

        //if (!is_null($this->TagOutput))
        if ($this->TagOutput === TagOutputType::toECHO)
        echo $this->openTag($this->TagName,$this->createTagParams());

        if (!is_null($this->Legend)) $this->Legend->Show();

        $counter = 0;

        foreach ($this as $component){

            if (isset($this->FOnBeginIterate)) {$p = array($counter,$component); $this->DispachEvent($this->FOnBeginIterate,$p);}

            $msgProvider = MessageProviderFinder::getMessageProvider($this->FormContainer,$component,$component);

            if (is_null($msgProvider)){
                $component->Show();
            } else {
                $msgProvider[0]->Show($component);
            }

            if (isset($this->FOnEndIterate)) $this->DispachEvent($this->FOnEndIterate,$component);

            $counter++;
        }

        //if (!is_null($this->TagOutput))
        if ($this->TagOutput === TagOutputType::toECHO)
        echo $this->closeTag($this->TagName);

    }


}
