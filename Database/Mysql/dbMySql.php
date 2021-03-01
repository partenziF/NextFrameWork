<?php

    require_once(PATH_TO_FRAMEWORK_DATABASE_INTERFACES.'IDatabase.php');
    require_once(PATH_TO_FRAMEWORK_DATABASE_INTERFACES.'ISqlParam.php');

    if (!defined('MYSQL_NEW_LINK')) DEFINE('MYSQL_NEW_LINK',true);
    if (!defined('MYSQL_LOG_QUERY')) DEFINE('MYSQL_LOG_QUERY',false);
    if (!defined('MYSQL_LOG_ERROR')) DEFINE('MYSQL_LOG_ERROR',false);
    if (!defined('MYSQL_LOG_ERROR_FORMAT')) DEFINE('MYSQL_LOG_ERROR_FORMAT','%q');

    class MySqlFunction extends SqlFunction {

        public function getValue($aValue){

            if (is_string($aValue)) {
                return '"'.mysql_real_escape_string($aValue).'"';
            } else {             
                if (is_null($aValue)){
                    return 'null';
                } else {
                    return $aValue;
                }
            }

        }

    }

    class MySqlStatement extends SqlStatement{

    }


    class dbMySql implements IDatabase {

        private $mysql_errno;
        private $mysql_error;
        private $mysql_info;
        private $mysql_affected_rows;

        private $hDatabase;
        private $hResultset;

        protected $sqlQuery;

        private $Debug;
        private $newLink;
        private $FetchType;
        private $isConnected;


        public function __construct($aDebug=false,$aNewLink=MYSQL_NEW_LINK,$aFetchType=MYSQL_ASSOC){
            $this->isConnected = false;
            $this->Debug = $aDebug;
            $this->newLink = $aNewLink;
            $this->FetchType = $aFetchType;
        }

        public function connect($aHost,$aUsername,$aPassword,$aDatabase) {

            if (!$this->isConnected){

                $this->hDatabase = @mysql_connect($aHost,$aUsername,$aPassword,$this->newLink);                

                if (is_resource($this->hDatabase)){

                    if (!is_null($aDatabase)){
                        $this->setDatabase($aDatabase);                        
                    } else {
                        $this->isConnected = true;
                    }

                } else {                    
                    Throw new Exception('Error on connect to database.'); 
                    $this->isConnected = false;
                }

                //mysql_set_charset if ($isUTF8) $this->sqlEsegui('SET CHARACTER SET utf8');

                return $this->isConnected;

            }

        }

        public function setDatabase($aDatabase){

            if(!mysql_select_db($aDatabase,$this->hDatabase)) {
                //$this->error("Errore selezione database");
                $this->isConnected = false;
            }else{

                $this->isConnected = true;

            }


        }

        public function setQuey($aQuery,ArrayAccess $sqlParams=null){
            $this->setQuery($aQuery,$sqlParams);
        }
        
        public function setQuery($aQuery,ArrayAccess $sqlParams=null){

            $this->sqlQuery = $aQuery;

            if (!is_null($sqlParams)){

                foreach ($sqlParams as $value){

                    $this->sqlSetParam($value);

                }

            }

            if (MYSQL_LOG_QUERY){                
                echo '<div style="display:block;width:100%;white-space:pre;font-size:12px;padding:8px 12px;font-family:monospace;background-color:#FFFFFF;border-bottom:1px dotted #000000;">';
                echo (htmlentities(trim($this->sqlQuery)).(substr(trim($this->sqlQuery), -1)==';'?'':';'));
                echo '</div>';
                flush();
            }

        }

        public function select($rowCount=null,$offset=null,$bufferedQuery=false){

            $this->mysql_errno = NULL;
            $this->mysql_error = NULL;
            $this->mysql_affected_rows = NULL;
            $result = false;

            if (is_resource($this->hDatabase)) {

                if (is_resource($this->hResultset)) mysql_free_result($this->hResultset);
                $this->hResultset = null;

                $Limit = '';
                if (!is_null($rowCount)) {
                    $Limit .= ' LIMIT '.$rowCount;
                    if (!is_null($offset)) $Limit .= ' OFFSET '.$offset;
                }

                if ($bufferedQuery) {
                    $this->hResultset = mysql_query($this->sqlQuery.$Limit,$this->hDatabase);
                } else {
                    $this->hResultset = mysql_unbuffered_query($this->sqlQuery.$Limit,$this->hDatabase);
                }

                if (is_resource($this->hResultset)){
                    return true;
                } else {
                    $this->setError();
                    if ($this->mysql_errno){
                        $result = false;    
                    } else {
                        $result = NULL;
                    }

                }

            } else {

                Throw new Exception('Invalid database resource.'); 
                $this->isConnected = false;

            }

            return $result;

        }

        public function getRowCount(){
            $count = 1;
            if (stristr($this->sqlQuery,'SQL_CALC_FOUND_ROWS')=== false) $sqlQuery = preg_replace('/^SELECT/i','SELECT SQL_CALC_FOUND_ROWS',trim($this->sqlQuery));
            else $sqlQuery = $this->sqlQuery;
            $rs = mysql_query($sqlQuery,$this->hDatabase);
            if ($rs!==false){
                $rs = mysql_query('SELECT FOUND_ROWS(); ',$this->hDatabase);
                $r = mysql_fetch_array($rs,$this->FetchType);
                settype($r['FOUND_ROWS()'],'int');
                return $r['FOUND_ROWS()'];
            } else {
                return false;
            }

        }

        public function sqlRead($aClassName=null){
            if ($this->hResultset) {
                if (!empty($aClassName)){
                    $result = mysql_fetch_object($this->hResultset,$aClassName);
                    if (($result===false) and (is_null($this->mysql_errno))) {
                        return NULL;
                    } else {
                        return $result;
                    }
                } else {
                    $result = mysql_fetch_array($this->hResultset,$this->FetchType);
                    if (($result===false) and (is_null($this->mysql_errno))) {
                        return NULL;
                    } else {
                        return $result;
                    }
                }
            } else {
                return NULL;
            }

        }

        public function execute(){

            $this->mysql_errno = NULL;
            $this->mysql_error = NULL;
            $this->mysql_affected_rows = NULL;
            $result = false;

            if (is_resource($this->hDatabase)) {

                if (is_resource($this->hResultset)) mysql_free_result($this->hResultset);
                $this->hResultset = null;

                $result = mysql_query($this->sqlQuery,$this->hDatabase);
                $this->setError();

                if ($result !== false) {

                    $result = $this->mysql_affected_rows;
                }

            } else {
                Throw new Exception('Invalid database resource.'); 
                $this->isConnected = false;
            }

            return $result;

        }

        public function getLastId(){
            if ($this->hDatabase){               
                return mysql_insert_id($this->hDatabase);
            }else{
                return FALSE;
            }

        }

        public function sqlSetParam($Value,$ParamName='?'){

            // Trova tutti i punti interrogativi non preceduti dal carattere escape \$pattern = "/(?<!\\\\)\?/";

            if (is_scalar($Value)) {

                switch (gettype($Value)){

                    case "string":

                        // se nella stringa ci sono barre la funzione mysql_real_escape_string non funziona $Value = str_replace('\\','\\\\',$Value);
                        // metto le slashes perche' su preg_replace me le interpreta e le elimina
                        $Value = '"'.(mysql_real_escape_string($Value,$this->hDatabase)).'"';

                        // se nel campo valore ci sono ? allora devono essere ignorati come markup per i parametri
                        $Value = str_replace("?","\?",$Value);

                        $replaceString = $Value;

                        break;

                        break;
                    case "boolean":
                        ($Value) ? $replaceString=1 : $replaceString=0; 
                        break;
                    case "double":
                        //$v = localeconv();
                        //$replaceString = $Value;
                        $old = setlocale(LC_NUMERIC,0);
                        setlocale(LC_NUMERIC,'en');
                        //set settype($replaceString,'double');
                        $replaceString = (string)$Value;
                        #$decimal_point = ','; #$v[decimal_point]
                        setlocale(LC_NUMERIC,$old);
                        #$thousands_sep = '.'; #$v[thousands_sep]
                        #$replaceString = str_replace($thousands_sep,'',$Value);
                        #$replaceString = str_replace($decimal_point,'.',$replaceString);
                        //settype($replaceString,'double'); Attenzione se il locale è italia allora crea 
                        break;
                    default:
                        $replaceString = ($Value);
                }
            } else if (is_array($Value)){

                    if (!empty($Value)){

                        foreach ($Value as $theValue){

                            if (is_string($theValue)){

                                $theValue = '"'.(mysql_real_escape_string($theValue,$this->hDatabase)).'"';
                                // se nel campo valore ci sono ? allora devono essere ignorati come markup per i parametri
                                $theValue = str_replace("?","\?",$theValue);

                            }

                            $_replaceString[] = $theValue;

                    }
                    $replaceString = join(',',$_replaceString);  
                } else {
                    $replaceString = '';
                }
            } else if (is_null($Value)){
                    $replaceString = "NULL";                 
                } else if ($Value instanceof ISqlFunction) {

                        $replaceString = (string)($Value);

                    } else if ($Value instanceof ISqlStatement) {

                            $replaceString = (string)($Value);

                        }


                        $prec = '';
            $pos = strpos($this->sqlQuery,'?');

            /*            for($i = 0; $i < strlen($this->sqlQuery); $i++){
            if ($i>0){ $prec = $this->sqlQuery{$i-1}; }
            if ( ($prec!='\\') && ($this->sqlQuery{$i}=='?') ){
            $pos = $i;
            break;
            }
            }*/


            while ($pos!==false) {
                if (($pos>0) and ($this->sqlQuery{$pos-1}!='\\')) {
                    $this->sqlQuery = substr_replace($this->sqlQuery,$replaceString,$pos,1);
                    $pos = false;
                } else {
                    $pos = strpos($this->sqlQuery,'?',$pos+1);
                }

            }

            /*            if ($pos !== false){
            if (($pos>0) and ($this->sqlQuery{$pos-1}!='\\')) {
            $this->sqlQuery = substr_replace($this->sqlQuery,$replaceString,$pos,1);
            } else {

            }
            }*/

        }

        public function getError($aFormat=null){
            if (is_null($aFormat)) {
                return __CLASS__.' ['.$this->mysql_errno.'] '.$this->mysql_error;
            } else {
                return str_replace(array('%q','%c','%n','%e','%r'),array($this->sqlQuery, __CLASS__,$this->mysql_errno,$this->mysql_error,$this->mysql_affected_rows),$aFormat);
            }                        
        }

        function setError() {

            if ($this->hDatabase) {

                $this->mysql_error = mysql_error($this->hDatabase);
                $this->mysql_errno = mysql_errno($this->hDatabase);
                $this->mysql_info = mysql_info($this->hDatabase);
                $this->mysql_affected_rows = mysql_affected_rows($this->hDatabase);

                if (MYSQL_LOG_ERROR){
                    if ($this->mysql_errno!=0){
                        echo '<div style="display:block;width:100%;white-space:pre;font-size:12px;padding:8px 12px;font-family:monospace;background-color:#FFFFFF;border-bottom:1px dotted #000000;">';
                        #echo (htmlentities(trim($this->sqlQuery)).(substr(trim($this->sqlQuery), -1)==';'?'':';'));

                        echo htmlentities($this->getError(MYSQL_LOG_ERROR_FORMAT));

                        echo '<span style="color:#C70707">';
                        echo "\r\n mysql_error: ({$this->mysql_errno}):{$this->mysql_error}\r\n";
                        echo '</span>';
                        echo '</div>';

                    }
                }

            }
        }

        public function sqlBegin(){
            $this->setQuery('BEGIN');
            $r = $this->execute();
        }
        public function sqlCommit(){
            $this->setQuery('COMMIT');
            $r = $this->execute();
        }
        public function sqlRollback(){
            $this->setQuery('ROLLBACK');
            $r = $this->execute();
        }

        public function __destruct(){

            if (is_resource($this->hResultset)) mysql_free_result($this->hResultset);
            if (is_resource($this->hDatabase)) mysql_close($this->hDatabase);
            $this->isConnected = false;
        }

    }

?>