<?php

require_once(PATH_TO_FRAMEWORK_BASECLASS.'GenericCollection.php');
require_once(PATH_TO_FRAMEWORK_WEBCOMPONENT.'TBaseWebComponentContainer.php');

class CollectionListViewColumn extends ClassCollection {

    public function __construct(){
        parent::__construct('ListViewColumn','ListViewColumnGroup');
    }

    public function offsetSet($offset, $value) {
        if (($value instanceof ListViewColumn) || ($value instanceof ListViewColumnGroup)){
            return parent::offsetSet($value,$offset);
        }
    }     

    //public function __clone() {
    //$this = clone $this;
    //        $this->Components = clone $this->Components;
    //}

}

class ListViewColumnGroup extends GenericComponentContainer{

    public $Title;
    public $Id;
    public $Class;
    public $toEntities;

    public $Style;

    public $Value;

    public function __construct($aTitle,$aId=null,$aClass=null,$aToEntities=true,TBaseStyleTag $aStyle=null,$aName=null){
        $this->Title = $aTitle;
        $this->Id = $aId;
        $this->Class = $aClass;
        $this->toEntities = $aToEntities;
        parent::__construct($aName);
        $this->Style = $aStyle;
    }

    public function setStyle(TBaseStyleTag $aStyle){
        $this->Style = $aStyle;        
    }


}

//class ListViewColumn extends GenericComponentContainer{
class ListViewColumn extends GenericComponent{

    public $Title;
    public $Id;
    public $Class;
    public $toEntities;

    public $Style;

    public $Text;

    public $Components;


    public function __construct($aTitle,$aId=null,$aClass=null,$aToEntities=true,TBaseStyleTag $aStyle=null,$aName=null){
        parent::__construct($aName);
        $this->Title = $aTitle;
        $this->Id = $aId;
        $this->Class = $aClass;
        $this->toEntities = $aToEntities;
        $this->Style = $aStyle;
        $this->Components = new GenericComponentContainer(null);
    }

    public function setStyle(TBaseStyleTag $aStyle){
        $this->Style = $aStyle;        
    }

    public function addComponent($aComponent){
        if ($aComponent instanceof GenericComponent){
            $this->Components[] = $aComponent;
        }
    }

    public function __clone() {
        $this->Components = clone $this->Components;
    }

}


class CollectionListViewItems extends ClassCollection {

    public function __construct(){
        parent::__construct('ListViewItem');
    }

}


class ListViewItem extends GenericComponent implements ArrayAccess,IteratorAggregate  {

    public $Text;    
    private $_SubItems;

    public $Id;
    public $Class;
    public $toEntities;

    public $Style;


    public function __construct($aText, array $SubItems=array(),$aId=null,$aClass=null,$aToEntities=true,TBaseStyleTag $aStyle=null,$aName=null){
        $this->Text = $aText;  
        parent::__construct($aName);
        if (!empty($SubItems)){

            reset($SubItems);
            while (list($i,$Subitem) = each($SubItems)){
                $this[] = $Subitem;
            }

        }
        $this->Id = $aId;
        $this->Class = $aClass;
        $this->toEntities = $aToEntities;
        $this->Style = $aStyle;

    }

    public function setStyle(TBaseStyleTag $aStyle){
        $this->Style = $aStyle;        
    }


    public function offsetExists($offset) {
        return $this->_SubItems->offsetExists($offset);
    }
    public function offsetUnset($offset) {        
        return $this->_SubItems->offsetUnset($offset);
        //return $this->_Collection->detach($offset);
    }
    public function offsetGet($offset) {
        return $this->_SubItems->offsetGet($offset);
        //return ($this->_Collection->contains($offset))?$this->_Collection[$offset] : null;
    }    
    public function offsetSet($offset, $value) {
        if ($value instanceof ListViewSubItem){
            if (is_null($offset)){
                return $this->_SubItems[] = $value;
            } else {
                return $this->_SubItems[$offset] = $value;
            }
        } else if (is_string($value)){

                if (is_null($offset)){
                    return $this->_SubItems[] = new ListViewSubItem($value);
                } else {
                    return $this->_SubItems[$offset] = new ListViewSubItem($value);
                }
            }

    }
    public function getIterator() {
        return new ArrayIterator($this->_SubItems);
    }

    public function addComponent($aComponent){
        if ($aComponent instanceof GenericComponent){
            $this[] = $aComponent;
        }
    }


} 

class CollectionListViewSubItems extends ClassCollection {

    public function __construct(){
        parent::__construct('ListViewSubItem');
    }

}

class ListViewSubItem extends GenericComponentContainer {


    private $_SubItemsCollection;

    public $Text;
    public $Id;
    public $Class;
    public $toEntities;

    public $Style;

    public function __construct($aText,$aId=null,$aClass=null,$aToEntities=true,TBaseStyleTag $aStyle=null,$aName=null){

        $this->Text = $aText;
        parent::__construct($aName);
        $this->Id = $aId;
        $this->Class = $aClass;
        $this->toEntities = $aToEntities;
        $this->Style = $aStyle;

    }

