<?php
    interface ISqlFunction {

        public function __construct($functionName);
        function addParam($aValue);    
        function getValue($aValue);
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

    class MySqlFunction extends SqlFunction {

        public function getValue($aValue){

            if (is_string($aValue)) {
                return '"'.addslashes($aValue).'"';
            } else {
                return $aValue;
            }

        }

    }


interface IEntity {

    function setPrimaryKey();
    function issetPrimaryKey();

    function getPrimaryKey();
    function getPrimaryKeyValue($asArray=true);

    function getTableName();
}

abstract class Entity implements IEntity {

    private $Fields = array();

    static public function getClassname(){
        return get_called_class();
    }
    public function getTableName(){
        return get_class($this);
    }

    public function issetPrimaryKey(){

        $result = true;
        foreach ($this->getPrimaryKeyValue() as $KeyValue){
            $result = ($result && ((isset($KeyValue)) && (!is_null($KeyValue))) );
        }

        return $result;

    }
    function getPrimaryKeyValue($asArray=true){

        foreach ($this->getPrimaryKey() as $FieldName){
            $PrimaryKey[] = $FieldName;
        }
        if (isset($PrimaryKey)) {
            if ($asArray) {
                return $PrimaryKey;
            } else {
                return $PrimaryKey[0];
            }
        } else {
            return null;
        }

    }    
    function getPrimaryKey(){}


    function setPrimaryKey(){
        $i=0;
        foreach ($this->getPrimaryKey()as $FieldName){
            $this->$FieldName = func_get_arg($i);
            $i++;
        }

    }


}


class Utenti extends Entity  {

    public $P_Utente;
    public $Username;
    public $Password;
    public $Attivo;
    public $Visibile;
    public $Creato;
    public $Modificato;
    public $Firma;


    function getPrimaryKey(){
        return array('P_Utente');
    }


}

class EntityObjectModel extends FilterIterator {

    private $Entity;

    function __construct(Entity $aEntity){

        $this->Entity = $aEntity;
        $this->Fields = get_object_vars($this->Entity);
        $f = new ArrayObject($this->Fields);
        parent::__construct($f->getIterator());

    }

    public function accept(){
        $key = parent::key();
        return isset($this->Entity->$key);

    }


}

class eomUtenti extends EntityObjectModel {

}


$u = new Utenti();

//$u->setPrimaryKey(1);

$Utenti = new eomUtenti($u);

$u->setPrimaryKey(1);
$u->Username = 'federico';

//$Utenti->Password = 'asdfasdfasd';
/*
$myIt = new myFilter();
foreach ($myIt as $r){
echo $r;
}
*/
#$Utenti[] = 
$q = 'SELECT ';
foreach ($Utenti as $f=>$v){
    $q.="\t$f\n";
}

$q .=" FROM ".$u->getTableName();
$q .=" WHERE ".join(' AND ',$u->getPrimaryKey());




var_dump($u);
var_dump($q);

