<?php

    if (!defined('PATH_TO_FRAMEWORK_BASECLASS')) trigger_error('PATH_TO_FRAMEWORK_BASECLASS not defined',E_USER_ERROR);    
    require_once(PATH_TO_FRAMEWORK_BASECLASS.'TBaseClass.php');

    class GenericPaymentItem {

        public $HiddenComponents = array();

        public function __construct(){
            $this->HiddenComponents = array();
        }

        public function GenerateHiddenInput(){

            foreach ($this as $field=>$value){
                
                if ((!is_null($value)) && ($field!='HiddenComponents')) {

                    $ih = new THidden($field);
                    $ih->setValue($value);
                    $this->HiddenComponents[] = $ih;

                }

            }

            return $this->HiddenComponents;

        }

    }

    class GenericPaymentOptions {

        public $HiddenComponents;

        public function __construct(){
            $this->HiddenComponents = array();
        }

        public function GenerateHiddenInput(){

            foreach ($this as $field=>$value){

                if ((!is_null($value)) && ($field!='HiddenComponents')) {
                    $ih = new THidden($field);
                    $ih->setValue($value);

                    $this->HiddenComponents[] = $ih;
                }

            }

            return $this->HiddenComponents;

        }


    }

    class GenericPayments extends TBaseClass implements ArrayAccess,IteratorAggregate,Countable{

        private $_components;
        public $HiddenComponents = array();
        public $Action;

        public function __construct($aAction,$aName=null){
            $this->Action = $aAction;
            $this->_components = new SplObjectStorage();
            parent::__construct($aName);
        }

        public function addToComponent(&$aComponent){

            if ($aComponent instanceof TBaseWebComponentContainer){
                $inputToAdd = $this->GenerateHiddenInput();
                if (!empty($inputToAdd)){
                    foreach ($inputToAdd as $i=>$ih){
                        //$z = new THidden($ih->Name);
                        //$z->setValue($ih->getValue());
                        
                        $aComponent[] = $ih;
                        //$aComponent[] = $z;
                    }
                }
            }

        }

        public function GenerateHiddenInput(){ }

        public function getIterator() {
            return $this->_components;
        }

        public function count(){

            return $this->_components->count();

        }



        public function offsetSet($offset, $item) {

            if ($item instanceof GenericPaymentItem){ 

                if (empty($offset)) $offset = $this->_components->count()+1;
                $this->_components->attach($item,$offset);

            } 

        }
        public function offsetExists($item) {
            if ($item instanceof TBaseWebComponent){ 
                return $this->_components->offsetExists($item);
            }
        }
        public function offsetUnset($item) {
            $this->_components->detach($item);
        }
        public function offsetGet($item) {
            if ($item instanceof GenericPaymentItem){ 
                return $this->_components->offsetGet($item);
            }
        }



    }

    ####################################################################################################################################

    class PayPalItem extends GenericPaymentItem{

        public $amount; //The price or amount of the product, service, or contribution, not including shipping, handling, or tax. If omitted from Buy Now or Donate buttons, payers enter their own amount at the time of payment.
        public $discount_amount; //Discount amount associated with an item. 
        public $discount_amount2; //Discount amount associated with each additional quantity of the item.
        public $discount_rate; //Discount rate (percentage) associated with an item. 
        public $discount_rate2; //Discount rate (percentage) associated with each additional quantity of the item. 
        public $discount_num; //Number of additional quantities of the item to which the discount applies. 
        public $item_name; //Description of item. If omitted, payers enter their own name at the time of payment.
        public $item_number; //Pass-through variable for you to track product or service purchased or the contribution made. The value you specify passed back to you upon payment completion. 
        public $quantity; //Number of items. If profile-based shipping rates are configured with a basis of quantity, the sum of quantity values is used to calculate the shipping charges for the transaction. PayPal appends a sequence number to uniquely identify the item in the PayPal Shopping Cart (e.g., quantity1, quantity2).
        public $shipping; //The cost of shipping this item. If you specify shipping and shipping2 is not defined, this flat amount is charged regardless of the quantity of items purchased. 
        public $shipping2; //The cost of shipping each additional unit of this item. If omitted and profile-based shipping rates are configured, buyers are charged an amount according to the shipping methods they choose.
        public $tax;    //Transaction-based tax override variable. Set this to a flat tax amount to apply to the transaction regardless of the buyer’s location. This value overrides any tax settings set in your account profile. Valid only for Buy Now and Add to Cart buttons. Default – Profile tax settings, if any, apply.
        public $tax_rate;   //Transaction-based tax override variable. Set this to a percentage that will be applied to amount multiplied the quantity selected during checkout. This value overrides any tax settings set in your account profile. Allowable values are numbers 0.001 through 100. Valid only for Buy Now and Add to Cart buttons. Default – Profile tax settings, if any, apply. 
        public $undefined_quantity; // 1 – allows buyers to specify the quantity.
        public $weight; //Weight of items. If profile-based shipping rates are configured with a basis of weight, the sum of weight values is used to calculate the shipping charges for the transaction. 
        public $weight_unit;    //The unit of measure if weight is specified. Allowable values: lbs kgs
        public $on0; //First option field name and label. The os0 variable contains the corresponding value for this option field. For example, if on0 is size, os0 could be large. 
        public $on1; //Second option field name and label. The os1 variable contains the corresponding value for this option field. For example, if on1 is color then os1 could be blue.
        public $os0; //Option selection of the buyer for the first option field, on0. If the option field is a dropdown menu or a set of radio buttons, each allowable value should be no more than 64 characters. If buyers enter this value in a text field, there is a 200-character limit. 
        public $os1; //Option selection of the buyer for the second option field, on1. If the option field is a dropdown menu or a set of radio buttons, each allowable value should be no more than 64 characters. If buyers enter this value in a text field, there is a 200-character limit. 
        public $option_index; //The cardinal number of the option field, on0 through on9, that has product options with different prices for each option. You must include option_index if the option field with prices is not on0.
        public $option_select0; //For priced options, the value of the first option selection of the on0 dropdown menu. The values must match exactly, as the next sample code shows:
        public $option_amount0; //For priced options, the amount that you want to charge for the first option selection of the on0 dropdown menu. Use only numeric values; the currency is taken from the currency_code variable. For example:
        public $option_select1; //For priced options, the value of the second option selection of the on0 dropdown menu. For example:
        public $option_amount1; //For priced options, the amount that you want to charge for the second option selection of the on0 dropdown menu. For example:

        public function __construct($aItemNumber,$aItemName,$aAmount,$aQuantity,$aTax=null,$aShippingCost=null,$aWeight=null){

            parent::__construct($aName);
            $this->item_number = $aItemNumber;
            $this->item_name = $aItemName;
            $this->amount = $aAmount;
            $this->quantity = $aQuantity;
            $this->tax = $aTax;
            $this->shipping = $aShippingCost;
            $this->weight = $aWeight;

        }


    }

    class PayPalPayments extends GenericPayments{

        private $cmd; // _xclick, _donations, _xclick-subscriptions, _oe-gift-certificate, _cart, _s-xclick      
        public $notify_url;  //Optional The URL to which PayPal posts information about the transaction, in the form of Instant Payment Notification messages. 
        public $hosted_button_id;  //Required for buttons that have been saved in PayPal accounts; otherwise, not allowed.
        public $bn; //Substitute <Product> with WPS always for Website Payments Standard payment buttons and for the Website Payments Standard Cart Upload command. 
        public $business;

        public $PaymentTransactions;
        public $DisplayingCheckoutPages;
        public $PrepopulatingCheckoutPages;

        public function __construct($aAction,$aType,$aName=null){
            $this->Action = $aAction;
            parent::__construct($aName);

            switch ($aType){
                case "Buy Now":
                    //The button that the person clicked was a Buy Now button. 
                    $this->cmd = '_xclick';
                    break;

                case "Donate":
                    //The button that the person clicked was a Donate button.
                    $this->cmd = '_donations';
                    break;

                case "Subscribe":
                    //The button that the person clicked was a Subscribe button.
                    $this->cmd = '_xclick-subscriptions';
                    break;

                case "Buy Gift":
                    //The button that the person clicked was a Buy Gift Certificate button.
                    $this->cmd = '_oe-gift-certificate';
                    break;

                case "Shopping Cart":
                    //For shopping cart purchases; these additional variables specify the kind of shopping cart button that the person clicked:
                    $this->cmd = '_cart';
                    break;

                case "Saved Button":
                    //The button that the person clicked was protected from tampering by using encryption, or the button was saved in the merchant’s PayPal account. PayPal determines which kind of button was clicked by decoding the encrypted code or by looking up the saved button in the merchant’s account.
                    $this->cmd = '_s-xclick';
                    break;
            }

        }

        public function setPaymentTransactions(PayPalPaymentTransactions $aPayPalPaymentTransactions){
            $this->PaymentTransactions = $aPayPalPaymentTransactions;
        }

        public function setDisplayingCheckoutPages(PayPalDisplayingCheckoutPages $aPayPalDisplayingCheckoutPages){
            $this->DisplayingCheckoutPages = $aPayPalDisplayingCheckoutPages;
        }

        public function setPrepopulatingCheckoutPages(PayPalPrepopulatingCheckoutPages $aPayPalPrepopulatingCheckoutPages){
            $this->PrepopulatingCheckoutPages = $aPayPalPrepopulatingCheckoutPages;
        }

        public function GenerateHiddenInput(){

            $this->HiddenComponents = array();
            if (isset($this->cmd)){
                $ih = new THidden('cmd');
                $ih->setValue($this->cmd);
                $this->HiddenComponents[] = $ih;
            }
            if (isset($this->notify_url)){
                $ih = new THidden('notify_url');
                $ih->setValue($this->notify_url);
                $this->HiddenComponents[] = $ih;
            }
            if (isset($this->hosted_button_id)){
                $ih = new THidden('hosted_button_id');
                $ih->setValue($this->hosted_button_id);
                $this->HiddenComponents[] = $ih;
            }
            if (isset($this->bn)){
                $ih = new THidden('bn');
                $ih->setValue($this->bn);
                $this->HiddenComponents[] = $ih;
            }
            if (isset($this->business)){
                $ih = new THidden('business');
                $ih->setValue($this->business);
                $this->HiddenComponents[] = $ih;
            }

            if (!is_null($this->PaymentTransactions)){
                $this->HiddenComponents = array_merge($this->HiddenComponents, $this->PaymentTransactions->GenerateHiddenInput());
            }
            if (!is_null($this->DisplayingCheckoutPages)){
                $this->HiddenComponents = array_merge($this->HiddenComponents, $this->DisplayingCheckoutPages->GenerateHiddenInput());
            }
            if (!is_null($this->PrepopulatingCheckoutPages)){
                $this->HiddenComponents = array_merge($this->HiddenComponents, $this->PrepopulatingCheckoutPages->GenerateHiddenInput());
            }

            foreach ($this as $ItemProduct){
                $this->HiddenComponents = array_merge($this->HiddenComponents, $ItemProduct->GenerateHiddenInput());
            }

            return $this->HiddenComponents;

        }


    }

    class PayPalPaymentTransactions extends GenericPaymentOptions{

        public $address_override; // 1 – The address specified in prepopulation variables overrides the PayPal member’s stored address. The payer is shown the passed-in address but cannot edit it. No address is shown if the address is not valid, such as missing required fields like country, or is not included at all.
        public $currency_code;  //The currency of the payment. The default is USD. https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes
        public $custom; //Passthrough variable never presented to the payer. Default – No variable is passed back to you.
        public $handling; //Handling charges. This is not quantity-specific. The same handling cost applies, regardless of the number of items on the order. 
        public $invoice; //Passthrough variable you can use to identify your invoice number for this purchase. 
        public $shipping; //The cost of shipping the entire order contained in third-party shopping carts.
        public $tax_cart; //Cart-wide tax, overriding any individual item tax_  x value
        public $weight_cart; //If profile-based shipping rates are configured with a basis of weight, PayPal uses this value to calculate the shipping charges for the transaction. This value overrides the weight values of individual items. 
        public $weight_unit;  //The unit of measure if weight_cart is specified. Allowable values: lbs,kgs


        public function __construct($aCurrencyCode,$aInvoice=null,$aCustom=null,$aAddressOverride=null){
            $this->currency_code = $aCurrencyCode;
            $this->invoice = $aInvoice;
            $this->custom = $aCustom;
            $this->address_override = $aAddressOverride;
            parent::__construct();
        }


    }

    class PayPalDisplayingCheckoutPages extends GenericPaymentOptions{

        public $page_style; //The custom payment page style for checkout pages. Allowable values:paypal , primary ,page_style_name. The default is paypal. 
        public $image_url; //The URL of the 150x50-pixel image displayed as your logo in the upper left corner of the PayPal checkout pages. 
        public $cpp_header_image; //The image at the top left of the checkout page. The image’s maximum size is 750 pixels wide by 90 pixels high. PayPal recommends that you provide an image that is stored only on a secure (https) server. 
        public $cpp_headerback_color; //The background color for the header of the checkout page. 
        public $cpp_headerborder_color; //The border color around the header of the checkout page. The border is a 2-pixel perimeter around the header space, which has a maximum size of 750 pixels wide by 90 pixels high.
        public $cpp_payflow_color; //The background color for the checkout page below the header. Valid value is case-insensitive six-character HTML hexadecimal color code in ASCII.
        public $cs; //The background color of the checkout page. Allowable values: 0 – background color is white 1 – background color is black. The default is 0. 
        public $lc; //The language of the login or sign-up page that subscribers see when they click the Subscribe button. If unspecified, the language is determined by a PayPal cookie in the subscriber’s browser. If there is no PayPal cookie, the default language is U.S. English. 
        public $no_note; //Do not prompt payers to include a note with their payments. Allowable values: 0 – provide a text box and prompt for the note 1 – hide the text box and the prompt The default is 0. 
        public $cn; //Label that appears above the note field. This value is not saved and will not appear in any of your notifications. If omitted, the default label above the note field is “Add special instructions to merchant.” The cn variable is not valid with Subscribe buttons or if you include no_note="1".
        public $no_shipping; //Do not prompt payers for shipping address. Allowable values: 0 – prompt for an address, but do not require one   1 – do not prompt for an address  2 – prompt for an address, and require one  The default is 0.
        public $return; //The URL to which the payer’s browser is redirected after completing the payment; for example, a URL on your site that displays a “Thank you for your payment” page. 
        public $rm; //Return method. The FORM  METHOD used to send data to the URL specified by the return variable after payment completion. Allowable values: 0 – all shopping cart transactions use the GET method  1 – the payer’s browser is redirected to the return URL by the GET method, and no transaction variables are sent 2 – the payer’s browser is redirected to the return URL by the POST method, and all transaction variables are also posted
        public $cbt;    //Sets the text for the Return to Merchant button on the PayPal Payment Complete page. For Business accounts, the return button displays your business name in place of the word “Merchant” by default. For Donate buttons, the text reads “Return to donations coordinator” by default. 
        public $cancel_return;  //A URL to which the payer’s browser is redirected if payment is cancelled; for example, a URL on your website that displays a “Payment Canceled” page.


        public function __construct($aReturn,$aCancelReturn,$aImageUrl){

            $this->return = $aReturn;
            $this->cancel_return = $aCancelReturn;
            $this->image_url = $aImageUrl;

            parent::__construct();
        }


    }

    class PayPalPrepopulatingCheckoutPages extends GenericPaymentOptions{

        public $address1;   //Street (1 of 2 fields)
        public $address2;   //Street (2 of 2 fields)
        public $city;       //City
        public $country;    //Sets shipping and billing country. 
        public $email;      //Email address
        public $first_name; //First name
        public $last_name;  //Last name
        public $lc;         //Sets the payer’s language for the billing information/log-in page only. The default is US.
        public $charset;    //Sets the character encoding for the billing information/log-in page, for the information you send to PayPal in your HTML button code, and for the information that PayPal returns to you as a result of checkout processes initiated by the payment button. The default is based on the character encoding settings in your account profile. 
        public $night_phone_a;  //The area code for U.S. phone numbers, or the country code for phone numbers outside the U.S. This will prepopulate the payer’s home phone number. 
        public $night_phone_b;  //The three-digit prefix for U.S. phone numbers, or the entire phone number for phone numbers outside the U.S., excluding country code. This will prepopulate the payer’s home phone number.
        public $night_phone_c;  //The four-digit phone number for U.S. phone numbers. This will prepopulate the payer’s home phone number.
        public $state;          //State; use Official U.S. Postal Service Abbreviations.
        public $zip;            //Postal code

        public function __construct($aFirstMame,$aLastname,$aAddress,$aCity,$aCounty,$aEmail,$aState,$aPostaCode){

            $this->first_name = $aFirstMame;
            $this->last_name = $aLastname;
            $this->address1 = $aAddress;
            $this->city = $aCity;
            $this->country = $aCounty;
            $this->email = $aEmail;
            $this->state = $aState;
            $this->zip = $aPostaCode;

            parent::__construct();
        }


    }

    class PayPalBuyNow extends PayPalPayments {


        public function __construct($aPayPalID,$aAction,$aName=null){
            
            $this->business = $aPayPalID;
            parent::__construct($aAction,'Buy Now',$aName);


        }

        public function GenerateHiddenInput(){

            parent::GenerateHiddenInput();
            return $this->HiddenComponents;
        }


    }

    class PayPalSubscribe extends PayPalPayments {

        public $business;   //Your PayPal ID or an email address associated with your PayPal account. Email addresses must be confirmed. 
        public $item_name;  //Description of item being sold (maximum 127 characters). If you are collecting aggregate payments, this can include a summary of all items purchased, tracking numbers, or generic terms such as “subscription.” If omitted, customer will see a field in which they have the option of entering an Item Name
        public $currency_code; //The currency of prices for trial periods and the subscription. The default is USD. 
        public $a1; //Trial period 1 price. For a free trial period, specify 0.
        public $p1; //Trial period 1 duration. Required if you specify a1. Specify an integer value in the allowable range for the units of duration that you specify with t1. 
        public $t1; //Trial period 1 units of duration. Required if you specify a1. Allowable values: 
        public $a2; //Trial period 2 price. Can be specified only if you also specify a1. 
        public $p2; //Trial period 2 duration. Required if you specify a2. Specify an integer value in the allowable range for the units of duration that you specify with t2.
        public $t2; //Trial period 2 units of duration. Allowable values: 
        public $a3; //Regular subscription price. 
        public $p3; //Subscription duration. Specify an integer value in the allowable range for the units of duration that you specify with t3.
        public $t3; //Regular subscription units of duration. Allowable values: 
        public $src;    //Recurring payments. Subscription payments recur unless subscribers cancel their subscriptions before the end of the current billing cycle or you limit the number of times that payments recur with the value that you specify for srt. # 0 – subscription payments do not recur 1 – subscription payments recur
        public $srt;    //Recurring times. Number of times that subscription payments recur. Specify an integer above 1. Valid only if you specify src="1". 
        public $sra;    //Reattempt on failure. If a recurring payment fails, PayPal attempts to collect the payment two more times before canceling the subscription. 
        public $no_note;    //Do not prompt payers to include a note with their payments. Allowable values for Subscribe buttons: 1 – hide the text box and the prompt For Subscribe buttons, always include no_note and set it to 1. 
        public $custom; //User-defined field which will be passed through the system and returned in your merchant payment notification email. This field will not be shown to your subscribers.
        public $invoice;    //User-defined field which must be unique with each subscription. The invoice number will be shown to subscribers with the other details of their transactions
        public $modify;     //Modification behavior. Allowable values: 0 – allows subscribers to only create new subscriptions; 1 – allows subscribers to modify their current subscriptions or sign up for new ones; 2 – allows subscribers to only modify their current subscriptions
        public $usr_manage; //Set to 1 to have PayPal generate usernames and initial passwords for subscribers. 


        public function __construct($aPayPalID,$aAction,$aName=null){

            $this->business = $aPayPalID;
            parent::__construct($aAction,'Subscribe',$aName);


        }

    }

?>