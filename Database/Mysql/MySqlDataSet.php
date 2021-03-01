<?php

    require_once(PATH_TO_FRAMEWORK_DATABASE_INTERFACES.'Dataset.php');
    
    class MySqlDataSet extends DataSet {

        public function __construct(IDatabase $i){

            $this->hDatabase = $i;
            $this->currentIndex = 0;

        }

    }

?>