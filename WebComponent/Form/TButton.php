<?php

    class TGenericButton extends TBaseWebComponent {

        private $_image_control;

        public $caption;
        public $type; #(button|submit|reset)
        private $_action;
        public $value;

        function __construct($aName,$aType,$aCaption,$aAction,$aId,$aClass,$aTitle) {

            $this->type = $aType;
            $this->TagName = 'button';
            $this->caption = $aCaption;

            parent::__construct($aName,$aId,$aClass,$aTitle);

            if (is_null($aAction)) {
                $this->_action = $this->Name;
            } else {
                $this->_action = $aAction;
            }

        }

        function setCaption($aValue){
            $this->caption = $aValue;
        }

        function getCaption(){
            return $this->caption;
        }

        function setAction($aValue){
            $this->_action = $aValue;
        }

        function getAction(){
            return $this->_action;
        }

        function setImage(TBaseWebComponent $aImage){
            $this->_image_control = $aImage;
        }

        protected function createTagParams(){   

            $theParams = array();
            (!empty($this->_action)) ? $theParams['name'] = $this->_action : null;
            (!empty($this->type)) ? $theParams['type'] = $this->type : null;
            (!empty($this->value)) ? $theParams['value'] = $this->value : null;

            $theBaseParams = parent::createTagParams();     
            return array_merge((array)$theParams,(array)$theBaseParams);

        }

        public function onShow(){

            if (is_null($this->_image_control)){
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption);
            } else {
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption);
                $this->_image_control->Show();
                echo $this->closeTag($this->TagName);
            }

        }

    }


    class TSubmitButton extends TGenericButton {

        function __construct($aName,$aCaption,$aAction=null,$aId=null,$aClass=null,$aTitle=null) {

            parent::__construct($aName,'submit',$aCaption,$aAction,$aId,$aClass,$aTitle);
        }
    }

    class TResetButton extends TGenericButton {

        function __construct($aName,$aCaption,$aAction,$aId,$aClass,$aTitle) {

            parent::__construct($aName,'reset',$aCaption,$aAction=null,$aId=null,$aClass=null,$aTitle=null);
        }
    }

    class TButton extends TGenericButton {

        function __construct($aName,$aCaption,$aAction,$aId,$aClass,$aTitle) {

            parent::__construct($aName,'button',$aCaption,$aAction=null,$aId=null,$aClass=null,$aTitle=null);
        }
    }


    class TSubmitImage extends TBaseWebComponent{

        public $alt;
        public $src;
        public $type; #(image)
        private $_action;
        public $value;
        
        function __construct($aUrlImage,$aAlternativeText,$aId=null,$aClass=null) {

            $this->type = 'image';
            $this->TagName = 'input';
            $this->src = $aUrlImage;
            $this->alt = $aAlternativeText;
            $this->id = $aId;    
            $this->class = $aClass;

            parent::__construct($aName);

        }
        
        function setUrlImage($aValue){
            $this->src = $aValue;
        }

        function getUrlImage(){
            return $this->src;
        }

        function setAction($aValue){
            $this->_action = $aValue;
        }
        
        function getAction(){
            return $this->_action;
        }


        protected function createTagParams(){   

            $theParams = array();
            (!empty($this->_action)) ? $theParams['name'] = $this->_action : null;
            (!empty($this->type)) ? $theParams['type'] = $this->type : null;
            (!empty($this->value)) ? $theParams['value'] = $this->value : null;
            (!empty($this->src)) ? $theParams['src'] = $this->src : null;
            (!empty($this->alt)) ? $theParams['alt'] = $this->alt : null;

            $theBaseParams = parent::createTagParams();     
            return array_merge((array)$theParams,(array)$theBaseParams);

        }

        public function onShow(){

            if (is_null($this->_image_control)){
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption);
            } else {
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption);
                $this->_image_control->Show();
                echo $this->closeTag($this->TagName);
            }

        }
        
    }

?>