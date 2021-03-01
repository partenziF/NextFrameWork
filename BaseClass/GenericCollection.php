<?php
    class GenericCollection extends ArrayObject{
        private $_storage;
        function __construct(){
            $this->_storage = new SplObjectStorage();
        }

        function addObject($_id, $_object){
            $_thisItem = new CollectionObject($_id, $_object);
            $this->_storage->attach($_thisItem);
        }
        function deleteObject($item){
            $this->_storage->detach($item);
        }
        function getObject($_id){
            $_thisObject = $this->_storage[$_id];
            return $_thisObject->getObject();
        }
        function count() {
            return $this->_storage->count();
            //print_r($this->data);
        }
    }

    class CollectionObject {
        private $id;
        private $object;

        function __construct($_id, $_object){
            $this->id = $_id;
            $this->object = $_object;
        }
        function getObject(){
            return $this->object;
        }
        function printObject() {
            //print_r($this);
        }
    }  


    class ClassCollection implements Iterator,ArrayAccess,Countable{
        private $_Collection;
        private $ClassName;

        public function __construct($aClassName){
            $this->_Collection = new SplObjectStorage();
            $this->ClassName = array($aClassName);
            if (func_num_args()>1){
                for($i=1;$i<func_num_args();$i++){
                    $this->ClassName[] = func_get_arg($i);                
                }
            }
        }

        function rewind() {
            return $this->_Collection->rewind();
        }
        function current() {
            return $this->_Collection->current();
        }
        function key() {
            return $this->_Collection->key();
        }
        function next() {
            return $this->_Collection->next();
        }
        function valid() {
            return $this->_Collection->valid();
        }    
        public function offsetSet($offset, $value) {
            if (!is_null($offset)) {
                if (in_array(get_class($offset),$this->ClassName)) {
                    $this->_Collection->attach($offset);
                }
            }
        }
        public function offsetExists($offset) {
            return $this->_Collection->contains($offset);
        }
        public function offsetUnset($offset) {        
            return $this->_Collection->detach($offset);
        }
        public function offsetGet($offset) {
            return ($this->_Collection->contains($offset))?$this->_Collection[$offset] : null;
        }    

        public function count(){
            return $this->_Collection->count();
        }

        public function __clone() {
            $this->_Collection = clone $this->_Collection;
        }

    }


?>