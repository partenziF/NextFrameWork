<?php
if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

require_once(PATH_TO_FRAMEWORK_BASECLASS.'GenericCollection.php');

class Caption extends TBaseWebComponentContainer {

    private $Caption;

    function __construct($aCaption,$aName=null,$aId=null,$aClass=null) {
        $this->TagName = 'caption';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        $this->Caption = $aCaption;
    }

    public function setCaption($aCaption){
        $this->Caption = $aCaption;
    }

    public function getCaption(){
        return $this->Caption;
    }

    public function onShow(){

        echo $this->openTag($this->TagName,$this->createTagParams(),$this->Caption);
        parent::onShow();
        echo $this->closeTag($this->TagName);
        

    }

}

class TableRow extends TBaseWebComponent implements ArrayAccess,Iterator {
    public $align;
    public $char;
    public $charoff;
    public $valign;

    private $_CollectionTableCell;

    function __construct(CollectionTableCell $aCollectionTableCell=null,$aId=null,$aClass=null,$aName=null) {
        $this->TagName = 'tr';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        //        $this->Caption = $aCaption;
        if (is_null($aCollectionTableCell)){
            $this->_CollectionTableRow = new CollectionTableRow();
        } else {
            $this->_CollectionTableCell = $aCollectionTableCell;            
        }
    }

    public function onShow(){

        echo $this->openTag($this->TagName,$this->createTagParams());
        if (!is_null($this->_CollectionTableCell)) {
            foreach ($this->_CollectionTableCell as $i=>$cell){
                $cell->Show();
            }
        }
        echo $this->closeTag($this->TagName);

    }

    public function rewind() {
        if (!is_null($this->_CollectionTableCell))
            reset($this->_CollectionTableCell);

    }
    public function current() {
        if (!is_null($this->_CollectionTableCell))
            return current($this->_CollectionTableCell);
    }
    public function key() {
        if (!is_null($this->_CollectionTableCell))
            return key($this->_CollectionTableCell);
    }
    public function next() {
        if (!is_null($this->_CollectionTableCell))
            return next($this->_CollectionTableCell);
    }
    public function valid() {
        //return $this->_CollectionTableRow->valid();
        if (!is_null($this->_CollectionTableCell)){
            $key = key($this->_CollectionTableCell);
            $result = ($key !== NULL && $key !== FALSE);
            return $result;
        } else {
            return false;
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
        if (is_string($value)){
            $this->_CollectionTableCell[$offset] = new TableCell("row{$offset}",$value);
        } else if (($value instanceof TableDataCell) || ($value instanceof TableHeaderCell)) {
                if (is_null($offset)){
                    $this->_CollectionTableCell[] = $value;
                } else {
                    $this->_CollectionTableCell[$offset] = $value;    
                }
            }
    }

}

abstract class TableCell extends TBaseWebComponent implements ArrayAccess {

    public $Text;

    public $abbr;
    public $align;
    public $axis;
    public $char;
    public $charoff;
    public $headers;
    public $colspan;
    public $rowspan;
    public $scope;
    public $valign;

    protected $_CollectionTableCellContent;

    public function offsetExists($offset) {
        return $this->_CollectionTableCellContent->contains($offset);
    }
    public function offsetUnset($offset) {        
        return $this->_CollectionTableCellContent->detach($offset);
    }
    public function offsetGet($offset) {
        return ($this->_CollectionTableCellContent[$offset]);
    }    

    public function offsetSet($offset, $value) {
        if (is_string($value)){
            $this->_CollectionTableCellContent[$offset] = new TableCell("row{$offset}",$value);
        } else if (($value instanceof TBaseWebComponent) || ($value instanceof TextNode)) {
                if (is_null($offset)){
                    $this->_CollectionTableCellContent[] = $value;    
                } else {
                    $this->_CollectionTableCellContent[$offset] = $value;
                }
            }
    }

    protected function createTagParams(){   

        $theParams = array();
        (!empty($this->abbr)) ? $theParams['abbr'] = $this->abbr : null;
        (!empty($this->align)) ? $theParams['align'] = $this->align : null;
        (!empty($this->axis)) ? $theParams['axis'] = $this->axis : null;
        (!empty($this->char)) ? $theParams['char'] = $this->char : null;
        (!empty($this->charoff)) ? $theParams['charoff'] = $this->charoff : null;
        (!empty($this->headers)) ? $theParams['headers'] = $this->headers : null;
        (!empty($this->colspan)) ? $theParams['colspan'] = $this->colspan : null;
        (!empty($this->rowspan)) ? $theParams['rowspan'] = $this->rowspan : null;
        (!empty($this->scope)) ? $theParams['scope'] = $this->scope : null;
        (!empty($this->valign)) ? $theParams['valign'] = $this->valign : null;

        $theBaseParams = parent::createTagParams();     
        return array_merge((array)$theParams,(array)$theBaseParams);

    }


}

class TableDataCell extends TableCell{

    public $toEntities;

    function __construct($aText,$aId=null,$aClass=null,$atoEntities=true,$aName=null) {
        $this->TagName = 'td';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        $this->Text = $aText;
        $this->toEntities = $atoEntities;
    }

