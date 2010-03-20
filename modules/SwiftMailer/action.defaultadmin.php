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

    if( !$this->Permission->check('Modify Site Preferences') )
      return;

    $smarty->assign('endform',
			  $this->Form->form_end());
    $smarty->assign('startform',
			  $this->Form->form_start(array('action' => 'setadminprefs')));

    $maileritems = array();
    $maileritems['mail'] = 'mail';
    $maileritems['sendmail'] = 'sendmail';
    $maileritems['smtp'] = 'smtp';
    $smarty->assign('prompt_mailer', $this->Lang('mailer'));
    $smarty->assign('info_mailer', $this->Lang('info_mailer'));
    $smarty->assign('input_mailer', $this->Form->input_select(array('name' => 'input_mailer')) . $this->Form->input_options(array('items' => $maileritems, 'selected_value' => $this->Preference->get('mailer',-1))) . '</select>');

    $smarty->assign('prompt_host', $this->Lang('host'));
    $smarty->assign('info_host', $this->Lang('info_host'));
    $smarty->assign('input_host', $this->Form->input_text(array('name' => 'input_host', 'value' => $this->Preference->get('host'), 'size' => 50, 'maxlength' => 80)));

    $smarty->assign('prompt_port', $this->Lang('port'));
    $smarty->assign('info_port', $this->Lang('info_port'));
    $smarty->assign('input_port', $this->Form->input_text(array('name' => 'input_port', 'value' => $this->Preference->get('port'), 'size' => 6, 'maxlength' => 8)));

    $smarty->assign('prompt_from', $this->Lang('from'));
    $smarty->assign('info_from', $this->Lang('info_from'));
    $smarty->assign('input_from', $this->Form->input_text(array('name' => 'input_from', 'value' => $this->Preference->get('from'), 'size' => 80, 'maxlength' => 80)));

    $smarty->assign('prompt_fromuser',$this->Lang('fromuser'));
    $smarty->assign('info_fromuser', $this->Lang('info_fromuser'));
    $smarty->assign('input_fromuser', $this->Form->input_text(array('name' => 'input_fromuser', 'value' => $this->Preference->get('fromuser'), 'size' => 50, 'maxlength' => 80)));
			  
    $smarty->assign('prompt_sendmail',$this->Lang('sendmail'));
    $smarty->assign('info_sendmail', $this->Lang('info_sendmail'));
    $smarty->assign('input_sendmail', $this->Form->input_text(array('name' => 'input_sendmail', 'value' => $this->Preference->get('sendmail'), 'size' => 50, 'maxlength' => 255)));

    $smarty->assign('prompt_timeout', $this->Lang('timeout'));
    $smarty->assign('info_timeout', $this->Lang('info_timeout'));
    $smarty->assign('input_timeout', $this->Form->input_text(array('name' => 'input_timeout', 'value' => $this->Preference->get('timeout'), 'size' => 5, 'maxlength' => 5)));

    $smarty->assign('prompt_smtpauth', $this->Lang('smtpauth'));
    $smarty->assign('info_smtpauth', $this->Lang('info_smtpauth'));
    $smarty->assign('input_smtpauth', $this->Form->input_checkbox(array('name' => 'input_smtpauth', 'checked' => $this->Preference->get('smtpauth') == '1')));

    $smarty->assign('prompt_username', $this->Lang('username'));
    $smarty->assign('info_username', $this->Lang('info_username'));
    $smarty->assign('input_username', $this->Form->input_text(array('name' => 'input_username', 'value' => $this->Preference->get('username'), 'size' => 50, 'maxlength' => 255)));

    $smarty->assign('prompt_password', $this->Lang('password'));
    $smarty->assign('info_password', $this->Lang('info_password'));
    $smarty->assign('input_password', $this->Form->input_text(array('name' => 'input_password', 'value' => $this->Preference->get('password'), 'size' => 30, 'maxlength' => 30, 'password' => true)));

    $smarty->assign('prompt_testaddress', 
			  $this->Lang('prompt_testaddress'));
    $smarty->assign('input_testaddress', $this->Form->input_text(array('name' => 'input_testaddress', 'size' => 40, 'maxlength' => 255)));

    $smarty->assign('sendtest', $this->Form->input_submit(array('name' => 'sendtest', 'value' => $this->lang('sendtest'), 'warn_message' => $this->lang('sendtestmailconfirm'))));

    $smarty->assign('prompt_charset',
		    $this->Lang('charset'));
    $smarty->assign('input_charset', $this->Form->input_text(array('name' => 'input_charset', 'value' => $this->Preference->get('charset'), 'size' => 40, 'maxlength' => 40)));

    $smarty->assign('submit', $this->Form->input_submit(array('name' => 'submit', 'value' => $this->lang('submit'), 'warn_message' => $this->lang('settingsconfirm'))));
    $smarty->assign('cancel', $this->Form->input_submit(array('name' => 'cancel', 'value' => $this->lang('cancel'))));

    echo $this->Template->process('prefs.tpl');
?>