<?

    if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    

    require_once(PATH_TO_FRAMEWORK_BASECLASS.'TBaseClass.php');

    if (!function_exists('mime_content_type')){
        function mime_content_type($file){
            if(!is_readable($file)) return false;
            @$size = getimagesize($file);
            if(!empty($size[mime])){
                return($size[mime]);
            }else{
                $extensions = array('doc' => 'application/msword', 'html'=> 'text/html', 'htm' => 'text/html',
                'pdf' => 'application/pdf', 'ppt' => 'application/vnd.ms-powerpoint', 'rtf' => 'text/rtf',
                'xls' => 'application/vnd.ms-excel', 'zip' => 'application/zip');
                $keys = array_keys($extensions);
                $parts = array_reverse(explode('.', $file));
                $extension = $parts['0'];
                if(in_array($extension, $keys)) return $extensions[$extension];
                $data = file_get_contents($filename);
                $bad = false;
                for($x = 0, $y = strlen($data); !$bad && $x < $y; $x++){
                    $bad = (ord($data{$x}) > 127);
                }
                if(!$bad) return ('text/plain');
                return('application/octet-stream');
            }
        }
    }

    // retrieve images on the site
    function get_images($file){
        $h1count = preg_match_all('/(background|src)=("|\')([^"\'>]+)/imsx',$file,$patterns);
        return $patterns[3];
    }     



    class GenericMailer extends TBaseClass {

        var $Sender;
        var $CarbonCopy = array();
        var $BlindCarbonCopy = array();
        var $Recipients = array();
        var $Subject;
        var $Message;
        var $SenderMail;
        var $SenderName;
        var $SendAsHTML = false;
        var $ReplyTo = NULL;
        var $ErrorMessage = array();
        var $SendMailToEachRecipients = true;
        var $Attachement = array();

        var $FOnSendSuccess = NULL;
        var $FOnSendError = NULL;
        var $FOnSendPrepare = NULL;
        var $FOnSendComplete = NULL;

        var $LastError = null;

        var $Headers = array();

        function SendSuccess($To){
            if (!is_null($this->FOnSendSuccess)) {
                $this->DispachEvent($this->FOnSendSuccess,$To);
            }
        }

        function SendError($To,$ErrorMessage){
            if (!is_null($this->FOnSendError)) {
                $Params = array($To,$ErrorMessage);
                $this->DispachEvent($this->FOnSendError,$Params);
            }

        }

        function SendPrepare($To,$Remain,$Total){
            if (!is_null($this->FOnSendPrepare)) {
                $Params = array($To,$Remain,$Total);
                $this->DispachEvent($this->FOnSendPrepare,$Params);
            }

        }        

        function SendComplete(){
            if (!is_null($this->FOnSendComplete)) {
                $this->DispachEvent($this->FOnSendComplete);
            }

        }

        function  __construct($aSenderMail,$aSender = NULL) {
            $this->SenderMail = $aSenderMail;
            $this->SenderName = $aSender;

            parent::__construct(null);
        }

        function AddToCarbonCopy($aEmail,$aName = null ){
            $this->CarbonCopy[$aEmail] = $aName;
        }

        function AddToBlindCarbonCopy($aEmail,$aName = null ){
            $this->BlindCarbonCopy[$aEmail] = $aName;
        }

        function AddToRecipients($aEmail,$aName = null ){
            $this->Recipients[$aEmail] = $aName;
        }

        function AddHeader($aHeaderLine) {
            $this->Headers[] = $aHeaderLine;
        }

        function SendMail($aMessage,$aSubject = NULL){
            $this->LastError = null;
            $this->ErrorMessage = array();
            if (!is_array($aMessage)) $this->Message = $aMessage;
            if (!is_array($aSubject)) $this->Subject = $aSubject;

            //$theToArray = array_keys($this->Recipients);
            $theToArray = array();

            if (!$this->SendMailToEachRecipients) {

                if (!empty($this->SenderMail)) { $this->AddHeader('From: '.$this->SenderName.' <'.$this->SenderMail.'>'); }

                if (!is_null($this->ReplyTo)) { $this->AddHeader('Reply-To: '.$this->ReplyTo); }                 

                if ($this->SendAsHTML) {
                    //$this->AddHeader('MIME-Version: 1.0');
                    //                    $this->AddHeader('Content-type: text/html; charset=iso-8859-1');
                    $this->AddHeader('MIME-Version: 1.0');
                    $this->AddHeader('Content-Type: text/html; charset="iso-8859-1"');
                    $this->AddHeader('Content-Transfer-Encoding: 7bit');
                }

                if (!empty($this->Recipients)) {

                    $theToArray = array();

                    foreach ($this->Recipients as $theEmail=>$theName) {

                        $theToArray[] = "$theName <$theEmail>";
                    }
                     
                    //$this->AddHeader('To: '.join(',',$theRecipients));

                } else {
                    $this->ErrorMessage[] = 'Non sono stati specificati i destinatari della mail';
                }                


                $theCarbonCopy = array();
                foreach ($this->CarbonCopy as $theEmail=>$theName){
                    $theCarbonCopy[] = "$theName <$theEmail>";                    
                }                
                if (!empty($theCarbonCopy)){
                    $this->AddHeader('CC: '.join(',',$theCarbonCopy));
                }


                $theBlindCarbonCopy = array();
                foreach ($this->BlindCarbonCopy as $theEmail=>$theName){
                    $theBlindCarbonCopy[] = "$theName <$theEmail>";
                }                
                if (!empty($theBlindCarbonCopy)){
                    $this->AddHeader('BCC: '.join(',',$theBlindCarbonCopy));
                }


                $To = join(',',$theToArray);

                if ( @mail($To,$this->Subject,$this->Message,join("\r\n",$this->Headers)) ) {
                    return true;
                } else {
                    //$this->LastError = $php_errormsg;
                    $this->SendError($To,error_get_last());
                    return false;
                }


            } else {

                $this->ErrorMessage = array();

                if ($this->SendAsHTML) {

                    $boundary  = "--------------".md5(time());

                    $this->AddHeader("MIME-Version: 1.0"); 
                    $this->AddHeader("Content-Type: multipart/related;"); 
                    $this->AddHeader(" type=\"multipart/alternative\";"); 
                    $this->AddHeader(" boundary=\"$boundary\";");                        

                }                

                if (!empty($theToArray)) {

                    $To = array_pop($theToArray);

                    //$this->AddHeader('To: '.$this->Recipients[$To].' <'.$To.'>');
                    if (!empty($this->SenderMail)) { $this->AddHeader('From: '.$this->SenderName.' <'.$this->SenderMail.'>'); }

                    if (!is_null($this->ReplyTo)) { $this->AddHeader('Reply-To: '.$this->ReplyTo); }                 

                    $this->SendPrepare($To,(count($this->Recipients)-count($theToArray)),count($this->Recipients));

                    do {

                        if (isset($boundary)){

                            $attachements = array();
                            $attachements[] = array('header'=>null,'content'=>'This is a multi-part message in MIME format.');
                            $html = (is_array($aMessage)) ? $aMessage[$To] : $html = $aMessage;
                            $attachements[] = array('header'=>array('Content-Type: text/html; charset=UTF-8','Content-Transfer-Encoding: 8bit'),'content'=>&$html);

                            $r = get_images($html);
                            if (!empty($r)){

                                foreach ($r as $i=>$param){

                                    $pathToFilename = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$param;
                                    $filename = basename($param);
                                    if (file_exists($pathToFilename)){
                                        $inline = chunk_split(base64_encode(file_get_contents($pathToFilename)));

                                        $cid = 'fileparts'.$i.'.'.crc32($param).'.'.crc32(time());

                                        $mimeType = mime_content_type($pathToFilename);
                                        $attachements[] = array(
                                        'header'=>array('Content-Type: '.$mimeType.';\n name="'.$filename.'"','Content-Transfer-Encoding: base64','Content-ID: <'.$cid.'>','Content-Disposition: inline;\n filename="'.$filename.'"'),
                                        'content'=>$inline
                                        );
                                        $html = str_replace($param,"cid:$cid",$html);
                                    }
                                }
                            }

                            unset($i);
                            unset($param);
                            unset($r);          
                            unset($inline);          
                            unset($cid);          
                            unset($filename);          


                            reset($attachements);
                            while ($parts = current($attachements)){

                                if (!is_null($parts['header'])) {
                                    $message_header = join("\n",$parts['header'])."\n";        
                                } else {
                                    $message_header = null;
                                }
                                $message_body = $parts['content'];
                                $has_next = next($attachements);
                                if ($has_next===false){        
                                    $message_body .= "--{$boundary}--";        
                                } else {
                                    if (!is_null($message_header)){
                                        $message_body .= "\n\n";
                                    } else {
                                        $message_body .= "\n";
                                    }
                                    $message_body .= "--{$boundary}\n";
                                }

                                if (!is_null($message_header)) {
                                    $message .= $message_header."\n\n".$message_body;
                                } else {
                                    $message .= $message_body;
                                }

                            }                            

                            if (is_array($aSubject)) $this->Subject = $aSubject[$To];

                            if ( mail($To,$this->Subject,$message,join("\r\n",$this->Headers)) ) {
                                $this->SendSuccess($To);
                            } else {
                                $this->SendError($To,error_get_last());
                            }                    

                            unset($message_header);
                            unset($message_body);
                            unset($message);

                        } else {

                            if (is_array($aMessage)) $this->Message = $aMessage[$To];
                            if (is_array($aSubject)) $this->Subject = $aSubject[$To];

                            if ( mail($To,$this->Subject,$this->Message,join("\r\n",$this->Headers)) ) {

                                $this->SendSuccess($To);

                            } else {
                                $this->SendError($To,error_get_last());
                            }                    

                        }
                        $To = array_pop($theToArray);

                    } while (!is_null($To));

                    $this->SendComplete();
                }


            }




        }

        function PrepareMail($aMessage,$aSubject = NULL){

            $this->ErrorMessage = array();
            $this->Headers = array();

            if (!is_array($aMessage)) $this->Message = $aMessage;
            if (!is_array($aSubject)) $this->Subject = $aSubject;

            $theToArray = array_keys($this->Recipients);

            if (!$this->SendMailToEachRecipients) {

                if ($this->SendAsHTML) {
                    $this->AddHeader('MIME-Version: 1.0');
                    $this->AddHeader('Content-Type: text/html; charset="iso-8859-1"');
                    $this->AddHeader('Content-Transfer-Encoding: 7bit');
                }

                if (!empty($this->Recipients)) {

                    $theRecipients = array();

                    foreach ($this->Recipients as $theEmail=>$theName) {

                        $theRecipients[] = "$theName <$theEmail>";
                    }

                    $this->AddHeader('To: '.join(',',$theRecipients));

                } else {
                    $this->ErrorMessage[] = 'Non sono stati specificati i destinatari della mail';
                }                

                $To = join(',',$theToArray);

                //if ( mail($To,$this->Subject,$this->Message,join("\r\n",$this->Headers)) ) {
                return array('to'=>$To,'subject'=>$this->Subject,'message'=>$this->Message,'headers'=>join("\r\n",$this->Headers));
                //} else {
                //return false;
                //}


            } else {

                $this->ErrorMessage = array();

                if ($this->SendAsHTML) {

                    $boundary  = "--------------".md5(time());

                    $this->AddHeader("MIME-Version: 1.0"); 
                    $this->AddHeader("Content-Type: multipart/related;"); 
                    $this->AddHeader(" type=\"multipart/alternative\";"); 
                    $this->AddHeader(" boundary=\"$boundary\";");                        

                }                

                $result = array();

                if (!empty($theToArray)) {

                    //$this->AddHeader('To: '.$this->Recipients[$To].' <'.$To.'>');
                    if (!empty($this->SenderMail)) { $this->AddHeader('From: '.$this->SenderName.' <'.$this->SenderMail.'>'); }

                    if (!is_null($this->ReplyTo)) { $this->AddHeader('Reply-To: '.$this->ReplyTo); }                 

                    $To = array_pop($theToArray);

                    do {

                        if (isset($boundary)){

                            $attachements = array();
                            $attachements[] = array('header'=>null,'content'=>'This is a multi-part message in MIME format.');
                            $html = (is_array($aMessage)) ? $aMessage[$To] : $html = $aMessage;
                            $attachements[] = array('header'=>array('Content-Type: text/html; charset=UTF-8','Content-Transfer-Encoding: 8bit'),'content'=>&$html);

                            $r = get_images($html);
                            if (!empty($r)){

                                foreach ($r as $i=>$param){

                                    $pathToFilename = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$param;
                                    $filename = basename($param);
                                    if (file_exists($pathToFilename)){
                                        $inline = chunk_split(base64_encode(file_get_contents($pathToFilename)));

                                        $cid = 'fileparts'.$i.'.'.crc32($param).'.'.crc32(time());

                                        $mimeType = mime_content_type($pathToFilename);
                                        $attachements[] = array(
                                        'header'=>array('Content-Type: '.$mimeType."; name=\"".$filename.'"','Content-Transfer-Encoding: base64','Content-ID: <'.$cid.'>',"Content-Disposition: inline; filename=\"".$filename.'"'),
                                        'content'=>$inline
                                        );
                                        $html = str_replace($param,"cid:$cid",$html);
                                    }
                                }
                            }

                            unset($i);
                            unset($param);
                            unset($r);          
                            unset($inline);          
                            unset($cid);          
                            unset($filename);          


                            reset($attachements);
                            while ($parts = current($attachements)){

                                if (!is_null($parts['header'])) {
                                    $message_header = join("\n",$parts['header'])."\n";        
                                } else {
                                    $message_header = null;
                                }
                                $message_body = $parts['content'];
                                $has_next = next($attachements);
                                if ($has_next===false){        
                                    $message_body .= "--{$boundary}--";        
                                } else {
                                    if (!is_null($message_header)){
                                        $message_body .= "\n\n";
                                    } else {
                                        $message_body .= "\n";
                                    }
                                    $message_body .= "--{$boundary}\n";
                                }

                                if (!is_null($message_header)) {
                                    $message .= $message_header."\n\n".$message_body;
                                } else {
                                    $message .= $message_body;
                                }
                                unset($message_header);
                                unset($message_body);
                            }                            

                            if (is_array($aSubject)) $this->Subject = $aSubject[$To];

                            $result[] = array('to'=>$To,'subject'=>$this->Subject,'message'=>$message,'headers'=>join("\r\n",$this->Headers));

                            /*                            if ( @mail($To,$this->Subject,$message,join("\r\n",$this->Headers)) ) {
                            $this->SendSuccess($To);
                            } else {
                            $this->SendError($To,error_get_last());
                            }                    */

                            unset($message_header);
                            unset($message_body);
                            unset($message);

                        } else {

                            if (is_array($aMessage)) $this->Message = $aMessage[$To];
                            if (is_array($aSubject)) $this->Subject = $aSubject[$To];

                            $result[] = array('to'=>$To,'subject'=>$this->Subject,'message'=>$this->Message,'headers'=>join("\r\n",$this->Headers));

                            /*                            if ( @mail($To,$this->Subject,$this->Message,join("\r\n",$this->Headers)) ) {

                            $this->SendSuccess($To);

                            } else {
                            $this->SendError($To,error_get_last());
                            }                    */

                        }                        
                        $To = array_pop($theToArray);

                    } while (!is_null($To));
                    return $result;

                    //$this->SendComplete();
                }


            }




        }

        function checkmail($aEmail,&$ResultCode) {


            // First, we check that there's one @ symbol, and that the lengths are right
            if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $aEmail)) {
                // aEmail invalid because wrong number of characters in one section, or wrong number of @ symbols.
                $ResultCode = 1;
                return false;
            }
            // Split it into sections to make life easier
            $aEmail_array = explode("@", $aEmail);
            $local_array = explode(".", $aEmail_array[0]);
            for ($i = 0; $i < sizeof($local_array); $i++) {
                if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                    $ResultCode = 1;
                    return false;
                }
            }
            if (!ereg("^\[?[0-9\.]+\]?$", $aEmail_array[1])) { // Check if domain is IP. If not, it should be valid domain name
                $domain_array = explode(".", $aEmail_array[1]);
                if (sizeof($domain_array) < 2) {
                    $ResultCode = 1;
                    return false; // Not enough parts to domain
                }
                for ($i = 0; $i < sizeof($domain_array); $i++) {
                    if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                        $ResultCode = 1;
                        return false;
                    }
                }
            }
            return true;
        }

    }

?>