    public function onShow(){        
        if (!empty($this->_CollectionTableCellContent)){

            echo $this->openTag($this->TagName,$this->createTagParams(),(is_null($this->Text)?'&nbsp;':$this->Text),$this->toEntities);

            foreach ($this->_CollectionTableCellContent as $content){
                if (($content instanceof TBaseWebComponent) || ($content instanceof TextNode)){
                    $content->show();
                }
            }

            echo $this->closeTag($this->TagName);


        } else {
            echo $this->opencloseTag($this->TagName,$this->createTagParams(),(is_null($this->Text)?'&nbsp;':$this->Text),$this->toEntities);
        }
    }

}

class TableHeaderCell extends TableCell{

    public $toEntities;

    function __construct($aText,$aId=null,$aClass=null,$atoEntities=true,$aName=null) {
        $this->TagName = 'th';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        $this->Text = $aText;
        $this->toEntities = $atoEntities;
    }

    public function onShow(){        
        //echo $this->opencloseTag($this->TagName,$this->createTagParams(),($this->Text),false);
        if (!empty($this->_CollectionTableCellContent)){

            echo $this->openTag($this->TagName,$this->createTagParams(),(is_null($this->Text)?'&nbsp;':$this->Text),$this->toEntities);

            foreach ($this->_CollectionTableCellContent as $content){
                if ($content instanceof TBaseWebComponent){
                    $content->show();
                }
            }

            echo $this->closeTag($this->TagName);


        } else {
            echo $this->opencloseTag($this->TagName,$this->createTagParams(),(is_null($this->Text)?'&nbsp;':$this->Text),$this->toEntities);
        }        
    }

}

class CollectionTableRow extends ClassCollection {

    public function __construct(){
        parent::__construct('TableRow');
    }

}

class CollectionTableCell extends ClassCollection{

    public function __construct(){
        parent::__construct('TableCell');
    }
    /*
    public function offsetSet($offset, $value) {

    if (!is_null($value)) {
    if (get_class($value)==parent::ClassName){
    $this->_Collection->attach($value);
    }
    }

    }
    */
}

class TableParts extends TBaseWebComponent implements ArrayAccess {

    private $_CollectionTableRow;
    public $CurrentRow;


    function __construct($TagName,$aName=null,$aId=null,$aClass=null) {
        $this->TagName = $TagName;
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;
        $this->_CollectionTableRow = new CollectionTableRow();
    }

    public function offsetExists($offset) {
        return $this->_CollectionTableRow->contains($offset);
    }
    public function offsetUnset($offset) {        
        return $this->_CollectionTableRow->detach($offset);
    }
    public function offsetGet($offset) {
        return ($this->_CollectionTableRow[$offset]);
    }    

    public function offsetSet($offset, $value) {
        $this->_CollectionTableRow[$value] = null;
    }

    public function onShow(){

        echo $this->openTag($this->TagName,$this->createTagParams());
        foreach ($this->_CollectionTableRow as $i=>$row){
            $row->Show();
        }
        echo $this->closeTag($this->TagName);

    }

    public function newRow(){

        $this->CurrentRow = new TableRow();
        $this[] = $this->CurrentRow;

    }


    public function addRow(TableRow $aTableRow){

        $this[] = $aTableRow;
        $this->CurrentRow = $aTableRow;

    }

    public function newCell($aRow,$aCol,$aText){
        if (!is_null($this->CurrentRow)){
            if ($this instanceof TableHeader){
                $this->CurrentRow["{$aRow},{$aCol}"]  = new TableHeaderCell($aText);
            } else {
                $this->CurrentRow["{$aRow},{$aCol}"]  = new TableDataCell($aText);
            }
        }
    }    

    public function addCell($aRow,$aCol,TableCell $aCell){
        if (!is_null($this->CurrentRow)){
            $this->CurrentRow["{$aRow},{$aCol}"] = $aCell;
            //$this->_CurrentRow[$aCell];
        }
    }    


}

//class TableHeader extends TBaseWebComponent implements ArrayAccess{
class TableHeader extends TableParts {

    function __construct($aId=null,$aClass=null,$aName=null){
        parent::__construct('thead',$aName,$aId,$aClass);
    }    
    /*
    private $_CollectionTableRow;

    function __construct($aName=null,$aId=null,$aClass=null) {
    $this->TagName = 'thead';
    parent::__construct($aName);
    $this->id = $aId;
    $this->class = $aClass;
    $this->_CollectionTableRow = new CollectionTableRow();
    }


    public function offsetExists($offset) {
    return $this->_CollectionTableRow->contains($offset);
    }
    public function offsetUnset($offset) {        
    return $this->_CollectionTableRow->detach($offset);
    }
    public function offsetGet($offset) {
    return ($this->_CollectionTableRow[$offset]);
    }    

    public function offsetSet($offset, $value) {
    $this->_CollectionTableRow[$value] = null;
    }

    public function onShow(){

    echo $this->openTag($this->TagName,$this->createTagParams());
    foreach ($this->_CollectionTableRow as $i=>$row){
    $row->Show();
    }
    echo $this->closeTag($this->TagName);

    }

    public function newRow(){

    $this->_CurrentRow = new TableRow();
    $this[] = $this->_CurrentRow;

    }

    public function newCell($aRow,$aCol,TableCell $aCell,$aText=null){
    if (!is_null($this->_CurrentRow)){
    if (!is_null($aCell)){
    $this->_CurrentRow["{$aRow},{$aCol}"] = $aCell;
    } else {
    $this->_CurrentRow["{$aRow},{$aCol}"]  = new TableDataCell("cell{$r}{$k}",$aText);
    }

    }
    }
    */

}

//class TableBody extends TBaseWebComponent implements ArrayAccess{
class TableBody extends TableParts{

