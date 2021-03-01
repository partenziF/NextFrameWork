<?php
class GenericLogin implements ArrayAccess {

    var $Username = NULL;
    var $Password = NULL;
    var $IpAddress = NULL;

    var $isLogin = false;
    var $ResultCode = 0;

    var $SessionName = null;

    
    public function offsetSet($offset, $value) {
        if ((!is_null($offset)) and (gettype($offset)=='string') ) {
            $_SESSION[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($_SESSION[$offset]);
    }
    public function offsetUnset($offset) {
        unset($_SESSION[$offset]);
    }
    public function offsetGet($offset) {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }    

    public function __construct($db=null,$aSessionName=null){

        $this->SessionName = $aSessionName;

        if (!is_null($this->SessionName)) {

            session_name($this->SessionName);

        }

        session_start();

    }

    public function UserDoLogin(){
        
    }

    public function doLogin($ParamNameUser,$ParamNamePassword){

        $this->Username = TGenericRequest::getRequestValue($ParamNameUser,'string',NULL);
        $this->Password = TGenericRequest::getRequestValue($ParamNamePassword,'string',NULL);
        if ($this->Username == "" ){unset($this->Username);}
        if ($this->Password == "" ){unset($this->Password);}

        if ($_SERVER['REQUEST_METHOD']!='POST') {

            $this->ResultCode = E_LOGIN_METHOD_NOT_PERMITTED;
            unset($this->Username);
            unset($this->Password);
            $this->isLogin = false;

        } else {

            if ( (isset($this->Username)) && (isset($this->Password)) ) {

                if ($this->UserDoLogin()){

                    $this->isLogin = true;
                    $this->ResultCode = E_LOGIN_SUCCESS;
                    $this->OnLoginSuccess();
                } else {
                    $this->isLogin = false;
                    if ($this->ResultCode===0) $this->ResultCode = E_LOGIN_INCORRECT;
                    $this->Logout();
                }

            } else {
                $this->isLogin = false;
                $this->ResultCode = E_LOGIN_INCORRECT;
                $this->Logout();
            }

        }

        return $this->isLogin;

    }


    function CheckLogin(){

        if (!($this->isLogin)){ $this->OnLoginError(); }

    }

    function OnLoginSuccess(){

    }

    function Logout(){

        @session_unset();
        @session_destroy();

    }

    function OnLoginError(){

        $this->Logout();
    }
    
    
}