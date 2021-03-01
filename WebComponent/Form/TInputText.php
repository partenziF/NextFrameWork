<?php
//http://decorplanit.com/plugin/ Plugin per jquery per validare input numeri 
if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');

class TInputText extends TBaseInputWebComponent {

    public $NullAllowed;


    public $readonly; //for text and passwd --
    public $maxlength; //max chars for text fields --


    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'input';        
        $this->type = 'text';
        parent::__construct($aName,$aId,$aClass,$aTitle);
    }

    protected function createTagParams(){   

        $theParams = array();
        (!empty($this->readonly)) ? $theParams['readonly'] = $this->readonly : null;
        (!empty($this->maxlength)) ? $theParams['maxlength'] = $this->maxlength : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }


    public function onShow(){

        echo $this->emptyTag($this->TagName,$this->createTagParams());

    }

    public static function getJSNumberValidation($functionName='inputmask_int'){
        return <<<quote
            function $functionName(input){
                var num = input.value.replace(/\,/g,'');
                if(!isNaN(num)){
                    if(num.indexOf('.') > -1) {
                        input.value = input.value.substring(0,input.value.length-1);
                    }
                } else {
                    input.value = input.value.substring(0,input.value.length-1);
                }
            }               
quote;
    }

    public static function getJSDecimalValidation($functionName='inputmask_double'){
        return <<<quote
            function $functionName(input){
                var num = input.value.replace(/\,/g,'');
                if(!isNaN(num)){
                    if(num.indexOf('.') > -1){
                        num = num.split('.');
                        num[0] = num[0].toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1,').split('').reverse().join('').replace(/^[\,]/,'');
                        if(num[1].length > 2){

                            num[1] = num[1].substring(0,num[1].length-1);
                        } input.value = num[0]+'.'+num[1];
                    } else {
                        input.value = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1,').split('').reverse().join('').replace(/^[\,]/,'') };
                } else {

                    input.value = input.value.substring(0,input.value.length-1);
                }
            }        
quote;
    }


}

class TInputPassword extends TBaseInputWebComponent {

    public $NullAllowed;
    //public $DefaultValue;

    public $readonly; //for text and passwd --
    public $maxlength; //max chars for text fields --

    protected function createTagParams(){   

        $theParams = array();
        (!empty($this->readonly)) ? $theParams['readonly'] = $this->readonly : null;
        (!empty($this->maxlength)) ? $theParams['maxlength'] = $this->maxlength : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }

    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'input';        
        $this->type = 'password';
        parent::__construct($aName,$aId,$aClass,$aTitle);

    }


    public function onShow(){

        echo $this->emptyTag($this->TagName,$this->createTagParams());

    }

}


/*
$this->cbObbligatorio = new TCheckbox('Obbligatorio',null,'radio');     
$this->cbObbligatorio->CheckValue = true;
$this->cbObbligatorio->setDefaultValue(false);
$this->cbObbligatorio->DataBinding('Value',$this->MainTable,'Obbligatorio');        
$this->cbObbligatorio->setDataType('boolean');

$this->lblObbligatorio = new TLabel('lblObbligatorio','Voce obbligatoria?',null,'radio');
$this->lblObbligatorio->setControl($this->cbObbligatorio);
*/










