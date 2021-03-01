<?php
    require_once(PATH_TO_FRAMEWORK_DATABASE_INTERFACES.'IDatabase.php');
    require_once(PATH_TO_FRAMEWORK_DATABASE_INTERFACES.'Dataset.php');
    require_once(PATH_TO_FRAMEWORK_DATABASE_MYSQL.'dbMySql.php');
    require_once(PATH_TO_FRAMEWORK_DATABASE_MYSQL.'MySqlDataSet.php');

    class dbFactory {

        public static function Create($aType='mysql'){

            switch ($aType){
                case 'mysql':
                    return new dbMySql();
                    break;
                default:
                    return null;
            }

        }

    }


?>
