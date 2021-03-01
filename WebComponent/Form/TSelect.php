<?php

    if (!defined('PATH_TO_FRAMEWORK_WEBCOMPONENT')) trigger_error('PATH_TO_FRAMEWORK_WEBCOMPONENT not defined',E_USER_ERROR);

    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseInputWebComponent.php');


    class TItemSelect {
        public $key;
        public $value;
        public $checkKey;

        public $selected;
        public $disabled;
        public $label;

        public function __construct($key,$value,$isSelected=false,$isDisabled=false,$Label=null){
            $this->key = $key;
            $this->value = $value;
            $this->selected = $isSelected;
            $this->disabled = $isDisabled;
            $this->label = $Label;
        }

    }

    class TArrayOfTItemSelect implements ArrayAccess,Iterator, Countable {

        private $_items = array();
        private $_parent;

        public function __construct($aParent){
            $this->_parent = $aParent;

        }

        public function offsetSet($offset, $value) {

            if ($value instanceof TItemSelect ){
                if (!is_null($value->label)){
                    $offset = $value->label;
                }
                if (is_null($offset)) {
                    $this->_items[] = $value;
                } else {
                    if (array_key_exists($offset,$this->_items)){
                        $this->_items[$offset][] = $value;
                    } else {
                        $this->_items[$offset] = array(0=>$value);
                    }
                }

                if ($value->selected){
                    if (!$this->_parent->issetValue){
                        if ($this->_parent->CheckKeyValue){
                            $this->_parent->setValue($value->key);
                        } else {
                            $this->_parent->setValue($value->value);    
                        }
                    }
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



    class TSelect extends TBaseInputWebComponent {

        private $dataset;
        public $Items;
        public $NullValue;
        public $CheckKeyValue;


        public $DropDownStyle;
        public $DisplayMember;
        public $ValueMember;

        public $FOnPrepareData;

        #public $size; #IMPLIED  -- rows visible --
        public $multiple; #IMPLIED  -- default is single selection --
        #  tabindex    NUMBER         #IMPLIED  -- position in tabbing order --

        #onfocus     %Script;       #IMPLIED  -- the element got the focus --
        #onblur      %Script;       #IMPLIED  -- the element lost the focus --
        #onchange    %Script;           
        public function __construct($aName,$aId=null,$aClass=null,$aTitle=null) {

            $this->TagName = 'select';
            $this->Items = new TArrayOfTItemSelect(&$this);
            $this->DropDownStyle = 'DropDownStyle';
            $this->NullValue = true;
            $this->CheckKeyValue = true;
            parent::__construct($aName,$aId,$aClass,$aTitle);

        }


        protected function createTagParams(){   

            $theParams = array();

            (!empty($this->multiple)) ? $theParams['multiple'] = $this->multiple : null;
            (!empty($this->onclick)) ? $theParams['onclick'] = $this->onclick : null;


            $theBaseParams = parent::createTagParams();     
            unset($theBaseParams['value']);

            return array_merge((array)$theParams,(array)$theBaseParams);


        }

        public function Input($AutoValidate = false){
            //$this->PrepareData();
            parent::Input($AutoValidate);
        }

        public function getDisplayValue(){
            if ($this->issetValue()){
                foreach ($this->Items as $Item)
                    if ($Item->key === $this->Value) {
                        return $Item->value;
                    }
                    return NULL;
            } else {
                return NULL;
            }
        }

        public function onShow(){

            echo $this->openTag($this->TagName,$this->createTagParams());

            foreach ($this->Items as $k => $aItem ) {

                $OptionParams = array();

                if (is_array($aItem)){

                    reset($aItem);
                    echo $this->openTag('optgroup',array('label'=>addslashes($k)));

                    do{

                        $theItem = current($aItem);
                                                
                        if ($theItem instanceof TItemSelect){

                            $theItem->selected = ($this->CheckKeyValue)?($theItem->key == $this->Value):($theItem->value == $this->Value);

                            $OptionParams['value'] = $theItem->key;
                            ($theItem->selected) ? $OptionParams['selected'] = 'selected' : null;
                            ($theItem->disabled) ? $OptionParams['disabled'] = $theItem->disabled : null;
                            (!empty($theItem->label)) ? $OptionParams['label'] = $theItem->label : null;

                            echo $this->opencloseTag('option',$OptionParams,$theItem->value);

                        }


                    } while (next($aItem)!==false);
                    
                    echo $this->closeTag('optgroup');

                } else { 

                    if ($aItem instanceof TItemSelect){

                        $aItem->selected = ($this->CheckKeyValue)?($aItem->key == $this->Value):($aItem->value == $this->Value);

                        $OptionParams['value'] = $aItem->key;
                        ($aItem->selected) ? $OptionParams['selected'] = 'selected' : null;
                        ($aItem->disabled) ? $OptionParams['disabled'] = $aItem->disabled : null;
                        (!empty($aItem->label)) ? $OptionParams['label'] = $aItem->label : null;

                        echo $this->opencloseTag('option',$OptionParams,$aItem->value);

                    }

                }

            }

            echo $this->closeTag($this->TagName);

        }

        public function setDataset($aDataset){
            $this->dataset = $aDataset;
        }


        public function PrepareData() {

            if (isset($this->FOnPrepareData)) {
                if ($this->Items->count()==0){
                    $this->dataset = $this->DispachEvent($this->FOnPrepareData);

                    if (isset($this->dataset)) {

                        if ($this->NullValue){
                            $this->Items[] = new TItemSelect('','');
                        }

                        $vm = $this->ValueMember;
                        $dm = $this->DisplayMember;
                        foreach ($this->dataset as $k=>$o){

                            if ((isset($this->ValueMember)) and (isset($this->DisplayMember))) {

                                $this->Items[] = new TItemSelect($o->$vm,$o->$dm);

                            }

                        }

                    }

                }
            }

        }


    }

?>