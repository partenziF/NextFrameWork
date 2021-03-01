<?php
    if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponent.php');
    require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponentContainer.php');

    class ListItem extends TBaseWebComponentContainer implements ArrayAccess {
        public $ParentID;
        public $ID;
        public $Caption;
        public $isSelected = false;
        public $Childs = array();

        public function __construct($aID,$aCaption=null,$aName=null){

            $this->TagName = 'li';
            $this->ID = $aID;
            $this->Caption = $aCaption;


            parent::__construct($aName);
        }


        function __toString(){
            return (string)$this->ID;
        }

        function Show($aDepth){
            if ($this->isVisible) {
                $this->DispachEvent($this->FOnShow,$aDepth);
            }
        }


        public function onShow($aDepth){
            echo str_repeat("\t",$aDepth);
            echo $this->openTag($this->TagName,$this->createTagParams(),$this->Caption);
            parent::onShow();
            if (empty($this->Childs)){
                echo str_repeat("\t",$aDepth);
                echo $this->closeTag($this->TagName);
            }
        }


        public function getInnerIterator() {
            return null;
        }       

        public function offsetSet($offset, $value) {
            if ($value instanceof ListItem){
                if (is_null($offset)) {
                    $this->Childs[] = $value;
                } else {
                    $this->Childs[$offset] = $value;
                }
            } else {
                parent::offsetSet($offset,$value);
            }
        }
        public function offsetExists($offset) {            
            return isset($this->Childs[$offset]);

        }
        public function offsetUnset($offset) {
            unset($this->Childs[$offset]);
        }
        public function offsetGet($offset) {
            return isset($this->Childs[$offset]) ? $this->Childs[$offset] : null;
        }        

    }


    class ListRecursiveIterator extends RecursiveIteratorIterator{
        private $hGenericList;
        private $StackItem;

        function __construct( $m) {
            parent::__construct($m,RecursiveIteratorIterator::SELF_FIRST);
            $this->StackItem = array();
        }   

        public function addStack(ListItem $aElement){
            array_unshift($this->StackItem,$aElement);
        }

        public function setGenericList(GenericList $aHGenericList){
            $this->hGenericList = $aHGenericList;
        }

        function beginIteration() {

            $this->hGenericList->beginIteration();

        }

        function beginChildren() {

            $this->hGenericList->beginChildren($this->getDepth());                        

        }


        function endChildren() {                 

            $this->hGenericList->endChildren($this->getDepth(),array_shift($this->StackItem));

        }
        function endIteration() {                 

            $this->hGenericList->endIteration();

        }


        function callGetChildren(){

            if (parent::current() instanceof ListItem){

                if (!empty(parent::current()->Childs)){
                    return new RecursiveArrayIterator(parent::current()->Childs);
                } else {
                    return parent::current();
                }

            }
        }        

        function callHasChildren(){

            if (parent::current() instanceof ListItem){
                if (!empty(parent::current()->Childs)){
                    return true;                    
                }
            } else {
                return false;
            }            

        }

        function current(){
            if (parent::current() instanceof ListItem){
                return parent::current();
            } else {
                return NULL;
            }

        }


    }    


    class GenericList extends TBaseWebComponent implements ArrayAccess{

        protected $Tree = array();
        protected $it;

        public $FOnBeginChildren = 'GenericBeginChildren';
        public $FOnEndChildren = 'GenericEndChildren';

        public $FOnBeginIteration = 'CustomBeginIteration';
        public $FOnEndIteration = 'CustomEndIteration';

        public function __construct($aName=null){
            parent::__construct($aName);
        }


        protected function addItem(&$aTreeItem,&$aTree = null) {

            if (is_null($aTree)) {
                if (is_null($aTreeItem->ParentID)) {

                    $this->Tree[$aTreeItem->ID] = $aTreeItem;

                } else {

                    foreach( $this->Tree as $ParentID => &$aCurrentItem) {

                        if (empty($aCurrentItem->Childs)){
                            if ($ParentID == $aTreeItem->ParentID){
                                $aCurrentItem[$aTreeItem->ID] = $aTreeItem;
                                return;
                            }

                        } else {
                            if ($ParentID == $aTreeItem->ParentID){
                                $aCurrentItem[$aTreeItem->ID] = $aTreeItem;
                                return;
                            } else {                        
                                $this->addItem($aTreeItem,$aCurrentItem->Childs);
                            }
                        }
                    }
                    return;

                }

            } else {
                foreach( $aTree as $ParentID => &$aCurrentItem) {

                    if (empty($aCurrentItem->Childs)){
                        if ($ParentID == $aTreeItem->ParentID){
                            $aCurrentItem[$aTreeItem->ID] = $aTreeItem;
                            return;
                        }

                    } else {
                        if ($ParentID == $aTreeItem->ParentID){
                            $aCurrentItem[$aTreeItem->ID] = $aTreeItem;
                            return;
                        } else {                        
                            $this->addItem($aTreeItem,$aCurrentItem->Childs);
                        }
                    }

                }

                return;

            }

        }    

        protected function addTreeSubItem(&$aTree,&$aTreeItem) {

            if (is_object($aTreeItem)){

                if (is_null($aTreeItem->ParentID)) {

                    $aTree[$aTreeItem->ID] = $aTreeItem;

                } else {

                    foreach( $aTree as $Key =>$aCurrentItem) {

                        if ($aTree[$Key]->ID == $aTreeItem->ParentID) {

                            $aTree[$Key]->Childs[$aTreeItem->ID] = $aTreeItem;

                        }   else {

                            $this->addTreeSubItem($aTreeItem,$aTree[$Key]->Childs);
                        }

                    }

                }

            }
        }

        public function offsetExists($offset) {
            return $this->_CollectionTableCell->contains($offset);
        }
        public function offsetUnset($offset) {        
            return $this->_CollectionTableCell->detach($offset);
        }
        public function offsetGet($offset) {
            return ($this->_CollectionTableCell[$offset]);
        }    
        public function offsetSet($offset, $value) {

            if ($value instanceof ListItem){
                if (is_null($offset)) {
                    $value->ParentID = $offset;
                    $this->addItem($value);
                } else {
                    $value->ParentID = $offset;
                    $this->addItem($value);
                }

            }

        }


        public function beginChildren($aDepth) {
            if (!is_null($this->FOnBeginChildren)){                
                $this->DispachEvent($this->FOnBeginChildren,$aDepth);
            }
        }

        public function endChildren($aDepth,$aParenteElement) {

            if (!is_null($this->FOnEndChildren)){          

                $params = array($aDepth,$aParenteElement);
                $this->DispachEvent($this->FOnEndChildren,$params);
            }

        }

        public function beginIteration(){
            if (!is_null($this->FOnBeginIteration)){                
                $this->DispachEvent($this->FOnBeginIteration);
            }
        }

        public function endIteration(){

            if (!is_null($this->FOnEndIteration)){          

                $this->DispachEvent($this->FOnEndIteration);
            }
        }

        public function onShow(){

            $this->it = new ListRecursiveIterator(new RecursiveArrayIterator($this->Tree),RecursiveIteratorIterator::SELF_FIRST);        
            $this->it->setGenericList($this);


            foreach($this->it as $k=>$m) {

                if ($this->it->callHasChildren()){
                    $this->it->addStack($m);
                }

                $m->Show($this->it->getDepth());

            }    

        }

        public function CustomBeginIteration(){
            echo $this->openTag($this->TagName,$this->createTagParams());
        }

        public function CustomEndIteration(){
            echo $this->closeTag($this->TagName);
        }


    }


    class OrderList extends GenericList{

        public function __construct($aName=null){
            parent::__construct($aName);
            $this->TagName = 'ol';
        }

        public function GenericBeginChildren($aDepth){
            echo str_repeat("\t",$aDepth);
            echo $this->openTag($this->TagName,$this->createTagParams());

        }

        public function GenericEndChildren($aDepth,$aParentElement){

            echo str_repeat("\t",$aDepth);
            echo $this->closeTag($this->TagName);

            echo str_repeat("\t",$aDepth);  
            echo $this->closeTag($aParentElement->TagName);

        }


    }

    class UnOrderList extends GenericList{

        public function __construct($aClass=null,$aId=null,$aName=null){
            parent::__construct($aName);
            $this->class = $aClass;
            $this->id = $aId;
            $this->TagName = 'ul';
        }


        public function GenericBeginChildren($aDepth){
            echo str_repeat("\t",$aDepth);
            echo $this->openTag($this->TagName,$this->createTagParams());

        }

        public function GenericEndChildren($aDepth,$aParentElement){

            echo str_repeat("\t",$aDepth);
            echo $this->closeTag($this->TagName);

            echo str_repeat("\t",$aDepth);  
            echo $this->closeTag($aParentElement->TagName);

        }

    }

?>
