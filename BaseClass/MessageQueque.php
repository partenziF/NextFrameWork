<?php


    class TGenericMessage implements SplSubject {

        protected $observers;

        public $Receiver = NULL;
        public $Message;
        public $Params = array();
        public $CallBack;
        public $Result;

        public function __construct($aMessage) {

            $this->observers = new SplObjectStorage();

            $this->Message = $aMessage;

            $stack = debug_backtrace();
            $args = array();
            if (isset($stack[0]["args"]))
                $n = count($stack[0]["args"]);
            if ($n>1) {
                for($i=1; $i<$n; $i++){
                    $this->Params[] = & $stack[0]["args"][$i];
                }
            }

        }

        public function attach(SplObserver $observer) {
            $this->observers->attach($observer);
        }

        public function detach(SplObserver $observer) {
            $this->observers->detach($observer);

        }

        public function clear(){
            $this->observers->removeAll($this->observers);
        }

        public function notify() {

            foreach ($this->observers as $value) {
                $r = $value->update($this);
                if (isset($r)){
                    //array_unshift($this->Params,$r);
                    $this->Result = $r;
                }
                $this->callCallBack($this->Params);
            }

        }        

        public function setCallBack($aCallBackFunction,$aReceiver=null) {
            $this->CallBack = $aCallBackFunction;
            $this->Receiver = $aReceiver;
        }

        public function DispachMessage() {

            if (is_object($this->Receiver)) {

                if (method_exists(&$this->Receiver, $this->Message)) { 
                    $result = call_user_func_array(array(&$this->Receiver, $this->Message),&$this->Params); 
                }

            } else {

                if (function_exists($this->Message)) { 

                    if (empty($this->Params)) {
                        $result = call_user_func_array($this->Message,array(&$this,&$this->Params)); 
                    } else {
                        $result = call_user_func_array($this->Message,array_merge(array(&$this),&$this->Params)); 
                    }

                }

            }

        }

        public function callCallBack($result){

            if (!is_null($this->CallBack)) {

                if (is_object($this->Receiver)) {

                    if (method_exists(&$this->Receiver, $this->CallBack)) { 
                        //$result = call_user_func_array(array(&$this->Receiver, $this->CallBack),&$this->Result); 
                        $p = array_merge( (array)$this->Result,$this->Params);
                        $result = call_user_func_array(array(&$this->Receiver, $this->CallBack),&$p);
                    }

                } else {

                    if (function_exists($this->CallBack)) { 

                        $result = call_user_func_array($this->CallBack,array_merge(array(&$this),&$result)); 

                    }

                }

            }            
        }

        public function __toString(){
            return $this->Message;
        }

    }

    class TWebEvent extends TGenericMessage {

        protected $EventId;
        public $FOnValidateParam;


        public function __construct($aMessage,$aEventId = null) {

            parent::__construct($aMessage);
            $this->EventId = $aEventId;
            $n = func_num_args();
            if ($n>2){
                for($i=2;$i<$n;$i++){
                    $this->Params[] = func_get_arg($i);
                }
            } else if ($n == 2){
                    $this->Params = null;
                }
        }

        public function notify() {

            foreach ($this->observers as $value) {

                if (!is_null($this->EventId)){

                    if (isset($_REQUEST[$this->EventId])) {

                        if (count($this->Params)>0){

                            foreach ($this->Params as $theRequiredParam){

                                if ( (isset($_REQUEST[$theRequiredParam]))) {


                                    $r = ($r && (!is_null($this->FOnValidateParam))?$value->DispachEvent($this->FOnValidateParam,$this->EventId):true);
                                    if ($r === true){

                                        if(!isset($EventFired)){ $EventFired = true; }
                                        else { $EventFired = ($EventFired and true); }

                                    } else{
                                        $EventFired = false;
                                    }
                                }

                            }
                        } else {
                            $EventFired = true;
                        }

                    }

                } else {

                    if (count($this->Params)>0){
                        foreach ($this->Params as $theRequiredParam){

                            if ( (isset($_REQUEST[$theRequiredParam])) ) {
                                //if ( (array_key_exists($theRequiredParam,$_REQUEST)) ) {

                                $r = (!is_null($this->FOnValidateParam))?$value->DispachEvent($this->FOnValidateParam,$this->EventId):true;
                                if ($r === true){

                                    if(!isset($EventFired)){ $EventFired = true; }
                                    else { $EventFired = ($EventFired and true); }

                                } else{
                                    $EventFired = false;
                                }
                            }

                        }

                    } else {
                        $EventFired = true;
                    }

                }

                if  (isset($EventFired) and ($EventFired===true) ) {
                    $r = (!is_null($this->FOnValidateParam))?$value->DispachEvent($this->FOnValidateParam,$this->EventId):true;
                    if ($r === true){

                        $value->update($this);                    
                        $this->Result = true;

                    } else {

                        if (!is_null($this->CallBack)) {
                            $msg = new TGenericMessage($this->CallBack,$this->EventId);
                            $value->update($msg);
                        }

                        $this->Result = false;

                    }
                } else if (isset($EventFired) and ($EventFired === false)) {

                        if (!is_null($this->CallBack)) {
                            $msg = new TGenericMessage($this->CallBack,$this->EventId);
                            $value->update($msg);
                        }

                        $this->Result = false;
                }
                //array_unshift($this->Params,$r);
                //$this->callCallBack($this->Params);

            }

        }        

    }

    interface IMessageQueue {

        function Push(TGenericMessage $aTGenericMessage);
        function Post(TGenericMessage $aTGenericMessage);
        function PushInOrder(TGenericMessage $aTGenericMessage);
        function ResetPushOrder();
        function Get();
        function Process();

    }

    class MessageQueue implements IMessageQueue {

        protected $MessageQueue;
        protected $LastInsertPosition;

        function __construct(){
            $this->MessageQueue = array();
            $this->LastInsertPosition = null;
        }

        //Mette il messaggio in testa alla coda dei messaggi
        function Push(TGenericMessage $aTGenericMessage) {
            $this->LastInsertPosition = null;
            if (is_a($aTGenericMessage,'TGenericMessage'))
                array_unshift($this->MessageQueue,$aTGenericMessage);    
        }

        //Mette il messagio in coda alla lista dei messaggi
        public function Post(TGenericMessage $aTGenericMessage){
            $this->LastInsertPosition = null;
            if (is_a($aTGenericMessage,'TGenericMessage'))
                $this->MessageQueue[] = $aTGenericMessage;

        }

        public function PushInOrder(TGenericMessage $aTGenericMessage) {
            if (is_a($aTGenericMessage,'TGenericMessage')){
                if (is_null($this->LastInsertPosition)) {
                    array_unshift($this->MessageQueue,$aTGenericMessage);
                    $this->LastInsertPosition = 1;
                } else {
                    $this->MessageQueue=array_merge(array_slice($this->MessageQueue, 0,$this->LastInsertPosition), array($aTGenericMessage), array_slice($this->MessageQueue,$this->LastInsertPosition));
                    $this->LastInsertPosition++;
                }
            }

        }

        public function ResetPushOrder(){
            $this->LastInsertPosition = null;
        }

        public function Get(){

            return array_shift($this->MessageQueue);

        }

        public function Process() {

            while (!(empty($this->MessageQueue))) {

                $theMessage = $this->Get();
                $theMessage->notify();

            }


        }


    }

    class WebEventQueue extends MessageQueue{

        public function Process() {

            while (!(empty($this->MessageQueue))) {

                $theMessage = $this->Get();

                $theMessage->notify();
                if (isset($theMessage->Result)) {
                    break;
                }

            }


        }        
    }

    /*    
    class Component extends TBaseComponent implements SplObserver{

    protected $id;

    public function __construct($id){
    $this->id = $id;
    }

    public function update(SplSubject $s){
    $this->DispachEvent($s->Message,$s->Params);
    }

    public function Input($p1){
    echo __METHOD__.$p1.'<br />';
    }

    }



    $msg = new TGenericMessage('Input','afsdfas');
    $msg->attach(new Component('federico'));
    $msg->attach(new Component('federico2'));
    $msg->attach(new Component('federico3'));

    $msg->notify();
    */
    //$msg->

?>
