<?php

    class WhereBuilder {

        public function __construct(){

        }

    }



    class QueryBuilder {

        public function __construct(){

        }

        public static function getInsert(DatabaseDataLayer $ddl){

            $sql =  'INSERT INTO '.$ddl->getTableName().' ('.join(',',$ddl->getFields()).') VALUES (?'.str_repeat(',?',count($ddl->getFields())-1).')';

        }

    }
?>
