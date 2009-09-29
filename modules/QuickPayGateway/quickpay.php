<?php
// $Id: quickpay.phps,v 1.17 2005/12/14 19:16:58 ta Exp $

/**
 * @package QuickPay
 * @abstract This API is used to interact with QuickPay payment gateway. Use
this API if implementing QuickPay in a PHP enviroment. Requires PHP to be
compiled with CURL and XML support.
 * @see http://www.quickpay.dk/vejledning/integration/apiprotokol.php
 */

/*
### INTRO ###
This is an example of how one can communicate the quickpay payment gateway.
Below are two examples on how to use the API for authorization and capture.


### STATUS CODES ###
Status codes for the qpstat-array return after communicating with Quickpay

    $qpstatText = array(
                "000" => "Godkendt",
                "001" => "Afvist af PBS",
                "002" => "Kommunikations fejl",
                "003" => "Kort udløbet",
                "004" => "Status er forkert (Ikke autoriseret)",
                "005" => "Autorisation er forældet",
                "006" => "Fejl hos PBS",
                "007" => "Fejl hos QuickPay",
                "008" => "Fejl i parameter sendt til QuickPay"
                );


### EXAMPLES OF USAGE ####
These examples requires PHP5 with the extensions "curl" and "xml" installed.
Please note that you need to fill in your own values!

    *****************************
    *** To authorize a transaction: ***

    $eval = false;
    try {
        $qp = new quickpay;
        // Set values
        $qp->set_msgtype('1100');
        $qp->set_md5checkword('abcd');
        $qp->set_merchant('xxxxxxxx');
        $qp->set_posc('K00500K00130');
        $qp->set_cardnumber('4571xxxxxxxxxx');
        $qp->set_expirationdate('YYMM');
        $qp->set_cvd('yyy');
        $qp->set_ordernum('xxxx'); // MUST at least be of length 4
        $qp->set_amount('100');
        $qp->set_currency('DKK');
        // Set some custom variables
        $qp->add_customVars('Name1', 'Value1');
        $qp->add_customVars('Name2', 'Value2');
        // Commit the authorization
        $eval = $qp->authorize();
    } catch (Exception $e) {
        // Print error message
        echo "<b>Caught an exception in \"". $e->getFile() . "\" at line " .
$e->getLine() . "</b><br />" . $e->getMessage() . "<br />";
    }


    if ($eval) {
        if ($eval['qpstat'] === '000') {
            // The authorization was completed
            echo 'Authorization: ' . $qpstatText["" . $eval['qpstat'] . ""] .
'<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        } else {
            // An error occured with the authorize
            echo 'Authorization: ' . $qpstatText["" . $eval['qpstat'] . ""] .
'<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        }
    } else {
        // Communication error
    }

    ****************************
    *** To capture a transaction: ***

    $eval = false;
    try {
        $qp = new quickpay;
        // Set values
        $qp->set_msgtype('1220');
        $qp->set_md5checkword('abcd');
        $qp->set_merchant('xxxxxxxx');
        $qp->set_transaction('x');
        $qp->set_amount('x');
        // Commit the capture
        $eval = $qp->capture();
    } catch (Exception $e) {
        // Print error message
        echo "<b>Caught an exception in \"". $e->getFile() . "\" at line " .
$e->getLine() . "</b><br />" . $e->getMessage() . "<br />";
    }


    if ($eval) {
        if ($eval['qpstat'] === '000') {
            // The capture was completed
            echo 'Capture: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br
/>';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        } else {
            // An error occured with the capture
            echo 'Capture: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br
/>';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        }
    } else {
        // Communication error
    }

    ****************************
    *** To reverse a transaction: ***

    $eval = false;
    try {
        $qp = new quickpay;
        // Set values
        $qp->set_msgtype('1420');
        $qp->set_md5checkword('abcd');
        $qp->set_merchant('xxxxxxxx');
        $qp->set_transaction('x');
        // Commit the reversal
        $eval = $qp->reverse();
    } catch (Exception $e) {
        // Print error message
        echo "<b>Caught an exception in \"". $e->getFile() . "\" at line " .
$e->getLine() . "</b><br />" . $e->getMessage() . "<br />";
    }

    if ($eval) {
        if ($eval['qpstat'] === '000') {
            // The reversal was completed
            echo 'Reverse: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br
/>';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        } else {
            // An error occured with the reversal
            echo 'Reverse: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br
/>';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        }
    } else {
        // Communication error
    }


    ****************************
    *** To credit a transaction: ***

    $eval = false;
    try {
        $qp = new quickpay;
        // Set values
        $qp->set_msgtype('credit');
        $qp->set_md5checkword('abcd');
        $qp->set_merchant('xxxxxxxx');
        $qp->set_transaction('x');
        $qp->set_amount('x');
        // Commit the credit
        $eval = $qp->credit();
    } catch (Exception $e) {
        // Print error message
        echo "<b>Caught an exception in \"". $e->getFile() . "\" at line " .
$e->getLine() . "</b><br />" . $e->getMessage() . "<br />";
    }


    if ($eval) {
        if ($eval['qpstat'] === '000') {
            // The credit was completed
            echo 'Credit: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        } else {
            // An error occured with the credit
            echo 'Credit: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        }
    } else {
        // Communication error
    }


    ********************************
    *** To get status on a transaction: ***

    $eval = false;
    try {
        $qp = new quickpay;
        // Set values
        $qp->set_msgtype('status');
        $qp->set_md5checkword('abcd');
        $qp->set_merchant('xxxxxxxx');
        $qp->set_transaction('x');
        // Commit the status request
        $eval = $qp->status();
    } catch (Exception $e) {
        // Print error message
        echo "<b>Caught an exception in \"". $e->getFile() . "\" at line " .
$e->getLine() . "</b><br />" . $e->getMessage() . "<br />";
    }

    if ($eval) {
        if ($eval['qpstat'] === '000') {
            // The status request was completed
            echo 'Status request: ' . $qpstatText["" . $eval['qpstat'] . ""] .
'<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        } else {
            // An error occured with the status request
            echo 'Status request: ' . $qpstatText["" . $eval['qpstat'] . ""] .
'<br />';
            echo "<pre>";
            var_dump($eval);
            echo "</pre>";
        }
    } else {
        // Communication error
    }

    *******************************

*/


