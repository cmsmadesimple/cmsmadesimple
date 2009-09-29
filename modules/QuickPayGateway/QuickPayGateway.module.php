<?php
#A module for CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

class QuickPayGateway extends CmsModule {
  function GetName() {
    return 'QuickPayGateway';
  }

  function GetFriendlyName() {
    return "QuickPay betalings-modul";
  }

  function IsPluginModule() {
    return true;
  }

  function GetVersion() {
    return '1.0';
  }

  function MinimumCMSVersion() {
    return '1.6';
  }

  function GetAdminDescription() {
    return "QuickPay Betalings modul";
  }

  function HasAdmin() {
    return true;
  }

  function GetAuthor() {
    return 'Morten Poulsen';
  }

  function GetAuthorEmail() {
    return 'morten@poulsen.org';
  }

  function VisibleToAdminUser() {
    return $this->CheckPermission('Modify Site Preferences');
  }

  function QPAuthorize($orderid,$cardnr, $cardmonth, $cardyear, $csc, $amount) {
    include_once(dirname(__FILE__)."/quickpay.php");
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
  }

  function QPCapture($cardnr, $cardmonth, $cardyear, $csc, $amount) {
    include_once(dirname(__FILE__)."/quickpay.php");
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
  }



}

?>
