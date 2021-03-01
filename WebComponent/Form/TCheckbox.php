<?php
class TRadioButton extends TCheckbox{
    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'input';        

        parent::__construct($aName,$aId,$aClass,$aTitle);
        $this->type = 'radio';
    }    
}


class TCheckbox extends TBaseInputWebComponent{
    public $CheckValue;
    public $checked;     #IMPLIED  -- for radio buttons and check boxes -
    public $InitialValue;

    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'input';        
        $this->type = 'checkbox';
        parent::__construct($aName,$aId,$aClass,$aTitle);
    }    

    public function setCheckValue($aValue){
        $this->CheckValue = $aValue;
    }

    public function setInitialValue($aValue){
        $this->InitialValue = $aValue;
    }

    protected function createTagParams(){   

        $theParams = array();
        //(!empty($this->checked)) ? $theParams['checked'] = $this->checked : ((!isset($this->Value))? $this->CheckValue == $this->DefaultValue : $this->Value == $this->CheckValue );
        if (isset($this->checked)) {
            if ($this->checked) $theParams['checked'] = 'checked';
        } else {
            if (!isset($this->Value)) {
                if (isset($this->InitialValue)) {
                    if ($this->CheckValue == $this->InitialValue) { $theParams['checked'] = 'checked'; }
                } else {
                    if ($this->CheckValue == $this->DefaultValue) { $theParams['checked'] = 'checked'; }
                }
            } else  {
                if ($this->CheckValue == $this->Value) { $theParams['checked'] = 'checked'; }
            }
        }

        $theBaseParams = parent::createTagParams();
        (!is_null($this->CheckValue)) ? $theBaseParams['value'] = $this->CheckValue : null;
        return array_merge((array)$theParams,(array)$theBaseParams);

    }

    public function onShow(){

        echo $this->emptyTag($this->TagName,$this->createTagParams());

    }

    public function getInputRequest(){
        $this->checked = null;
        if (TGenericRequest::existParam($this->Name)) {
            $this->Value = TGenericRequest::getRequestValue($this->Name,$this->DataType,(is_null($this->DefaultValue)),$this->DefaultValue);
            if (is_null($this->Value)) {
                if (isset($this->DefaultValue)) $this->Value = $this->DefaultValue;
            }
            $this->checked = ($this->Value == $this->CheckValue);
            $this->InitialValue = $this->Value;
            return true;
        } else {
            if (isset($this->DefaultValue)) $this->Value = $this->DefaultValue;
            $this->checked = false;            
            $this->InitialValue = $this->Value;
            return false;
        }

    }

    public function isChecked(){
        return $this->checked;
    }


}


class TItemCheckbox extends TBaseStyleTag{
    public $CheckValue;
    public $checked;
    public $label;
    public $Id;
    public $Class;

    public function __construct($aCheckValue,$aLabel,$isChecked=false,TBaseStyleTag $aBaseStyleTag = null){
        $this->CheckValue = $aCheckValue;
        if (is_string($aLabel)){
            $this->label = new TLabel('lblfor'.$aCheckValue,$aLabel);
        } else {
            $this->label = $aLabel;
        }
        if ($isChecked) {$this->checked = $isChecked;}
        if (!is_null($aBaseStyleTag)){
            foreach ($aBaseStyleTag as $p=>$v){
                $this->$p = $v;
            }
        }
    }

}

class TArrayOfTItemCheckbox implements ArrayAccess,Iterator, Countable {

    private $_items = array();

    public function __construct(){

    }