    function __construct($aId=null,$aClass=null,$aName=null){
        parent::__construct('tbody',$aName,$aId,$aClass);
    }    

    /*
    private $_CollectionTableRow;
    private $_CurrentRow;

    function __construct($aName=null,$aId=null,$aClass=null) {
    $this->TagName = 'tbody';
    parent::__construct($aName);
    $this->id = $aId;
    $this->class = $aClass;
    $this->_CollectionTableRow = new CollectionTableRow();
    }


    public function offsetExists($offset) {
    return $this->_CollectionTableRow->contains($offset);
    }
    public function offsetUnset($offset) {        
    return $this->_CollectionTableRow->detach($offset);
    }
    public function offsetGet($offset) {
    return ($this->_CollectionTableRow[$offset]);
    }    

    public function offsetSet($offset, $value) {
    $this->_CollectionTableRow[$value] = null;
    }

    public function onShow(){

    echo $this->openTag($this->TagName,$this->createTagParams());
    foreach ($this->_CollectionTableRow as $i=>$row){
    $row->Show();
    }
    echo $this->closeTag($this->TagName);

    }

    public function newRow(){

    $this->_CurrentRow = new TableRow();
    $this[] = $this->_CurrentRow;

    }

    public function newCell($aRow,$aCol,TableCell $aCell,$aText=null){
    if (!is_null($this->_CurrentRow)){
    if (!is_null($aCell)){
    $this->_CurrentRow["{$aRow},{$aCol}"] = $aCell;
    } else {
    $this->_CurrentRow["{$aRow},{$aCol}"]  = new TableDataCell("cell{$r}{$k}",$aText);
    }

    }
    }


    }    
    */

}

//class TableFooter extends TBaseWebComponent implements ArrayAccess {
class TableFooter extends TableParts {

    function __construct($aId=null,$aClass=null,$aName=null){
        parent::__construct('tfoot',$aName,$aId,$aClass);
    }    

    /*
    private $_CollectionTableRow;

    function __construct($aName=null,$aId=null,$aClass=null) {
    $this->TagName = 'thead';
    parent::__construct($aName);
    $this->id = $aId;
    $this->class = $aClass;
    $this->_CollectionTableRow = new CollectionTableRow();
    }


    public function offsetExists($offset) {
    return $this->_CollectionTableRow->contains($offset);
    }
    public function offsetUnset($offset) {        
    return $this->_CollectionTableRow->detach($offset);
    }
    public function offsetGet($offset) {
    return ($this->_CollectionTableRow[$offset]);
    }    

    public function offsetSet($offset, $value) {
    $this->_CollectionTableRow[$value] = null;
    }

    public function onShow(){

    echo $this->openTag($this->TagName,$this->createTagParams());
    foreach ($this->_CollectionTableRow as $i=>$row){
    $row->Show();
    }
    echo $this->closeTag($this->TagName);

    }
    */
}



class Table extends TBaseWebComponent implements ArrayAccess{

    public $border;
    public $cellpadding;
    public $cellspacing;
    public $frame;
    public $rules;
    public $summary;
    //public $width;

    public $Caption;
    public $Header;
    public $Body;
    public $Footer;

    function __construct($aId=null,$aClass=null,$aName=null) {
        $a = get_called_class();
        $this->TagName = 'table';
        parent::__construct($aName);
        $this->id = $aId;
        $this->class = $aClass;

    }

    public function setCaption(Caption $aCaption){
        $this->_Caption = $aCaption;
    }

    public function offsetExists($offset) {
        //return $this->_Collection->contains($offset);
    }
    public function offsetUnset($offset) {        
        //return $this->_Collection->detach($offset);
    }
    public function offsetGet($offset) {
        //return ($this->_Collection->contains($offset))?$this->_Collection[$offset] : null;
    }    

    public function offsetSet($offset, $value) {
        if ($value instanceof TableHeader){

        } else if ($value instanceof TableHeader){
            } else if ($value instanceof TableHeader){
                }
    }

    public function onShow(){

        echo $this->openTag($this->TagName,$this->createTagParams());
        if (!is_null($this->Caption)) echo $this->Caption->Show();
        if (!is_null($this->Header)) echo $this->Header->Show();
        if (!is_null($this->Footer)) echo $this->Footer->Show();
        if (!is_null($this->Body)) echo $this->Body->Show();

        //echo $this->->Show();
        echo $this->closeTag($this->TagName);

    }


}
