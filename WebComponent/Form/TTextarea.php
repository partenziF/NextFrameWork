<?php

    if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);

    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');

    class TTextarea extends TBaseInputWebComponent {

        public $cols;
        public $rows;

        public $readonly; //for text and passwd --
        //? implementare con js public $maxlength; //max chars for text fields --        


        protected function createTagParams(){   

            $theParams = array();


            (!empty($this->cols)) ? $theParams['cols'] = $this->cols : $theParams['cols'] = 40;
            (!empty($this->rows)) ? $theParams['rows'] = $this->rows : $theParams['rows'] = 4;


            $theBaseParams = parent::createTagParams();     
            unset($theBaseParams['value']);
            return array_merge((array)$theParams,(array)$theBaseParams);

            //(!empty($this->maxlength)) ? $theParams['maxlength'] = $this->maxlength : null;

            //$theBaseParams = parent::createTagParams();
            //return array_merge((array)$theParams,(array)$theBaseParams);
//            return $theParams;

        }



        function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

            $this->TagName = 'textarea';        
            //$this->type = 'text';
            parent::__construct($aName,$aId,$aClass,$aTitle);
        }


        public function onShow(){
            //$this->Value = preg_replace('<br />','\n',$this->Value);
            echo $this->opencloseTag($this->TagName,self::createTagParams(),$this->Value);

        }


    }

?>