    public function offsetSet($offset, $value) {

        if ($value instanceof TItemCheckbox ){
            if (is_null($offset)) {
                $this->_items[] = $value;
            } else {
                $this->_items[$offset] = $value;
            }
        }
    }
    public function offsetExists($offset) {
        return isset($this->_items[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->_items[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }

    public function rewind() {
        reset($this->_items);
    }
    public function current() {
        return current($this->_items);
    }
    public function key() {
        return key($this->_items);
    }
    public function next() {
        return next($this->_items);
    }
    public function valid() {
        return $this->current() !== false;
    }
    public function count() {
        return count($this->_items);
    }

}

class TGroupCheckbox extends TBaseInputWebComponent{
    private $dataset;
    public $Items;

    public $DisplayMember;
    public $ValueMember;

    public $FOnPrepareData;

    public $CheckBoxStyle;

    function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

        $this->TagName = 'input';        
        $this->type = 'checkbox';
        parent::__construct($aName,$aId,$aClass,$aTitle);
    }    


    protected function createTagParams(){   

        $theParams = array();
        if (isset($this->checked)) {
            if ($this->checked) $theParams['checked'] = 'checked';
        } else {
            if (!isset($this->Value)) {        
                if ($this->CheckValue == $this->DefaultValue) { $theParams['checked'] = 'checked'; }
            } else  {
                if ($this->CheckValue == $this->Value) { $theParams['checked'] = 'checked'; }
            }
        }

        $theBaseParams = parent::createTagParams();
        (!is_null($this->CheckValue)) ? $theBaseParams['value'] = $this->CheckValue : null;
        return array_merge((array)$theParams,(array)$theBaseParams);

    }

    public function onShow(){

        $this->FOnPrepareData();

        if (is_array($this->Items)){

            foreach ($this->Items as $k => $aItem ) {

                $OptionParams = array();
                if (is_array($this->Value)) {
                    $aItem->selected = (in_array($aItem->CheckValue,$this->Value));
                } else {
                    $aItem->selected = ($aItem->CheckValue == $this->Value);
                }

                $OptionParams['type'] = $this->type;
                $OptionParams['name'] = $this->Name.'[]';
                $OptionParams['value'] = $aItem->CheckValue;
                $OptionParams['style'] = $this->CheckBoxStyle;

                if (isset($aItem->checked)) {
                    if ($aItem->checked) $OptionParams['checked'] = 'checked';
                } else {
                    if ($this->DataType == 'bitmask') {

                        if (!isset($this->Value)) {

                            if ($this->DefaultValue == ($this->DefaultValue | $aItem->CheckValue)) { $OptionParams['checked'] = 'checked'; }

                        } else  {

                            if ($this->Value == ($this->Value | $aItem->CheckValue)) { $OptionParams['checked'] = 'checked'; }   
                        }

                    } else {

                        if (!isset($this->Value)) {
                            if (is_array($this->DefaultValue)){
                                if (in_array($aItem->CheckValue,$this->DefaultValue)){
                                    $OptionParams['checked'] = 'checked';
                                }                        
                            } else {
                                if ($aItem->CheckValue == $this->DefaultValue) { $OptionParams['checked'] = 'checked'; }
                            }
                        } else  {
                            if (is_array($this->Value)){
                                if (in_array($aItem->CheckValue,$this->Value)){
                                    $OptionParams['checked'] = 'checked';
                                }
                            } else {
                                if ($aItem->CheckValue == $this->Value) { $OptionParams['checked'] = 'checked'; }
                            }
                        }

                    }

                }

                $cssStyle = $aItem->createParamStyle();
                (!empty($cssStyle)) ? $OptionParams['style'] = $cssStyle : null;



                if (!empty($aItem->id)) $aItem->label->for = $aItem->id;

                if (($aItem->label->EncloseTag) || (!isset($aItem->label->EncloseTag))) {
                    switch ($aItem->label->LabelPosition){
                        case TLABEL_POSITION_BEFORE:
                            echo $this->openTag($aItem->label->TagName,$aItem->label->createTagParams(),$aItem->label->caption);
                            echo $this->emptyTag($this->TagName,$OptionParams);
                            echo $this->closeTag($aItem->label->TagName);
                            break;
                        case TLABEL_POSITION_AFTER:
                            echo $this->openTag($aItem->label->TagName,$aItem->label->createTagParams());
                            echo $this->emptyTag($this->TagName,$OptionParams);
                            echo $this->closeTag($aItem->label->TagName,$aItem->label->caption);
                            break;
                    }

                } else {

                    switch ($aItem->label->LabelPosition){
                        case TLABEL_POSITION_BEFORE:                            
                            echo $this->opencloseTag($aItem->label->TagName,$aItem->label->createTagParams(),$aItem->label->caption);
                            echo $this->emptyTag($this->TagName,$OptionParams);
                            break;
                        case TLABEL_POSITION_AFTER:
                            echo $this->emptyTag($this->TagName,$OptionParams);
                            echo $this->opencloseTag($aItem->label->TagName,$aItem->label->createTagParams(),$aItem->label->caption);
                            break;
                    }

                }




            }

        }




    }

    public function setDataset($aDataset){
        $this->dataset = $aDataset;
    }


    public function FOnPrepareData(){

        if (isset($this->FOnPrepareData)) {

            if ((is_null($this->Items)) || ($this->Items->count()==0)) {

                $this->dataset = $this->DispachEvent($this->FOnPrepareData);

                if (isset($this->dataset)) {

                    if ($this->NullValue){
                        $this->Items[] = new TItemSelect('','');
                    }

                    $vm = $this->ValueMember;
                    $dm = $this->DisplayMember;
                    foreach ($this->dataset as $k=>$o){

                        if ((isset($this->ValueMember)) and (isset($this->DisplayMember))) {

                            $this->Items[] = new TItemCheckbox($o->$vm,$o->$dm);

                        }

                    }

                }

            }
        }

    }

}
