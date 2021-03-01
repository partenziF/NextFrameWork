<?php
    define('FIELD_LIST_ALL',1);
    define('FIELD_LIST_ONLY_PK',2);
    define('FIELD_LIST_NOT_PK',3);
    interface IDatabaseDataLayer {


        public function getPrimaryKey();
        public function getPrimaryKeyValue($asArray=true);
        public function issetPrimaryKey();

        public function getFields($FieldsType=FIELD_LIST_ALL);

    }

    class DatabaseDataLayer implements IDatabaseDataLayer,IteratorAggregate {

        static public function getClassname(){

            return get_called_class();

        }

        public function getPrimaryKey(){}
        public function getPrimaryKeyValue($asArray=true){}
        public function issetPrimaryKey(){

            $result = true;
            foreach ($this->getPrimaryKeyValue() as $KeyValue){
                $result = ($result && ((isset($KeyValue)) && (!is_null($KeyValue))) );
            }

            return $result;

        }

        public function setPrimaryKey(){}

        public function getFields($FieldsType=FIELD_LIST_ALL){}
        public function getIterator(){}

        function getTableName(){}


    }

?>