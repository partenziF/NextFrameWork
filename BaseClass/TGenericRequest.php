<?php
    /*

    TGenericRequest::constructor();
    $valore_paramstringa = TGenericRequest::getRequestValue('paramstringa','string',false,'default');
    $valore_paramint = TGenericRequest::getRequestValue('paramint','integer',false,10);
    $valore_paramacheck = TGenericRequest::getRequestValue('paramacheck','bool',false,false);
    $valore_parametrodata = TGenericRequest::getRequestValue('Data','date',false,strtotime("now"));
    $valore_paramstringadata = TGenericRequest::getRequestValue('paramstringadata','date');

    $valore_multivalore = TGenericRequest::getRequestValue('multivalue','int');

    */



    class TGenericRequest {

        private static $issetMagicQuote = MAGIC_QUOTE;
        protected static $toEntity = false;
        protected static $stripTags = true;
        protected static $isUTF8 = false;
        public static $SetSeparator = "\x1D";//TODO Cambiare il valore di SetSeparator e vedere dove viene usato

        protected static $Date_Day_Key = 'D';
        protected static $Date_Month_Key = 'M';
        protected static $Date_Year_Key = 'Y';
        //protected static $Date_Separator = '-';forse da usare per fare il check della data se stringa ma forse meglio usare date format?

        //protected static $DateTime_Hour_Key = 'h';
        //protected static $DateTime_Minute_Key = 'm';
        //protected static $DateTime_Second_Key = 's';
        //protected static $DateTime_Separator = ':';?

        public static function getDayKey(){ return self::$Date_Day_Key; }
        public static function getMonthKey(){ return self::$Date_Month_Key; }
        public static function getYearKey(){ return self::$Date_Year_Key; }

        public static function constructor($atoEntity=false,$astripTags=false,$aisUTF8=false) {

            self::$SetSeparator = "\x1D";
            self::$issetMagicQuote = MAGIC_QUOTE;
            self::$toEntity = $atoEntity;
            self::$stripTags = $astripTags;
            self::$isUTF8 = $aisUTF8;

        }

        private static function asInteger($theValue,$NullValueAllowed,$DefaultValue){

            if ($theValue === '') {

                if ($NullValueAllowed) {
                    $theValue = null;
                } else {
                    $theValue = $DefaultValue;
                    settype($theValue,'integer');
                }

            } else {
                if (strpos($theValue,self::$SetSeparator)!==false){
                    $theValue = explode(self::$SetSeparator,$theValue);
                    array_walk_recursive($theValue,'TGenericRequest::arrayAsInteger',array($NullValueAllowed,$DefaultValue));
                } else {
                    settype($theValue,'integer');
                }
            }

            return $theValue;
        }

        private static function asString($theValue,$NullValueAllowed,$DefaultValue) {

            if (self::$stripTags) $theValue = strip_tags($theValue);
            if (empty($theValue)) $theValue = $DefaultValue;

            if (($NullValueAllowed) and (empty($theValue))) {
                if (!is_null($theValue)){
                    settype($theValue,'string');
                }
            } else {
                if (strpos($theValue,self::$SetSeparator)!==false){
                    $theValue = explode(self::$SetSeparator,$theValue);
                    array_walk_recursive($theValue,'TGenericRequest::arrayAsString',array($NullValueAllowed,$DefaultValue));
                } else {
                    settype($theValue,'string');
                }
            }

            return $theValue;
        }

        private static function asFloat($theValue,$NullValueAllowed,$DefaultValue) {
            if ($theValue === '') {

                if ($NullValueAllowed) {
                    $theValue = null;
                } else {
                    $theValue = str_replace(',','',$DefaultValue);
                    settype($theValue,'float');
                }

            } else {
                if (strpos($theValue,self::$SetSeparator)!==false){
                    $theValue = explode(self::$SetSeparator,$theValue);
                    array_walk_recursive($theValue,'TGenericRequest::arrayAsFloat',array($NullValueAllowed,$DefaultValue));
                } else {
                    $theValue = str_replace(',','',$theValue);
                    settype($theValue,'float');
                }
            }
            return $theValue;
        }

        private static function asBoolean($theValue,$NullValueAllowed,$DefaultValue) {

            if (empty($theValue)) $theValue = $DefaultValue;
            if (($NullValueAllowed)) {

                if (empty($theValue)) $theValue = null;
                else settype( $theValue, 'boolean' );

            } else {

                if (empty($theValue)) $theValue = false;
                else settype( $theValue, 'boolean' ); 

            }

            return $theValue;

        }

        private static function asDate($theValue,$NullValueAllowed,$DefaultValue){

            if (is_array($theValue)){

                if (
                ((array_key_exists(self::$Date_Day_Key,$theValue)) and 
                (array_key_exists(self::$Date_Month_Key,$theValue)) and 
                (array_key_exists(self::$Date_Year_Key,$theValue))) and 
                (count(array_keys($theValue))==3)) {

                    $theDayValue = $theValue[self::$Date_Day_Key];
                    $theMonthValue = $theValue[self::$Date_Month_Key];
                    $theYearValue = $theValue[self::$Date_Year_Key];
                    if (!(empty($theDayValue)) and (!empty($theMonthValue)) and (!empty($theYearValue))){
                        if (checkdate($theMonthValue,$theDayValue,$theYearValue)) {
                            $theValue = mktime(0,0,0,$theMonthValue,$theDayValue,$theYearValue);
                        } else {
                            if (checkdate($theMonthValue,1,$theYearValue)) {

                                $theValue = mktime(0,0,0,$theMonthValue,date('t',mktime(0,0,0,$theMonthValue,1,$theYearValue)),$theYearValue);

                            } else {

                                if (is_numeric($DefaultValue)) {
                                    $theValue = $DefaultValue;
                                } else if (gettype($DefaultValue)==='string') {
                                        $theValue = strtotime($DefaultValue);
                                        if ($theValue===false){
                                            if ($NullValueAllowed) $theValue = null;
                                            else $theValue = 0;
                                        }

                                } else {
                                    if ($NullValueAllowed) $theValue = null;
                                    else $theValue = 0;
                                }

                            }

                        }

                    } else {

                        if (is_numeric($DefaultValue)) {
                            $theValue = $DefaultValue;
                        } else if (gettype($DefaultValue)==='string') {
                                $theValue = strtotime($DefaultValue);
                                if ($theValue===false){
                                    if ($NullValueAllowed) $theValue = null;
                                    else $theValue = 0;
                                }

                        } else {
                            if ($NullValueAllowed) $theValue = null;
                            else $theValue = 0;
                        }

                    }

                    if (!is_null($theValue)) settype($theValue,'integer');

                } else {

                    if (is_numeric($DefaultValue)) {
                        $theValue = $DefaultValue;
                    } else if (gettype($DefaultValue)==='string') {
                            $theValue = strtotime($DefaultValue);
                            if ($theValue===false){
                                if ($NullValueAllowed) $theValue = null;
                                else $theValue = 0;
                            }

                    } else {
                        if ($NullValueAllowed) $theValue = null;
                        else $theValue = 0;
                    }

                    if (!is_null($theValue)) settype($theValue,'integer');                        
                } 


            } else {

                if (empty($theValue)) {

                    if ($NullValueAllowed) {
                        $theValue = null;
                    } else {
                        if (is_numeric($DefaultValue)) {                                        
                            $theValue = $DefaultValue;
                        } else if (gettype($DefaultValue)==='string') {
                                $theValue = strtotime($DefaultValue);
                                if ($theValue===false){
                                    if ($NullValueAllowed) $theValue = null;
                                    else $theValue = 0;
                                }
                        } else {
                            if ($NullValueAllowed) $theValue = null;
                            else $theValue = 0;
                        }
                    }

                } else {
                    if (is_numeric($theValue)) {                                        
                        $theValue = $theValue;                                        
                    } else if (gettype($theValue)==='string') {
                            $theValue = strtotime($theValue);
                            if ($theValue===false){
                                if ($NullValueAllowed) $theValue = null;
                                else {
                                    if (is_numeric($DefaultValue)) {                                        
                                        $theValue = $DefaultValue;
                                    } else if (gettype($DefaultValue)==='string') {
                                            $theValue = strtotime($DefaultValue);
                                            if ($theValue===false){                                                            
                                                $theValue = 0;
                                            }
                                    } else {                                                    
                                        $theValue = 0;
                                    }
                            }
                        }
                    } else {
                        if ($NullValueAllowed) $theValue = null;
                        else {
                            if (is_numeric($DefaultValue)) {                                        
                                $theValue = $DefaultValue;
                            } else if (gettype($DefaultValue)==='string') {
                                    $theValue = strtotime($DefaultValue);
                                    if ($theValue===false){                                                            
                                        $theValue = 0;
                                    }
                            } else {                                                    
                                $theValue = 0;
                            }
                        }
                    }
                }

                if (!is_null($theValue)) settype($theValue,'integer');

            }

            return $theValue;

        }


        private static function arrayAsInteger(&$value,$key,$UserParams){

            $value = self::asInteger($value,$UserParams[0],$UserParams[1]);
        }

        private static function arrayAsString(&$value,$key,$UserParams){

            $value = self::asString($value,$UserParams[0],$UserParams[1]);
        }

        private static function arrayAsFloat(&$value,$key,$UserParams){

            $value = self::asFloat($value,$UserParams[0],$UserParams[1]);
        }

        private static function arrayAsBoolean(&$value,$key,$UserParams){

            $value = self::asBoolean($value,$UserParams[0],$UserParams[1]);
        }

        private static function arrayAsDate(&$value,$key,$UserParams){

            if (
            ((array_key_exists(self::$Date_Day_Key,$value)) and 
            (array_key_exists(self::$Date_Month_Key,$value)) and 
            (array_key_exists(self::$Date_Year_Key,$value))) and 
            (count(array_keys($value))==3)) {
                $value = self::asDate($value,$UserParams[0],$UserParams[1]);
            } else {
                if (is_array($value)){
                    array_walk($value,'TGenericRequest::arrayAsDate',array($UserParams[0],$UserParams[1]));
                } else {
                    $value = self::asDate($value,$UserParams[0],$UserParams[1]);
                }
            }

        }

        public static function existParam($aParamName) {
            return (array_key_exists($aParamName,$_REQUEST) OR array_key_exists($aParamName,$_FILES));
        }


        public static function getRequestValue($aParamName,$aParamDataType='string',$NullValueAllowed=true,$DefaultValue=null){

            if (empty($aParamDataType)) $ParamDataType = 'string';
            else 
                if ( 
                ($aParamDataType!='bitmask') and 
                ($aParamDataType!='boolean') and ($aParamDataType!='bool') and 
                ($aParamDataType!='integer') and ($aParamDataType!='int') and 
                ($aParamDataType!='date') and ($aParamDataType!='datetime') and 
                ($aParamDataType!='float') and ($aParamDataType!='double') and ($aParamDataType!='real') and 
                ($aParamDataType!='string') and ($aParamDataType!='html')
                ){
                    $ParamDataType = 'string';
                } else {
                    $ParamDataType = $aParamDataType;
            }

            if (array_key_exists($aParamName,$_REQUEST)) {

                $theValue = $_REQUEST[$aParamName];

                if (is_array($theValue)){

                    //if (self::$issetMagicQuote) array_walk($theValue,create_function('&$value,$key','$value = stripslashes($value); '));
                    //if (true) array_walk($theValue,create_function('&$value,$key','$value = stripslashes($value); '));
                    //if (true) array_walk($theValue,create_function('&$value,$key','if (is_array($value)){ foreach ($value as $k=>$v) $value[$k] = stripslashes($v);} else {$value = stripslashes($value);}'));
                    if (self::$issetMagicQuote) array_walk_recursive($theValue,create_function('&$value,$key','$value = stripslashes($value);'));
                    if (self::$toEntity) array_walk_recursive($theValue,create_function('&$value,$key','$value = htmlentities($value);'));

                    switch ($ParamDataType) {

                        case 'string':                            
                            array_walk_recursive($theValue,'TGenericRequest::arrayAsString',array($NullValueAllowed,$DefaultValue));
                            break;

                        case 'integer':
                        case 'int':
                            array_walk_recursive($theValue,'TGenericRequest::arrayAsInteger',array($NullValueAllowed,$DefaultValue));
                            break;

                        case 'bitmask':
                            array_walk_recursive($theValue,'TGenericRequest::arrayAsInteger',array($NullValueAllowed,$DefaultValue));
                            $r = 0;
                            foreach ($theValue as $k=>$v){
                                $r = $r | $v;
                            }
                            $theValue = $r;
                            break;

                        case 'float':
                        case 'double':
                        case 'real':
                            array_walk_recursive($theValue,'TGenericRequest::arrayAsFloat',array($NullValueAllowed,$DefaultValue));
                            break;

                        case 'bool':
                        case 'boolean':
                            array_walk_recursive($theValue,'TGenericRequest::arrayAsBoolean',array($NullValueAllowed,$DefaultValue));
                            break;

                        case 'date':

                            if (
                            ((array_key_exists(self::$Date_Day_Key,$theValue)) and 
                            (array_key_exists(self::$Date_Month_Key,$theValue)) and 
                            (array_key_exists(self::$Date_Year_Key,$theValue))) and 
                            (count(array_keys($theValue))==3)) {
                                return self::asDate($theValue,$NullValueAllowed,$DefaultValue);
                            } else {
                                array_walk($theValue,'TGenericRequest::arrayAsDate',array($NullValueAllowed,$DefaultValue));
                            }
                            break;

                        case 'datetime':
                            break;
                    }

                } else {

                    if (self::$issetMagicQuote) $theValue = stripslashes($theValue);
                    if (self::$toEntity) $theValue = htmlentities($theValue);

                    switch ($ParamDataType) {

                        case 'html':
                            if (empty($theValue)) $theValue = $DefaultValue;

                            if (($NullValueAllowed) and (empty($theValue))) {
                                if (!is_null($theValue)){
                                    settype($theValue,$ParamDataType);
                                }
                            } else {
                                settype($theValue,$ParamDataType);
                            }
                            break;

                        case 'string':

                            $theValue = self::asString($theValue,$NullValueAllowed,$DefaultValue);
                            break;

                        case 'integer':
                        case 'int':                            
                        case 'bitmask':                            
                            $theValue = self::asInteger($theValue,$NullValueAllowed,$DefaultValue);
                            break;

                        case 'float':
                        case 'double':
                        case 'real':
                            $theValue = self::asFloat($theValue,$NullValueAllowed,$DefaultValue);
                            break;

                        case 'bool':
                        case 'boolean':
                            $theValue = self::asBoolean($theValue,$NullValueAllowed,$DefaultValue);
                            break;                            

                        case 'date':
                            $theValue = self::asDate($theValue,$NullValueAllowed,$DefaultValue);
                            break;

                    }



                }


            } else {

                if (($ParamDataType == 'bool' ) or ($ParamDataType == 'boolean' )) {

                    if ($NullValueAllowed) {

                        if ( (gettype($DefaultValue) == 'bool' ) or (gettype($DefaultValue) == 'boolean' ) ) {
                            $theValue = $DefaultValue;
                            settype($theValue,$ParamDataType);
                        } else {
                            $theValue = null;
                        }

                    } else {

                        if (!is_null($DefaultValue)) {

                            if ( (gettype($DefaultValue) == 'bool' ) or (gettype($DefaultValue) == 'boolean' ) ) {
                                $theValue = $DefaultValue;
                                settype($theValue,$ParamDataType);
                            } else {
                                $theValue = false;
                            }

                        } else {
                            $theValue = false;
                        }
                        settype($theValue,$ParamDataType);
                    }

                } else {

                    if ($NullValueAllowed) {

                        if (gettype($DefaultValue) == $ParamDataType) {
                            $theValue = $DefaultValue;
                            settype($theValue,$ParamDataType);
                        } else {
                            $theValue = null;
                        }

                    } else {

                        if (!is_null($DefaultValue)) {
                            if (gettype($DefaultValue) == $ParamDataType) {
                                $theValue = $DefaultValue;                                
                            }                            
                        }

                        settype($theValue,$ParamDataType);

                    }

                }


            }

            return $theValue;

        }


        public static function getFileValue($aParamName,$FileNameFormat,$DocumentRoot,$DirectoryUpload,$ExtensionAllowed,&$ErrorCode,&$ResultMessage) {

            if (!(empty($_FILES[$aParamName]['name']))) {

                if (ini_get('file_uploads') != "off") {

                    if ($_FILES[$aParamName]['error'] ==  UPLOAD_ERR_OK  ) {

                        $DirectoryUpload = trim($DirectoryUpload,DIRECTORY_SEPARATOR);
                        if( substr($DocumentRoot,-1) != DIRECTORY_SEPARATOR ) $DocumentRoot .= DIRECTORY_SEPARATOR; // add trailing slash

                        $FullPathDirectoryUpload = $DocumentRoot.$DirectoryUpload.DIRECTORY_SEPARATOR;

                        if (!(is_dir($FullPathDirectoryUpload))) { 

                            if (!file_exists($FullPathDirectoryUpload)){

                                if (!(@mkdir($FullPathDirectoryUpload,0777,true))) {

                                    $PermissionDenied = true;
                                }

                            }

                        }

                        #$PermissionDenied = true;
                        if (!($PermissionDenied)) {           

                            $upload_max_filesize = ini_get('upload_max_filesize');
                            $upload_max_filesize = preg_replace('/M/', '000000', $upload_max_filesize);


                            if ($_FILES[$aParamName]['size'] <= $upload_max_filesize) {

                                $isAllowedFileExtension = true;
                                if (!empty($ExtensionAllowed)) {

                                    $FileExtension = pathinfo($_FILES[$aParamName]['name'],PATHINFO_EXTENSION);

                                    if (in_array(strtoupper($FileExtension),$ExtensionAllowed)){
                                        $isAllowedFileExtension = true;
                                    } else {
                                        $isAllowedFileExtension = false;
                                    }

                                }                            

                                if ($isAllowedFileExtension == true) {



                                    if (true) {

                                        $Extension = pathinfo($_FILES[$aParamName]['name'],PATHINFO_EXTENSION);
                                        $HashMD5 = md5_file($_FILES[$aParamName]['tmp_name']);
                                        $FilenameUploaded = $HashMD5.'.'.$Extension;

                                        $FullPathUploadedFileName = $FullPathDirectoryUpload.$FilenameUploaded;

                                    } else {

                                        $FilenameUploaded = $_FILES[$aParamName]['name'];
                                        $FullPathUploadedFileName = $FullPathDirectoryUpload.$FilenameUploaded;
                                        $HashMD5 = md5_file($FullPathUploadedFileName);

                                    }

                                    if (!(file_exists($FullPathUploadedFileName))) {

                                        if( @move_uploaded_file($_FILES[$aParamName]['tmp_name'], $FullPathUploadedFileName) ) {

                                            if (!(@chmod($FullPathUploadedFileName,0644))) {
                                                $ResultMessage = 'Errore durante il caricamento del file';                                            
                                            }

                                            return array(
                                            'filename_uploaded' => $_FILES[$aParamName]['name'],
                                            'filename_saved' => $FullPathUploadedFileName,
                                            'filename_url' => "/{$DirectoryUpload}/{$FilenameUploaded}",
                                            'filesize' =>  $_FILES[$aParamName]['size'],
                                            'fileHashMD5' => $HashMD5
                                            );


                                        } else {

                                            $ResultMessage = 'Impossibile rinominare il file';

                                        }

                                    } else {
                                        return array(
                                        'filename_uploaded' => $_FILES[$aParamName]['name'],
                                        'filename_saved' => $FullPathUploadedFileName,
                                        'filename_url' => "/{$DirectoryUpload}/{$FilenameUploaded}",
                                        'filesize' =>  @filesize($FullPathUploadedFileName),
                                        'fileHashMD5' => $HashMD5
                                        );     
                                    }


                                } else {
                                    $ResultMessage = 'Il file per essere caricato deve avere una di queste estensioni: '.join(',',$ExtensionAllowed);
                                }

                            } else {
                                $ResultMessage = 'Non si possiedono i permessi per salvare il file';
                            }


                        } else {

                            $ResultMessage = 'Non si possiedono i permessi per salvare il file';

                        }


                    } else {

                        switch ($_FILES[$aParamName]['error'] ) {
                            case UPLOAD_ERR_INI_SIZE:
                                $ResultMessage= 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                                break;
                            case UPLOAD_ERR_FORM_SIZE:
                                $ResultMessage = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                $ResultMessage = 'The uploaded file was only partially uploaded';
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                $ResultMessage = 'No file was uploaded';
                                break;
                            case UPLOAD_ERR_NO_TMP_DIR:
                                $ResultMessage = 'Missing a temporary folder';
                                break;
                            case UPLOAD_ERR_CANT_WRITE:
                                $ResultMessage = 'Failed to write file to disk';
                                break;
                            case UPLOAD_ERR_EXTENSION:
                                $ResultMessage = 'File upload stopped by extension';
                                break;
                            default:
                                $ResultMessage = 'Unknown upload error';

                        }                    

                    }

                } else {
                    $ResultMessage = 'Upload non permesso. Verificare il file php.ini';
                    $ErrorCode = 101;
                }

            } else {
                return null;
            }

        }    

    }


?>
