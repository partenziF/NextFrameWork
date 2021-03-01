<?php
  if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');

class Heading extends TBaseWebComponentContainer{

    public $InnerText;

    function __construct($aInnerText=null,$aLevel=1,$aId=null,$aClass=null,$aName=null) {
        settype($aLevel,'int');
        $Level = ( ($aLevel==0) ? 1 : ( ($aLevel>6) ? 6 : $aLevel) );
        $this->TagName = "h{$Level}";
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

?>
