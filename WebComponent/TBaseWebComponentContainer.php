<?php
if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);

require_once(PATH_TO_FRAMEWORK_BASECLASS.'MessageQueque.php');
require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');


class CollectionWebComponent extends SplPriorityQueue {

    // — Compare priorities in order to place elements correctly in the heap while sifting up.    
    public function compare($priority1, $priority2){        
        if ($priority1 === $priority2) return 0;
        return $priority1 > $priority2 ? -1 : 1;
    } 

    //public function __construct — Constructs a new empty queue
    //public function count — Counts the number of elements in the queue.
    //public function current — Return current node pointed by the iterator
    //public function extract — Extracts a node from top of the heap and sift up.
    //— Inserts an element in the queue by sifting it up.
    public function insert($value,$priority){
        if (($value instanceof TBaseWebComponent) || ($value instanceof GenericComponent)){
            parent::insert($value,$priority);
        }

    }
    //public function isEmpty — Checks whether the queue is empty.
    //public function key — Return current node index
    //public function next — Move to the next node
    //public function recoverFromCorruption — Recover from the corrupted state and allow further actions on the queue.
    //public function rewind — Rewind iterator back to the start (no-op)
    //public function setExtractFlags — Sets the mode of extraction
    //public function top — Peeks at the node from the top of the queue
    //public function valid — Check whether the queue contains more nodes

}

class GenericComponentContainer extends GenericComponent implements ArrayAccess,IteratorAggregate,Countable {

    private $_components;
    protected $_orderComponents;
    
    function __construct($aName) {

        $this->_components = new SplObjectStorage();
        parent::__construct($aName);

    }        

    public function offsetSet($offset, $item) {

        if ($item instanceof TBaseWebComponent){ 
            if (empty($offset)) $offset = $this->_components->count()+1;
            if ($item instanceof TBaseWebComponentContainer){
                $item->ParentComponent = $this;
            }
            if ($item instanceof TFieldset){
                $item->FormContainer = $this;
            } else if ($item instanceof IBaseInputWebComponent) {
                    $item->FormContainer = &$this->FormContainer;
                    if ($item instanceof TSelect){
                        $item->PrepareData();
                    }
                }
                $this->_components->attach($item,$offset);                
        
        } else if ($item instanceof GenericComponent){ 
            if (empty($offset)) $offset = $this->_components->count()+1;
            $this->_components->attach($item,$offset);            
        }

    }
    public function offsetExists($item) {
        if ($item instanceof TBaseWebComponent){ 
            return $this->_components->offsetExists($item);
        }
    }
    public function offsetUnset($item) {
        $this->_components->detach($item);
    }
    public function offsetGet($item) {
        if ($item instanceof TBaseWebComponent){ 
            return $this->_components->offsetGet($item);
        }
    }

    public function getIterator() {

        $this->_orderComponents = new CollectionWebComponent();
        foreach ($this->_components as $k=>$v){                
            $this->_orderComponents->insert($v,$this->_components[$v]);
        }
        return $this->_orderComponents;
    }
    public function count(){
        
        return $this->_components->count();
        
    }

    public function __clone() {
        $this->_components = clone $this->_components;
        if (is_object($this->_orderComponents)) {$this->_orderComponents = clone $this->_orderComponents;}
    }

    
}

class TBaseWebComponentContainer extends TBaseWebComponent implements ArrayAccess,IteratorAggregate,Countable {

    private $_components;
    protected $_orderComponents;

    public $FOnBeginIterate;
    public $FOnEndIterate;

    function __construct($aName) {

        $this->_components = new SplObjectStorage();
        //$this->TagOutput
        parent::__construct($aName);

    }        

    public function offsetSet($offset, $item) {

        if ($item instanceof TBaseWebComponent){ 
            if (empty($offset)) $offset = $this->_components->count()+1;
            if ($item instanceof TBaseWebComponentContainer){
                $item->ParentComponent = $this;
            }
            if ($item instanceof TFieldset){
                $item->FormContainer = $this;
            } else if ($item instanceof IBaseInputWebComponent) {
                    $item->FormContainer = &$this->FormContainer;
//                    if ($item instanceof TSelect){
//                        $item->PrepareData();
//                    }
                }
                $this->_components->attach($item,$offset);
        } else if ($item instanceof GenericComponent){
            if (empty($offset)) $offset = $this->_components->count()+1;
            $this->_components->attach($item,$offset);            
        }

    }
    public function offsetExists($item) {
        if ($item instanceof TBaseWebComponent){ 
            return $this->_components->offsetExists($item);
        }
    }
    public function offsetUnset($item) {
        $this->_components->detach($item);
    }

    public function offsetGet($item) {
        if ($item instanceof TBaseWebComponent){ 
            return $this->_components->offsetGet($item);
        }
    }

    public function getIterator() {

        $this->_orderComponents = new CollectionWebComponent();
        foreach ($this->_components as $k=>$v){                
            $this->_orderComponents->insert($v,$this->_components[$v]);
        }
        return $this->_orderComponents;
    }        

    public function onShow(){
        
        $counter = 0;
        foreach ($this as $component){
            
            if (isset($this->FOnBeginIterate)) $this->DispachEvent($this->FOnBeginIterate,$counter);

            $component->Show();

            if (isset($this->FOnEndIterate)) $this->DispachEvent($this->FOnEndIterate,$counter);
            
            $counter++;

        }


    }
    
    public function count(){
        
        return $this->_components->count();
    }     

}