    public function setStyle(TBaseStyleTag $aStyle){
        $this->Style = $aStyle;        
    }

    public function addComponent($aComponent){
        if ($aComponent instanceof GenericComponent){
            $this[] = $aComponent;
        }
    }

} 


class ListView extends GenericComponent implements ArrayAccess {

    private $_caption;
    private $_table;
    public $Columns;  
    private $_items;
    private $ResultPrepareData;

    public $FOnPrepareData;
    public $FOnData;
    public $FOnCellData;
    public $FOnNewRow;
    public $FOnEndData;

    public $Id;
    public $Class;

    private $_currentRow = 0;

    public function __construct($aId=null,$aClass=null,TBaseStyleTag $aStyle=null,$aName=null){
        $this->_table = new Table();
        $this->Columns = new CollectionListViewColumn();
        $this->_items = new CollectionListViewItems();

        $this->Id = $aId;
        $this->Class = $aClass;

        unset($this->FOnShow);
        parent::__construct($aName);
    }

    public function setCaption($aCaption,TBaseStyleTag $aStyle=null){
        $this->_caption = $aCaption;
    }
    
    public function offsetExists($offset) {
        return $this->_items->offsetExists($offset);
    }
    public function offsetUnset($offset) {        
        return $this->_items->offsetUnset($offset);
        //return $this->_Collection->detach($offset);
    }
    public function offsetGet($offset) {
        return $this->_items->offsetGet($offset);
        //return ($this->_Collection->contains($offset))?$this->_Collection[$offset] : null;
    }    
    public function offsetSet($offset, $value) {
        if ($value instanceof ListViewItem){
            return $this->_items[$value] = $offset;
        }
    }

    function Show(){

        if ($this->isVisible) {

            //$r = $this->PrepareData();
            $this->_table = new Table($this->Id,$this->Class);

            if (($this->Columns->count())>0) {

                $this->_table->Header = new TableHeader();
                $this->_table->Header->newRow();
                foreach ($this->Columns as $i=>$Column){
                    if ($Column instanceof ListViewColumnGroup){
                        $HeadCell = new TableHeaderCell($Column->Title);
                        $HeadCell->colspan = ($Column->count());
                        if (!is_null($Column->Style)) {$Column->Style->copyStyle($HeadCell);}
                        $this->_table->Header->addCell(0,$i,$HeadCell);

                    } else {
                        $HeadCell = new TableHeaderCell($Column->Title);
                        if (!is_null($Column->Style)) {$Column->Style->copyStyle($HeadCell);}
                        $this->_table->Header->addCell(0,$i,$HeadCell);
                    }
                }

            }

            if ($this->ResultPrepareData===true){

                if (isset($this->_caption)){
                    $this->_table->Caption = new Caption($this->_caption);
                }

                $this->_table->Body = new TableBody();

                $Item = $this->Data();
                $indexRow = 0;

                do{
                    $tblRow = new TableRow();
                    $this->newRow($tblRow,$indexRow);
                    $this->_table->Body->addRow($tblRow);
                    $indexColumn = 0;

                    if ($Item instanceof ListViewItem){

                        $i = 0;
                        $this->CellData(0,$Item);
                        $this->_table->Body->addCell($this->_currentRow,$i,new TableDataCell($Item->Text));
                        $i++;

                        foreach ($Item as $k=>$subItem){
                            $this->CellData($k+1,$subItem);
                            $this->_table->Body->addCell($this->_currentRow,$k+$i,new TableDataCell($subItem->Text));
                        }

                    } else if (is_object($Item)){

                            foreach ($this->Columns as $i=>$theColumn){

                                $Column = clone $theColumn;

                                if ($Column instanceof ListViewColumnGroup){

                                    foreach ($Column as $k=>$theSubColumn){

                                        $SubColumn = clone $theSubColumn;

                                        $Column->doBind();

                                        if ($SubColumn instanceof ListViewColumn){

                                            $SubColumn->doBind();
                                            $this->CellData($indexColumn,$SubColumn);
                                            $HeadCell = new TableHeaderCell($SubColumn->Text);
                                            if (!is_null($SubColumn->Style)) {$SubColumn->Style->copyStyle($HeadCell);}
                                            if ($SubColumn->Components->count()>0){
                                            foreach ($SubColumn->Components as $component)
                                                $HeadCell[] = $component;

                                        }

                                        $this->_table->Body->addCell($this->_currentRow,$indexColumn++,$HeadCell);

                                    }

                                }

                            } else if ($Column instanceof ListViewColumn){

                                    $Column->doBind();

                                    $this->CellData($indexColumn,$Column);
                                    $DataCell = new TableDataCell($Column->Text);
                                    if (!is_null($Column->Style)) {$Column->Style->copyStyle($DataCell);}
                                    if ($Column->Components->count()>0){
                                    foreach ($Column->Components as $component)
                                        $DataCell[] = $component;
                                }                                    
                                $this->_table->Body->addCell($this->_currentRow,$indexColumn++,$DataCell);

                            }


                            //$Column->Components = new GenericComponentContainer(null);
                        }

                    }

                    $this->EndData($tblRow,$indexRow);

                    $indexRow +=1; //Alla fine perchè è 0-based

                } while ($Item = $this->Data());

            }

            $this->OnShow();
        }
    }

