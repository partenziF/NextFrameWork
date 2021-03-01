<?php

    interface IDatabase {


        public function connect($aUsername,$aPassword,$aHost,$aDatabase);

        public function setDatabase($aDatabase);

        public function setQuey($aQuery,ArrayAccess $sqlParams=null);

        public function select($rowCount=null,$offset=null,$bufferedQuery=false);

        public function getRowCount();
        public function sqlRead();

        public function execute();
        public function getLastId();

        public function sqlSetParam($Value,$ParamName='?');

        public function getError($aFormat=null);
        public function setError();

        public function sqlBegin();
        public function sqlCommit();
        public function sqlRollback();                

    }
  
?>
