<?php

    interface ISqlParam {
        public function __construct($aValue);
        public function getValue();
        function __toString();

    }

    abstract class SqlParam implements ISqlParam{
        private $Value;

        public function __construct($aValue){
            $this->Value;
        }

        function getValue(){
            return $this->Value;
        }


    };


    class SqlParamString extends SqlParam{
        public function __construct($aValue){
            settype($aValue,'string');
            parent::__construct($aValue);
        }
        public function __toString(){
            return '"'.$this->getValue().'"';
        }
    }

    class SqlParamInteger extends SqlParam{
        public function __construct($aValue){
            settype($aValue,'integer');
            parent::__construct($aValue);
        }
        public function __toString(){
            return '"'.$this->getValue().'"';
        }
    }

    class SqlParamDouble extends SqlParam{
        public function __construct($aValue){
            settype($aValue,'double');
            parent::__construct($aValue);
        }
        public function __toString(){
            return '"'.$this->getValue().'"';
        }
    }

    class SqlParamBoolean extends SqlParam{
        public function __construct($aValue){
            settype($aValue,'boolean');
            parent::__construct($aValue);
        }
        public function __toString(){
            return '"'.$this->getValue().'"';
        }
    }

    interface ISqlFunction {

        public function __construct($functionName);
        function addParam($aValue);    
        function getValue($aValue);
        function __toString();
    }

    interface ISqlStatement{
        public function __construct($sqlStatement);
        function __toString();
    }

    abstract class SqlFunction implements ISqlFunction {

        private $functionName;
        private $functionParams = array();

        public function __construct($functionName){

            $this->functionName = $functionName;            

            if (func_num_args()>1){
                $args = func_get_args();
                for($i=1;$i<func_num_args();$i++){

                    if (is_scalar($args[$i]) ) {

                        $this->addParam($args[$i]);

                    } else if (is_array($args[$i])) {

                        } else if (is_object($args[$i])) {

                            } else if (is_null($args[$i])){
                                $this->addParam(null);
                            }
                }
            }

        }        

        function addParam($aValue){
            $this->functionParams[] = $aValue;
        }

        function getValue($aValue){
            return $aValue;
        }

        function __toString(){
            $code = $this->functionName;

            if (empty($this->functionParams)){

                $code .= '()';

            } else {

                $p = array();

                foreach ($this->functionParams as $value){
                    $p[] = $this->getValue($value);
                }

                $sp = join(',',$p);
                $code .= '('.$sp.')';
            }

            return $code;
        }

    }
    abstract class SqlStatement implements ISqlStatement {
        private $Statement;
        public function __construct($sqlStatement){
            $this->Statement = $sqlStatement;
            
        }
        function __toString(){
            return $this->Statement;            
        }

    }
    
    class SqlParams implements ArrayAccess,IteratorAggregate {

        private $Params;

        //public function __construct(array $aParams = null){
        public function __construct() {
            $aParams = func_get_args();
            if (!is_null($aParams)) {
         
                $this->Params = $aParams;
                
            } else {
                $this->Params = array();
            }
        }

        public function offsetSet($offset, $value) {
            if (is_null($offset)) {
                $this->Params[] = $value;
            } else {
                $this->Params[$offset] = $value;
            }
        }
        public function offsetExists($offset) {
            return isset($this->Params[$offset]);
        }
        public function offsetUnset($offset) {
            unset($this->Params[$offset]);
        }
        public function offsetGet($offset) {
            return isset($this->Params[$offset]) ? $this->Params[$offset] : null;
        }        

        public function getIterator() {
            return new ArrayIterator($this->Params);
        }        
    }


?>