class quickpay {

    /**
     * @var string URL to QuickPay gateway
     */
    var $gatewayUrl = 'https://secure.quickpay.dk/transaction.php';
    /**
     * @var string MD5 checkword for data integrity ensurance
     */
    var $md5checkword;
    /**
     * @var string Message type
     */
    var $msgtype;
    /**
     * @var string Card number
     */
    var $cardnumber;
    /**
     * @var string Transaction amount
     */
    var $amount;
    /**
     * @var string Card expiration date (YYMM)
     */
    var $expirationdate;
    /**
     * @var string Posc (currently only take this value 'K00500K00130')
     */
    var $posc;
    /**
     * @var string Order number of the transaction (must be unique)
     */
    var $ordernum;
    /**
     * @var string 3 character ISO currency (Danish krone = 'DKK')
     */
    var $currency;
    /**
     * @var string Card CVD number
     */
    var $cvd;
    /**
     * @var string QuickPay merchant id
     */
    var $merchant;
    /**
     * @var string transaction id
     */
    var $transaction;
    /**
     * @var string Authorzation type [preauth, recurring]
     */
    var $authtype;
    /**
     * @var string Text for identifying a subscription payment
     */
    var $reference;
    /**
     * @var array Key/values in this array will be available in your QuickPay
administration page
     */
    var $customVars = array();
    /**
     * @var array Used internaly for XML parsing
     */
    var $arr;




