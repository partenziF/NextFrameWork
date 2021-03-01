<?php

    if ( !function_exists( 'property_exists' ) ) {
        function property_exists( $class, $property ) {
            if ( is_object( $class ) ) {
                $vars = get_object_vars( $class );
            } else {
                $vars = get_class_vars( $class );
            }
            return array_key_exists( $property, $vars );
        }
    } 


    class TClassFactory{
        
        public static $_classCounter = array();

        public static function getName(){
            $stack = debug_backtrace();
            $firstCall = end($stack);
            $classCalled = $firstCall['class'];
            
            if (isset(self::$_classCounter[$classCalled])) {            
                static::$_classCounter[$classCalled] ++;
            } else {
                static::$_classCounter[$classCalled] = 1;
            }
            return $classCalled.static::$_classCounter[$classCalled];
            
        }
        
        public static function getByName(){
            
        }

    }

    class TBaseClass {

        protected $Name;

        function __construct($aName) {
            if (is_null($aName)){
                $this->Name = TClassFactory::getName();
            } else {
                $this->Name = $aName;
            }
        }

        function __destruct() {
        }

        function DispachEvent($aEventName,&$aParams = null,$ParamsAsArray=false){

            //is_callable

            if (is_array($aParams)) {

                if ($ParamsAsArray === true) {

                    if (method_exists($this, $aEventName)) { 
                        $result = call_user_func(array($this, $aEventName),$aParams); }
                    else if (function_exists($aEventName)) { 

                            $result = call_user_func($aEventName,$this,$aParams); 
                        }

                } else {

                    if (method_exists($this, $aEventName)) { 
                        $result = call_user_func_array(array($this, $aEventName),&$aParams); 
                    } else if (function_exists($aEventName)) { 
                            if (empty($aParams)) {
                                $result = call_user_func_array($aEventName,array($this,$aParams)); 
                            } else {
                                $result = call_user_func_array($aEventName,array_merge(array($this),$aParams)); 
                            }
                        }

                }


            } else {

                if (method_exists($this, $aEventName)) { 

                    $result = call_user_func(array($this, $aEventName),&$aParams); 

                } else if (function_exists($aEventName)) { 

                        $result = call_user_func($aEventName,$this,&$aParams); 
                    }

            }

            return $result;

        }      

        function getName(){
            return $this->Name;
        }

    }
?>
