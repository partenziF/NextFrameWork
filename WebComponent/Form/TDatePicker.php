<?php
if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);
#http://www.mattkruse.com/javascript/calendarpopup/
#http://www.javascripttoolbox.com/lib/contextmenu/source.php
#http://www.kelvinluck.com/assets/jquery/datePicker/v2/demo/

require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');

class TDatePicker extends TBaseInputWebComponent {

    private $MonthName = array(1=>'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
    private $YearRange = array();
    public $NullValue = false;    

    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'span';        
        $this->type = 'text';
        parent::__construct($aName,$aId,$aClass,$aTitle);
        $this->YearRange = range(date('Y'),date('Y')+1,1);
        $this->DataType = 'date';
        $this->DefaultValue = time();
    }
    
    public function setDefaultValue($aValue){
        if (is_null($aValue)) $this->NullValue = true;
        parent::setDefaultValue($aValue);
    }

    public function setMonthName(array $MonthName){
        foreach ($MonthName as $k=>$Month){
            $this->MonthName[$k] = $MonthName;
        }
    }

    public function setYear($low,$high=null){
        if (is_null($high)){
            $this->YearRange = $low;
        } else {
            $this->YearRange = range($low,$high,1);
        }
    }

    protected function createTagParams(){   

        $theParams = array();

        (!empty($this->multiple)) ? $theParams['multiple'] = $this->multiple : null;

        $theBaseParams = parent::createTagParams();     
        unset($theBaseParams['value']);
        unset($theBaseParams['type']);


        return array_merge((array)$theParams,(array)$theBaseParams);

    }


    public function onShow(){

        if (!is_null($this->Value)){
            if (!is_numeric($this->Value)){
                $this->Value = strtotime($this->getValue());
            }
        }
        //echo $this->openTag($this->TagName,$theBaseParams);

        $theBaseParams = $this->createTagParams();
        if (is_array($theBaseParams['id'])) {$theBaseParams['id'] = $theBaseParams['id'][0];}
        $theBaseParams['name'] = $theBaseParams['name'].'['.TGenericRequest::getDayKey().']';
        
        if (is_string($this->style)){ $theBaseParams['style'] = $this->style;}
        else if (is_array($this->style)) {$theBaseParams['style'] = $this->style[0];}
        #$theBaseParams['style'] = 'float:left;width:4em;margin-right:0.4em;display:inline';
        echo $this->openTag('select',$theBaseParams);

        if (($this->NullValue==true)) {
            $OptionParams['value'] = '';
            echo $this->opencloseTag('option',$OptionParams,null);            
        }

        for ($i=1;$i<=31;$i++) {
            $OptionParams = array();

            $OptionParams['value'] = $i;
            if ((!is_null($this->Value)) || (!is_null($this->DefaultValue)) ){
                if (!is_array(($this->Value))) {
                    ($i == date('j',((is_null($this->Value))? $this->DefaultValue : $this->Value))) ? $OptionParams['selected'] = 'selected' : null;
                }
            }

            echo $this->opencloseTag('option',$OptionParams,$i);

        }

        echo $this->closeTag('select');

        ############################################################################################

        $theBaseParams = $this->createTagParams();
        if (is_array($theBaseParams['id'])) {$theBaseParams['id'] = $theBaseParams['id'][1];}
        $theBaseParams['name'] = $theBaseParams['name'].'['.TGenericRequest::getMonthKey().']';
        
        if (is_string($this->style)){ $theBaseParams['style'] = $this->style;}
        else if (is_array($this->style)) {$theBaseParams['style'] = $this->style[1];}
        #$theBaseParams['style'] = 'float:left;width:10em;margin-right:0.4em;display:inline;';
        echo $this->openTag('select',$theBaseParams);

        if (($this->NullValue==true)) {
            $OptionParams['value'] = '';
            echo $this->opencloseTag('option',$OptionParams,null);            
        }        

        for ($i=1;$i<=12;$i++) {
            $OptionParams = array();

            $OptionParams['value'] = $i;
            if ((!is_null($this->Value)) || (!is_null($this->DefaultValue)) ){
                if (!is_array(($this->Value))) {                
                    ($i == date('n',((is_null($this->Value))? $this->DefaultValue : $this->Value))) ? $OptionParams['selected'] = 'selected' : null;
                }
            }

            echo $this->opencloseTag('option',$OptionParams,$this->MonthName[$i]);

        }

        echo $this->closeTag('select');

        ############################################################################################

        $theBaseParams = $this->createTagParams();
        if (is_array($theBaseParams['id'])) {$theBaseParams['id'] = $theBaseParams['id'][2];}
        $theBaseParams['name'] = $theBaseParams['name'].'['.TGenericRequest::getYearKey().']';

        if (is_string($this->style)){ $theBaseParams['style'] = $this->style;}
        else if (is_array($this->style)) {$theBaseParams['style'] = $this->style[0];}
        
        #$theBaseParams['style'] = 'float:left;width:auto;display:inline;';
        echo $this->openTag('select',$theBaseParams);

        if (($this->NullValue==true)) {
            $OptionParams['value'] = '';
            echo $this->opencloseTag('option',$OptionParams,null);            
        }        

        foreach ($this->YearRange as $v) {
            $OptionParams = array();

            $OptionParams['value'] = $v;
            if ((!is_null($this->Value)) || (!is_null($this->DefaultValue)) ){
                if (!is_array(($this->Value))) {
                    ($v == date('Y',((is_null($this->Value))? $this->DefaultValue : $this->Value))) ? $OptionParams['selected'] = 'selected' : null;
                }
            }

            echo $this->opencloseTag('option',$OptionParams,$v);

        }

        echo $this->closeTag('select');
        //echo $this->closeTag($this->TagName);

    }


}
