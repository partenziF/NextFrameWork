<?php

    if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    
    require_once(PATH_TO_FRAMEWORK_BASECLASS.'TBaseClass.php');


    class TBaseComponent extends TBaseClass{

        protected $Bindings = array();

        function __construct($aName) {
            parent::__construct($aName);            
        }

        function DataBinding($aPropertyName,&$aDataSource,$aMemberName) {

            if (!is_null($aDataSource)) {
                $this->Bindings[] = array(    
                'PropertyName'=>$aPropertyName,
                'DataSource'=>&$aDataSource,
                'MemberName'=>$aMemberName);

                if (is_array($aDataSource)) {
                    if (array_key_exists($aMemberName,$aDataSource) ){                        
                        $this->$aPropertyName = &$aDataSource[$aMemberName];                        
                    }
                } else {
                    if (is_object($aDataSource)){
                        if ( (property_exists($this,$aPropertyName)) && (property_exists($aDataSource,$aMemberName)) ) {
                            $this->$aPropertyName = &$aDataSource->$aMemberName;
                        }
                    }
                }
            }
        }

        function doBind() {

            for($i=0;$i<count($this->Bindings);$i++) {

                $PropertyName = $this->Bindings[$i]['PropertyName'];
                $DataSource = $this->Bindings[$i]['DataSource'];
                $MemberName = $this->Bindings[$i]['MemberName'];

                if (is_array($DataSource)) {
                    if (array_key_exists($aMemberName,$DataSource) ){
                        $this->$aPropertyName = $DataSource[$aMemberName];
                    }
                } else if (is_object($DataSource)){
                        if ( (property_exists($this,$PropertyName)) && (property_exists($DataSource,$MemberName)) ) {

                            $this->$PropertyName = $DataSource->$MemberName;
                        }

                }

            }

        }



    }

?>
