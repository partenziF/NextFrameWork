<?php

if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);

require_once(PATH_TO_FRAMEWORK_BASECLASS.'MessageQueque.php');
require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'BlockLevel.php');

DEFINE('MSG_PROVIDER_POSITION_BEFORE',0);
DEFINE('MSG_PROVIDER_POSITION_AFTER',1);


class MessageProvider extends TBaseClass {

    public $ContainerControl;
    public $Position;
    private $_components;

    public $FOnShow;
    public $FOnShowMessage;

    public $FOnBeforeShow;
    public $FOnAfterShow;
    
    public $WebComponent;


    public function __construct($aContainerControl,TBaseWebComponent $aWebComponent=null){

        $this->_components = new SplObjectStorage();
        $this->FOnShow = 'onShow';
        $this->FOnShowMessage = 'onShowMessage';
        $this->Position = MSG_PROVIDER_POSITION_AFTER;
        $this->ContainerControl = $aContainerControl;
        if (is_null($aWebComponent)){
            $this->WebComponent = new BlockLevel('div_'.$this->Name);
        }

    }

    public function setMessage($aControl,$aMessage,$aClass=null){    
        $this->_components->attach($aControl,$aMessage);
        if (!is_null($aClass)) $this->WebComponent->class = $aClass;
    }
    

    public function unsetError($aControl){
        $this->_components->detach($aControl);
    }

    public function exists($aControl){
        return $this->_components->offsetExists($aControl);
    }

    public function Show($aControl){
        
        if ($this->_components->offsetExists($aControl)){
            
            //echo '<span class="inputerror">';
            if (isset($this->FOnBeforeShow)) $this->DispachEvent($this->FOnBeforeShow);
            
            if ($this->Position === MSG_PROVIDER_POSITION_BEFORE) $this->ShowMessage($aControl);
            
            $this->DispachEvent($this->FOnShow,$aControl);
            
            if ($this->Position === MSG_PROVIDER_POSITION_AFTER) $this->ShowMessage($aControl);
            
            if (isset($this->FOnAfterShow)) $this->DispachEvent($this->FOnAfterShow);
            
            
        }

    }


    public function onShow($aControl) {

        $aControl->Show();



    }

    function onShowMessage($aMessage){

        $this->WebComponent->InnerText = $aMessage;
        $this->WebComponent->Show();
  
    }

    public function ShowMessage($aControl){

        $this->DispachEvent($this->FOnShowMessage,$this->_components[$aControl]);
    }


}


class MessageProviderFinder{


    public static function getMessageProvider($aForm,$aControl){        
        $result = NULL;
        foreach ($GLOBALS as $k => $v){
            if ($v instanceof MessageProvider) {
                if ($v->ContainerControl == $aForm){
                    if ($v->exists($aControl)){
                        $result[] = $v;
                        //return $v;
                    }
                }
            }
        }
        return $result;

    }
    

}