    private function PrepareData(){

        $this->_currentRow = 0;

        if (isset($this->FOnPrepareData)){
            $r = $this->DispachEvent($this->FOnPrepareData);
            return $r;
        } else {
            if ($this->_items->count()>0){
                $this->_items->rewind();
                return true;
            } else {
                return false;
            }
        }
    }

    private function Data(){
        $this->_currentRow++;
        if (isset($this->FOnData)){
            $r = $this->DispachEvent($this->FOnData);
            return $r;
        } else {
            $c = $this->_items->current();
            $this->_items->next();
            return $c;
        }
    }

    private function newRow($tblRow,$indexRow){

        if (isset($this->FOnNewRow)){
            $params = array(&$tblRow,$indexRow);
            $this->DispachEvent($this->FOnNewRow,$params);
        }

    }

    private function EndData($tblRow,$indexRow){

        if (isset($this->FOnEndData)){
            $params = array(&$tblRow,$indexRow);
            $this->DispachEvent($this->FOnEndData,$params);
        }

    }



    private function CellData($i,$Item){

        if (isset($this->FOnCellData)){
            $params = array($i,&$Item);
            $this->DispachEvent($this->FOnCellData,$params);
        }

    }


    private function OnShow(){

        if (isset($this->FOnShow)) {
            $this->DispachEvent($this->FOnShow);
        } else {
            $this->_table->Show();
        }
    }

    public function Process(){
        $this->ResultPrepareData = $this->PrepareData();
    }
}


/*    
$lv = new ListView();
$lv->setCaption('Tabella di prova');

$lv->Columns[] = new ListViewColumn('Titolo1');
$lv->Columns[] = new ListViewColumn('Titolo2');
$lv->Columns[] = new ListViewColumn('Titolo3');

$lvi = new ListViewItem('colonna 1 1',array(new ListViewSubItem('colonna 1 2'),'colonna 1 3'));
$lv[] = $lvi; 

$lvi = new ListViewItem('colonna 2 1');
$lvi[] = new ListViewSubItem('colonna 2 2');
$lvi[] = 'colonna 2 3';
$lv[] = $lvi; 

$lvi = new ListViewItem('colonna 3 1');
$lvi[] = new ListViewSubItem('colonna 3 2');
$lvi[] = 'colonna 3 3';
$lv[] = $lvi; 


$lv->Show();


$db = dbFactory::Create();
$db->connect(DbHost,DbUser,DbPassword,DbName);


function prepare($self){
global $db;
global $ds;
global $MainTable;

$db->setQuey('SELECT * FROM StruttureRicettive LIMIT 0,1');
$ds = new MySqlDataSet($db);
$ds->setDataLayer(ddlStruttureRicettive::getClassname());
$MainTable = $ds->rewind();

return ($MainTable!==false);

}


function onData(){
global $ds;
global $MainTable;

$MainTable = $ds->current();
$ds->next();
return $MainTable;

}

function onCellData($self,$i,$item){
global $MainTable;
if ($item instanceof ListViewColumn){
switch ($i){
case 0:
global $edLink;
$edLink->setUrl('test9.php',array('id'=>$MainTable->P_StrutturaRicettiva));
break;
case 1:
case 2:
$item->Text = 'asdas';              
break;
}
}
}

$MainTable = new ddlStruttureRicettive();

$lv = new ListView();
$lv->setCaption('Tabella di prova');
$GroupColumn = new ListViewColumnGroup('Azioni');
$c = new ListViewColumn('edit1');
$edLink = new Link('Edit','#');
$c[] = $edLink;
$GroupColumn[] = $c;
$GroupColumn[] = new ListViewColumn('edit2');
$GroupColumn[] = new ListViewColumn('edit3');

$lv->Columns[] = $GroupColumn;

$Column = new ListViewColumn('Ragione Sociale');
$Column->DataBinding('Text',$MainTable,'RagioneSociale');
$lv->Columns[] = $Column;
$Column = new ListViewColumn('Città');
$Column->DataBinding('Text',$MainTable,'Citta');
$lv->Columns[] = $Column;
$Column = new ListViewColumn('Indirizzo');
$Column->DataBinding('Text',$MainTable,'Indirizzo');
$lv->Columns[] = $Column;

$lv->FOnPrepareData = 'prepare';
$lv->FOnData = 'onData';
$lv->FOnCellData = 'onCellData';


*/