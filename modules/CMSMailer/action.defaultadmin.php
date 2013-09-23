<?php
#-------------------------------------------------------------------------
# Module: CMSMailer - a simple wrapper around phpmailer
# Version: 1.73.10, Robert Campbell <rob@techcom.dyndns.org>
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
if( !isset($gCms) ) exit;

    if( !$this->CheckPermission('Modify Site Preferences') )
      return;

    $this->smarty->assign('endform',
			  $this->CreateFormEnd());
    $this->smarty->assign('startform',
			  $this->CreateFormStart( $id, 'setadminprefs', $returnid ));

    $maileritems = array();
    $maileritems['mail'] = 'mail';
    $maileritems['sendmail'] = 'sendmail';
    $maileritems['smtp'] = 'smtp';
    $this->smarty->assign('prompt_mailer', $this->Lang('mailer'));
    $this->smarty->assign('info_mailer', $this->Lang('info_mailer'));
    $this->smarty->assign('input_mailer',
			  $this->CreateInputDropdown( $id, 'input_mailer',
						      $maileritems, -1,
						      $this->GetPreference('mailer',-1)));

    $this->smarty->assign('prompt_host', $this->Lang('host'));
    $this->smarty->assign('info_host', $this->Lang('info_host'));
    $this->smarty->assign('input_host',
			  $this->CreateInputText( $id, 'input_host',
						  $this->GetPreference('host'),
						  50, 80 ));

    $this->smarty->assign('prompt_port', $this->Lang('port'));
    $this->smarty->assign('info_port', $this->Lang('info_port'));
    $this->smarty->assign('input_port',
			  $this->CreateInputText( $id, 'input_port',
						  $this->GetPreference('port'),
						  6, 8));

    $this->smarty->assign('prompt_from', $this->Lang('from'));
    $this->smarty->assign('info_from', $this->Lang('info_from'));
    $this->smarty->assign('input_from',
			  $this->CreateInputText( $id, 'input_from',
						  $this->GetPreference('from'),
						  80, 80));

    $this->smarty->assign('prompt_fromuser',$this->Lang('fromuser'));
    $this->smarty->assign('info_fromuser', $this->Lang('info_fromuser'));
    $this->smarty->assign('input_fromuser',
			  $this->CreateInputText( $id, 'input_fromuser',
						  $this->GetPreference('fromuser'),
						  50, 80));
			  
    $this->smarty->assign('prompt_sendmail',$this->Lang('sendmail'));
    $this->smarty->assign('info_sendmail', $this->Lang('info_sendmail'));
    $this->smarty->assign('input_sendmail',
			  $this->CreateInputText( $id, 'input_sendmail',
						  $this->GetPreference('sendmail'),
						  50, 255));

    $this->smarty->assign('prompt_timeout', $this->Lang('timeout'));
    $this->smarty->assign('info_timeout', $this->Lang('info_timeout'));
    $this->smarty->assign('input_timeout',
			  $this->CreateInputText( $id, 'input_timeout',
						  $this->GetPreference('timeout'),
						  5, 5));

    $this->smarty->assign('prompt_smtpauth', $this->Lang('smtpauth'));
    $this->smarty->assign('info_smtpauth', $this->Lang('info_smtpauth'));
    $this->smarty->assign('input_smtpauth',
			  $this->CreateInputCheckbox($id, 'input_smtpauth',1,
						     $this->GetPreference('smtpauth')));

    $opts = array();
    $opts[''] = $this->Lang('none');
    $opts['ssl'] = $this->Lang('ssl');
    $opts['tls'] = $this->Lang('tls');
    $opts = array_flip($opts);
    $this->smarty->assign('prompt_secure', $this->Lang('secure'));
    $this->smarty->assign('info_secure', $this->Lang('info_secure'));
    $this->smarty->assign('input_secure',
			  $this->CreateInputDropdown($id,'input_secure',$opts,
						     -1,$this->GetPreference('secure','')));

    $this->smarty->assign('prompt_username', $this->Lang('username'));
    $this->smarty->assign('info_username', $this->Lang('info_username'));
    $this->smarty->assign('input_username',
			  $this->CreateInputText( $id, 'input_username',
						  $this->GetPreference('username'),
						  50, 255));

    $this->smarty->assign('prompt_password', $this->Lang('password'));
    $this->smarty->assign('info_password', $this->Lang('info_password'));
    $this->smarty->assign('input_password', $this->CreateInputPassword( $id, 'input_password', '', 30, 30));

    $this->smarty->assign('prompt_testaddress', 
			  $this->Lang('prompt_testaddress'));
    $this->smarty->assign('input_testaddress',
			  $this->CreateInputText( $id, 'input_testaddress',
						  '',
						  40, 255));
    $this->smarty->assign('sendtest',
			  $this->CreateInputSubmit( $id, 'sendtest',
						    $this->Lang('sendtest'), '', '', $this->Lang('sendtestmailconfirm')));

    $this->smarty->assign('prompt_charset',
		    $this->Lang('charset'));
    $this->smarty->assign('input_charset',
		    $this->CreateInputText($id,'input_charset',
					   $this->GetPreference('charset','utf-8'),
					   40,40));

    $this->smarty->assign('submit',
			  $this->CreateInputSubmit( $id, 'submit', 
						    $this->Lang('submit'), '', '',
						    $this->Lang('settingsconfirm')));

    echo $this->ProcessTemplate('prefs.tpl');
?>
