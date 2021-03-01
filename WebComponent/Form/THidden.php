<?php
    if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);

    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');

    class THidden extends TBaseInputWebComponent {
        
        function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

            $this->TagName = 'input';        
            $this->type = 'hidden';
            parent::__construct($aName,$aId,$aClass,$aTitle);
        }
        


        public function onShow(){

            if (isset($this->Value))
            echo $this->emptyTag($this->TagName,$this->createTagParams());

        }
        
        
    }
  
    