<?php

    if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);
    DEFINE('TLABEL_POSITION_BEFORE',0);
    DEFINE('TLABEL_POSITION_AFTER',1);

    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');


    //class TLabel extends TBaseWebComponent implements IBaseInputWebComponent {
    class TLabel extends TBaseWebComponentContainer implements IBaseInputWebComponent {

        public $caption;

        public $LabelPosition = TLABEL_POSITION_BEFORE;
        public $EncloseTag;
        //public $UseBreakAfter = false;

        //private $_control;        

        public $for;
        public $accesskey;

        function __construct($aName=null,$aCaption=null,$aId=null,$aClass=null,$aTitle=null) {
            $this->TagName = 'label';            
            $this->caption = $aCaption;

            if (!is_null($aId)) $this->id = $aId;
            if (!is_null($aClass)) $this->class = $aClass;
            if (!is_null($aTitle)) $this->title = $aTitle;
            //$this->_control = array();

            parent::__construct($aName);
        }

        protected function createTagParams(){   

            $theParams = array();
            (!empty($this->for)) ? $theParams['for'] = $this->for : null;

            $theBaseParams = parent::createTagParams();     
            return array_merge((array)$theParams,(array)$theBaseParams);

        }

        public function setControl(TBaseInputWebComponent &$aWebInputControl,$aEncloseTag=true){
            if (!isset($this->EncloseTag)) $this->EncloseTag = $aEncloseTag;
            $aWebInputControl->ParentComponent = $this;
            //$this->_control[] = $aWebInputControl;
            $this[] = $aWebInputControl;
        }

        public function addComponent(TBaseWebComponent &$aWebComponent){
            $aWebComponent->ParentComponent = $this;
            //$this->_control[] = $aWebComponent;
            $this[] = $aWebComponent;
        }


        public function onShow(){


            if ($this->count()>0)  {
                /*                

                if (($this->EncloseTag) || (!isset($this->EncloseTag))) {

                switch ($this->LabelPosition){
                case TLABEL_POSITION_BEFORE:
                echo $this->openTag($this->TagName,$this->createTagParams(),$this->caption,true,false,true,true);
                //($aTagName,$aParamList=null,$aInnerText=null,$haveChild=true,$isClosed=false,$toEntities = true,$nl2br=false) 
                break;
                case TLABEL_POSITION_AFTER:
                echo $this->openTag($this->TagName,$this->createTagParams());
                break;
                }

                }  else {

                switch ($this->LabelPosition){
                case TLABEL_POSITION_BEFORE:                            
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption,true,true);

                break;
                }

                }

                foreach ($this as $control) {

                if ($control instanceof TGroupCheckbox){ 

                $LabelTagClosed = true;

                if (($this->EncloseTag) || (!isset($this->EncloseTag))) {

                switch ($this->LabelPosition){
                case TLABEL_POSITION_BEFORE:
                echo $this->closeTag($this->TagName);
                break;
                case TLABEL_POSITION_AFTER:
                echo $this->closeTag($this->TagName,$this->caption,true);
                break;
                }

                $control->Show();

                }  else {

                switch ($this->LabelPosition){

                case TLABEL_POSITION_BEFORE:
                $control->Show();                                    
                break;

                case TLABEL_POSITION_AFTER:
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption,true,true);
                $control->Show();
                break;
                }

                }                        

                } else if ($control instanceof TBaseInputWebComponent){ 

                $LabelTagClosed = true;

                if (($this->EncloseTag) || (!isset($this->EncloseTag))) {
                $control->Show();
                switch ($this->LabelPosition){
                case TLABEL_POSITION_BEFORE:
                echo $this->closeTag($this->TagName);
                break;
                case TLABEL_POSITION_AFTER:
                echo $this->closeTag($this->TagName,$this->caption,true);
                break;
                }



                }  else {

                switch ($this->LabelPosition){

                case TLABEL_POSITION_BEFORE:
                $control->Show();                                    
                break;

                case TLABEL_POSITION_AFTER:
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption,true,true);
                $control->Show();
                break;
                }

                } 

                } else {
                $control->Show();
                }

                }

                if (!isset($LabelTagClosed)){

                if (($this->EncloseTag) || (!isset($this->EncloseTag))) {

                switch ($this->LabelPosition){
                case TLABEL_POSITION_BEFORE:
                echo $this->closeTag($this->TagName);
                break;
                case TLABEL_POSITION_AFTER:
                echo $this->closeTag($this->TagName,$this->caption,true);
                break;
                }

                }  else {

                switch ($this->LabelPosition){
                case TLABEL_POSITION_AFTER:
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption,true,true);
                break;
                }

                }

                }
                */                

                echo $this->openTag($this->TagName,$this->createTagParams(),$this->caption,true,false,true,true);

                foreach ($this as $control){

                    if ($control instanceof TBaseInputWebComponent){

                        $control->Show();
                        
                    } else {

                        $control->Show();
                    }

                }

                echo $this->closeTag($this->TagName);


            }  else {
                echo $this->opencloseTag($this->TagName,$this->createTagParams(),$this->caption,true,true);
            }


        }


        public function getValue() {
            return $this->_control->getValue();
        }

        public function setValue($aValue){
            return $this->_control->setValue($aValue);
        }

        public function setDataType($aDataType){
            return $this->_control->setDataType($aDataType);
        }

        public function getInputRequest(){
            return $this->_control->getInputRequest();
        }

        public function Input($aAutoValidate=false){
            return $this->_control->Input();
        }

        public function Validate(){
            return $this->_control->Validate();
        }

        public function setDefaultValue($aValue){
            $this->DefaultValue = $aValue;
            $this->Value = $aValue;
        }

        public function issetValue(){
            return isset($this->Value);
        }


    }
?>
