<?php

    abstract class DataSet implements ArrayAccess, Iterator, Countable {

        protected $hDatabase;

        protected $currentIndex;
        private $currentData;
        private $isValid = true;
        private $ddlClassname;

        public function setDataLayer($aClassname){
            $this->ddlClassname = $aClassname;
        }


        //Region ArrayAccess
        function offsetExists($offset){

            $this->currentIndex = 0;

            $result = $this->hDatabase->select(1,$offset);
            $this->isValid = ($result!==false);
            $this->currentData = $result;
        }

        function offsetGet($offset) {

            if (is_null($offset)){

                if ($this->hDatabase->select()===true){
                    return $this->hDatabase->sqlRead($this->ddlClassname);
                } else { 
                    return false;
                }

            } else if (is_string($offset)){

                    list($theRowCount,$theOffset) = $explode(',',$offset);                
                    if ($this->hDatabase->select($theRowCount,$theOffset)===true){
                        return $this->hDatabase->sqlRead($this->ddlClassname);
                    } else { 
                        return false;
                    }

                } else {
                    if ($this->hDatabase->select(1,$offset)===true){
                        return $this->hDatabase->sqlRead($this->ddlClassname);
                    } else { 
                        return false;
                    }
            }


        }

        function offsetSet($offset,$value) { throw new Exception("This collection is read only."); }

        function offsetUnset($offset) { throw new Exception("This collection is read only."); }
        //EndRegion

        //Region Countable
        function count(){

            return $this->hDatabase->getRowCount();

        }
        //EndRegion

        //Region Iterator
        function current(){ return $this->currentData; }

        function key() {

            if ((is_object($this->currentData)) and ($this->currentData instanceof IDatabaseDataLayer)){
                return $this->currentData->getPrimaryKeyValue(false);
            }  else {
                return $this->currentIndex;
            }

        }

        function next(){

            $row_data = $this->hDatabase->sqlRead($this->ddlClassname);

            if ((is_object($row_data)) and ($row_data instanceof IDatabaseDataLayer)){
                $this->isValid = true;
                $this->currentData = $row_data;
                return $this->currentIndex++;
            } else {
                if (!is_null($row_data)){
                    $this->isValid = true;
                    $this->currentData = $row_data;
                    return $this->currentIndex++;
                } else {
                    $this->currentData = null;
                    $this->isValid = false;

                }

            }

        }

        function rewind(){

            if ($this->hDatabase->select()){

                $row_data = $this->hDatabase->sqlRead($this->ddlClassname);

                if ((is_object($row_data)) and ($row_data instanceof IDatabaseDataLayer)){                    
                    $this->isValid = true;
                    $this->currentData = $row_data;
                    return $this->currentIndex++;
                } else {
                    //                    $this->isValid = false;
                    if (!is_null($row_data)){
                        $this->isValid = true;
                        $this->currentData = $row_data;
                        return $this->currentIndex++;
                    } else {
                        $this->currentData = null;
                        $this->isValid = false;

                    }
                }

                $this->currentIndex = 0;

            } else {
                $this->isValid = false;    
            }

        }

        function valid() { return $this->isValid; }

        function append($value) { throw new Exception("This collection is read only"); }

        function getIterator() { return $this; }


    }

?>