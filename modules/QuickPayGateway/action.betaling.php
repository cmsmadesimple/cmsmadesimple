<?php
require(dirname(__FILE__)."/quickpay.php");

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

if (!isset($params["kortnr"])) {
  $params["fejl"]="Det blev ikke angivet et kortnummer";
  $this->Redirect($id,"default",$returnid,$params);
}
$kortnr=$params["kortnr"];
if (strlen($kortnr)<16) {
  $params["fejl"]="Kortnummeret skal være på 16 cifre";
  $this->Redirect($id,"default",$returnid,$params);
}

if (!isset($params["kortcsc"])) {
  $params["fejl"]="Det blev ikke angivet en sikkerhedskode";
  $this->Redirect($id,"default",$returnid,$params);
}

$kortcsc=$params["kortcsc"];
if (strlen($kortcsc)!=3) {
  $params["fejl"]="Sikkerhedskoden skal være på 3 cifre";
  $this->Redirect($id,"default",$returnid,$params);
}

$kortaar=$params["kortaar"];
$kortmaaned=$params["kortmaaned"];

///$ordrenummer=date()//

/*****************************
*** To authorize a transaction: ***/

$eval = false;
try {
  $qp = new quickpay;
  // Set values
  $qp->set_msgtype('1100');
  $qp->set_md5checkword('1u3Y984aF3pfU6EAG8DwcdgLSvCm18B8Z7r7452MbX33VP6l57xy2WHi9QkN412R');
  $qp->set_merchant('87476014'); //merchant
  $qp->set_posc('K00500K00130'); //Sådan skal de bare være Internethandle-secure
  $qp->set_cardnumber($kortnr);
  $qp->set_expirationdate($kortudloeb);
  $qp->set_cvd($kortcsc);
  $qp->set_ordernum($ordrenummer); // MUST at least be of length 4
  $qp->set_amount($beloeb);
  $qp->set_currency('DKK');
  // Set some custom variables
  //$qp->add_customVars('Name1', 'Value1');
  //$qp->add_customVars('Name2', 'Value2');
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
    echo 'Authorization: ' . $qpstatText["" . $eval['qpstat'] . ""] .'<br />';
    echo "<pre>";
    var_dump($eval);
    echo "</pre>";
  } else {
    // An error occured with the authorize
    echo 'Authorization: ' . $qpstatText["" . $eval['qpstat'] . ""] .'<br />';
    echo "<pre>";
    var_dump($eval);
    echo "</pre>";
  }
} else {
  // Communication error
}

/****************************
*** To capture a transaction: ***/

$eval = false;
try {
  $qp = new quickpay;
  // Set values
  $qp->set_msgtype('1220');
  $qp->set_md5checkword('1u3Y984aF3pfU6EAG8DwcdgLSvCm18B8Z7r7452MbX33VP6l57xy2WHi9QkN412R');
  $qp->set_merchant('87476014');
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
    echo 'Capture: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br/>';
    echo "<pre>";
    var_dump($eval);
    echo "</pre>";
  } else {
    // An error occured with the capture
    echo 'Capture: ' . $qpstatText["" . $eval['qpstat'] . ""] . '<br/>';
    echo "<pre>";
    var_dump($eval);
    echo "</pre>";
  }
} else {
  // Communication error
}


?>