    /**
    * Constructor
    * @return void
    */
    public function __construct() {
        //check for curl and xml
        if(!extension_loaded('curl')) {
            throw new Exception("The \"curl\" extension must be installed on
your server<br />");
        }
        if(!extension_loaded('xml')) {
            throw new Exception("The \"xml\" extension must be installed on your
server<br />");
        }
    }

    /**
    * authorize transaction
    * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
    */
    public function authorize() {

        switch ($this->authtype) {
            case 'preauth':
            $this->checkVars('authPre');
            break;
            case 'recurring':
            $this->cardnumber = '';
            $this->expirationdate = '';
            $this->cvd = '';
            $this->checkVars('authRec');
            break;
            default:
            $this->checkVars('auth');
        }

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'cardnumber' ] = $this->cardnumber;
        $postData[ 'amount' ] = $this->amount;
        $postData[ 'expirationdate' ] = $this->expirationdate;
        $postData[ 'posc' ] = $this->posc;
        $postData[ 'ordernum' ] = $this->ordernum;
        $postData[ 'currency' ] = $this->currency;
        $postData[ 'cvd' ] = $this->cvd;
        $postData[ 'merchant' ] = $this->merchant;
        $postData['authtype'] = $this->authtype;
        $postData['reference'] = $this->reference;
        $postData['transaction'] = $this->transaction;
        $postData[ 'md5checkV2' ] =
md5($this->msgtype.$this->cardnumber.$this->amount.$this->expirationdate.$this->posc.$this->ordernum.$this->currency.$this->cvd.$this->merchant.$this->authtype.$this->reference.$this->transaction.$this->md5checkword);

        // Add Custom variables
        foreach ($this->customVars as $key => $val) {
            $postData[$key] = $val;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
    * Capture transaction
    * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
    */
    public function capture(){

        $this->checkVars('cap');

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'amount' ] = $this->amount;
        $postData[ 'merchant' ] = $this->merchant;
        $postData[ 'transaction' ] = $this->transaction;
        $postData[ 'md5check' ] =
md5($this->msgtype.$this->amount.$this->merchant.$this->transaction.$this->md5checkword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
    * Reverse  transaction
    * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
    */
    public function reverse() {
        $this->checkVars('rev');

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'merchant' ] = $this->merchant;
        $postData[ 'transaction' ] = $this->transaction;
        $postData[ 'md5check' ] =
md5($this->msgtype.$this->merchant.$this->transaction.$this->md5checkword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
     *Status on transaction
    * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
     */
    public function credit() {

        $this->checkVars('credit');

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'merchant' ] = $this->merchant;
        $postData[ 'transaction' ] = $this->transaction;
        $postData['amount'] = $this->amount;
        $postData[ 'md5check' ] =
md5($this->msgtype.$this->amount.$this->merchant.$this->transaction.$this->md5checkword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
     * Status on transaction
     * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
     */
    public function status() {
        $this->checkVars('stat');

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'merchant' ] = $this->merchant;
        $postData[ 'transaction' ] = $this->transaction;
        $postData[ 'md5check' ] =
md5($this->msgtype.$this->merchant.$this->transaction.$this->md5checkword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
     * Get status of clearing houses
     * @return mixed    Return an array with statuscode if communcation with
quickpay succeeded - otherwise false
     */
    public function pbs_status() {
        $this->checkVars('pbs_stat');

        $postData = array();
        $postData[ 'msgtype' ] = $this->msgtype;
        $postData[ 'merchant' ] = $this->merchant;
        $postData[ 'md5check' ] =
md5($this->msgtype.$this->merchant.$this->md5checkword);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $responseArr = $this->parse($response);

        return $responseArr;
    }

    /**
    * Checks whether the correct values are set for the object
    * @param
    * @return
    */
    private function checkVars($type) {

        $authVars =
'md5checkword,msgtype,cardnumber,amount,expirationdate,posc,ordernum,currency,cvd,merchant';
        $authVarsPre =
'md5checkword,msgtype,cardnumber,amount,expirationdate,posc,ordernum,currency,cvd,merchant';
        $authVarsRec =
'md5checkword,msgtype,cardnumber,amount,expirationdate,posc,ordernum,currency,cvd,merchant';
        $capVars = 'md5checkword,msgtype,amount,merchant,transaction';
        $revVars = 'md5checkword,msgtype,merchant,transaction';
        $statVars = 'md5checkword,msgtype,merchant,transaction';
        $creditVars = 'md5checkword,msgtype,amount,merchant,transaction';
        $pbsStatVars = 'md5checkword,msgtype,merchant';

        if ($type === 'auth') {
            $vars = explode(',', $authVars);
        }
        if ($type === 'authPre') {
            $vars = explode(',', $authVarsPre);
        }
        if ($type === 'authRec') {
            $vars = explode(',', $authVarsRec);
        }
        if ($type === 'cap') {
            $vars = explode(',', $capVars);
        }
        if ($type === 'rev') {
            $vars = explode(',', $revVars);
        }
        if ($type === 'credit') {
            $vars = explode(',', $creditVars);
        }
        if ($type === 'stat') {
            $vars = explode(',', $statVars);
        }
        if ($type === 'pbs_stat') {
            $vars = explode(',', $pbsStatVars);
        }

        if (is_array($vars)) {
            for ($i = 0; $i < count($vars); $i++) {
                if (!isset($this->$vars[$i])) {
                    throw new Exception("You need to set " . $vars[$i] . " -
example: \$obj->set_" . $vars[$i] . "(&lt;value&gt;);");
                }
            }
        }
    }

    /**
    * XML parser
    * @return mixed    Returns an array if XML is provided - otherwise NULL is
returned
    */
    private function parse($xmlstring="") {
        // set up a new XML parser to do all the work for us
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($this->parser, "startElement", "endElement");
        xml_set_character_data_handler($this->parser, "characterData");

        // parse the data and free the parser...
        xml_parse($this->parser, $xmlstring);
        xml_parser_free($this->parser);

        return $this->arr;
    }

    /**
    * XML helper function
    * @return void
    */
    private function startElement($parser, $name, $attrs) {
        if($name == "values" ){
            $this->arr = $attrs;
        }
    }
    /**
    * XML helper function
    * @return void
    */
    private function endElement($parser, $name)    {/* Vi bruger ikke noget fra
endElementet.*/}
    /**
    * XML helper function
    * @return void
    */
    private function characterData($parser, $data) {/* Vi har ingen
CharacterData.*/}

    /**
    * Get the gatewayUrl
    * @return string gatewayUrl
    */
    public function get_gatewayUrl() {
        return (string) $this->gatewayUrl;
    }

    /**
    * Set the gatewayUrl
    * @param string    gatewayUrl
    * @return void
    */
    public function set_gatewayUrl($gatewayUrl) {
        $this->gatewayUrl = (string) $gatewayUrl;
    }

    /**
    * Get the md5 check word
    * @return string    md5 check word
    */
    public function get_md5checkword() {
        return (string) $this->md5checkword;
    }

    /**
    * Set the md5 check word
    * @param string    md5 check word
    * @return void
    */
    public function set_md5checkword($md5checkword) {
        $this->md5checkword = (string) $md5checkword;
    }


    /**
    * Get the transaction msgtype
    * @return string    Returns the transaction msgtype
    */
    public function get_msgtype() {
        return (string) $this->msgtype;
    }

    /**
    * Set the transaction msgtype
    * @param string    Transaction msgtype
    * @return void
    */
    public function set_msgtype($msgtype) {
        $this->msgtype = (string) $msgtype;
    }

    /**
    * Get the card number for the transaction
    * @return string    Card no.
    */
    public function get_cardnumber() {
        return (string) $this->cardnumber;
    }

    /**
    * Set the card number for the transaction
    * @param string    Card no.
    * @return void
    */
    public function set_cardnumber($cardnumber) {
        $this->cardnumber = (string) $cardnumber;
    }

    /**
    * Get the amount of the transaction
    * @return string    Amount
    */
    public function get_amount() {
        return (string) $this->amount;
    }

    /**
    * Set the amount of the transaction
    * @param string    Amount
    * @return void
    */
    public function set_amount($amount) {
        $this->amount = (string) $amount;
    }

    /**
    * Get the expirationdate of the transaction
    * @return string    Expiration date
    */
    public function get_expirationdate() {
        return (string) $this->expirationdate;
    }

    /**
    * Set the expirationdate of the transaction
    * @param string    Expiration date
    * @return void
    */
    public function set_expirationdate($expirationdate) {
        $this->expirationdate = (string) $expirationdate;
    }

    /**
    * Get the transaction type (posc)
    * @return string    Transaction type
    */
    public function get_posc() {
        return (string) $this->posc;
    }

    /**
    * Set the transaction type (posc)
    * @param string    Transaction type
    * @return void
    */
    public function set_posc($posc) {
        $this->posc = (string) $posc;
    }

    /**
    * Get the order no. the applies to the transaction
    * @return string    Order no.
    */
    public function get_ordernum() {
        return (string) $this->ordernum;
    }

    /**
    * Set the order no. the applies to the transaction
    * @param string    Order no.
    * @return void
    */
    public function set_ordernum($ordernum) {
        $this->ordernum = (string) $ordernum;
    }

    /**
    * Get the currency of the transaction
    * @return string    Currency
    */
    public function get_currency() {
        return (string) $this->currency;
    }

    /**
    * Set the currency of the transaction
    * @param string    Currency
    * @return void
    */
    public function set_currency($currency) {
        $this->currency = (string) $currency;
    }

    /**
    * Get the card verification digits
    * @return string    CVD
    */
    public function get_cvd() {
        return (string) $this->cvd;
    }

    /**
    * Set the card verification digits
    * @param string    CVD
    * @return void
    */
    public function set_cvd($cvd) {
        $this->cvd = (string) $cvd;
    }

    /**
    * Get the merchant of transaction
    * @return string    Merchant
    */
    public function get_merchant() {
        return (string) $this->merchant;
    }

    /**
    * Set the merchant of the transaction
    * @param string    Merchant
    * @return void
    */
    public function set_merchant($merchant) {
        $this->merchant = (string) $merchant;
    }

    /**
    * Get the transaction authtype
    * @return string    Transaction authtype
    */
    public function get_authtype() {
        return (string) $this->authtype;
    }

    /**
    * Set the transaction authtype
    * @param string    Transaction authtype
    * @return void
    */
    public function set_authtype($authtype) {
        $this->authtype = (string) $authtype;
    }

    /**
    * Get the transaction reference
    * @return string    Transaction reference
    */
    public function get_reference() {
        return (string) $this->reference;
    }

    /**
    * Set the transaction reference
    * @param string    Transaction reference
    * @return void
    */
    public function set_reference($reference) {
        $this->reference = (string) $reference;
    }

    /**
    * Get the transaction id
    * @return string    Transaction id
    */
    public function get_transaction() {
        return (string) $this->transaction;
    }

    /**
    * Set the transaction id
    * @param string    Transaction id
    * @return void
    */
    public function set_transaction($transaction) {
        $this->transaction = (string) $transaction;
    }

    /**
    * Add a custom variable
    * @param string     Name
    * @param string     Value
    * @return void
    */
    public function add_customVars($name, $value) {
        if (!empty($name)) {
            $this->customVars["CUSTOM_$name"] = $value;
        }
    }
}

?>
