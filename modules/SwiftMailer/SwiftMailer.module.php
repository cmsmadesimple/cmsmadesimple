<?php
#-------------------------------------------------------------------------
# Module: SwiftMailer - a simple wrapper around swift
# Version: 1.0, Ted Kulp <ted@cmsmadesimple.org>
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


#-------------------------------------------------------------------------
/* Your initial Class declaration. This file's name must
   be "[class's name].module.php", or, in this case,
   Skeleton.module.php
*/

define('SWIFT_PRIORITY_HIGHEST', 1);
define('SWIFT_PRIORITY_HIGH', 2);
define('SWIFT_PRIORITY_NORMAL', 3);
define('SWIFT_PRIORITY_LOW', 4);
define('SWIFT_PRIORITY_LOWEST', 5);

class SwiftMailer extends CmsModuleBase
{
	var $the_mailer;
	
	function __construct()
	{
		parent::__construct();
	}

	/*---------------------------------------------------------
	 GetFriendlyName()
	 ---------------------------------------------------------*/
	function GetFriendlyName()
	{
		return $this->lang('friendlyname');
	}

	/*---------------------------------------------------------
	 GetHelp()
	 ---------------------------------------------------------*/
	function GetHelp()
	{
		return $this->lang('help');
	}


	/*---------------------------------------------------------
	 GetAdminDescription()
	 ---------------------------------------------------------*/
	function GetAdminDescription()
	{
		return $this->lang('moddescription');
	}

	/*---------------------------------------------------------
	 VisibleToAdminUser()
	 ---------------------------------------------------------*/
	function visible_to_admin_user()
	{
		return $this->Permission->check('Modify Site Preferences');
	}

	/*---------------------------------------------------------
	 DoAction($action, $id, $params, $return_id)
	 ---------------------------------------------------------*/
	function do_action($action, $params)
	{
		$gCms = cmsms();
		$smarty = cms_smarty();
		$smarty->assign_by_ref('mod', $this);

		switch ($action)
		{
			case 'default':
			{
				break;
			}
			case 'setadminprefs':
			{
				$this->_SetAdminPrefs($params);
				break;
			}
			default:
			{
				return parent::do_action($action, $params);
			}
		}
	}

	/*---------------------------------------------------------
	 DisplayErrorPage($id, $params, $return_id, $message)
	 NOT PART OF THE MODULE API
	 ---------------------------------------------------------*/
	function _DisplayErrorPage(&$params, $message='')
	{
		$this->smarty->assign('title_error', $this->lang('error'));
		if ($message != '')
		{
			$this->smarty->assign('message', $message);
		}

		// Display the populated template
		echo $this->ProcessTemplate('error.tpl');
	}

	//////////////////////////////////////////////////////////////////////
	//// BEGIN API SECTION
	//////////////////////////////////////////////////////////////////////

	/*---------------------------------------------------------
	 reset()
	 NOT PART OF THE MODULE API
	 ---------------------------------------------------------*/
	function reset($do_load = true)
	{
		if ($do_load)
			$this->_load();
		
		$this->the_mailer = Swift_Message::newInstance();
		
		$charset = $this->Preference->get('charset', '');
		if (!empty($charset))
		{
			$this->set_charset($charset);
		}
		
		$this->set_from($this->Preference->get('from', 'root@localhost'), $this->Preference->get('fromuser', 'CMS Administrator'));
	}


	/*---------------------------------------------------------
	 _SetAdminPrefs($id, $params, $return_id )
	 NOT PART OF THE MODULE API
	 ---------------------------------------------------------*/
	function _SetAdminPrefs(&$params)
	{
		if( !$this->Permission->check('Modify Site Preferences') )
			return;

		set_site_preference('mail_is_set',1);

		if( isset( $params['input_mailer'] ) )
		{
			$this->Preference->set('mailer',$params['input_mailer']);
		}

		if( isset( $params['input_host'] ) )
		{
			$this->Preference->set('host',$params['input_host']);
		}

		if( isset( $params['input_port'] ) )
		{
			$this->Preference->set('port',$params['input_port']);
		}

		if( isset( $params['input_from'] ) )
		{
			$this->Preference->set('from',$params['input_from']);
		}

		if( isset( $params['input_fromuser'] ) )
		{
			$this->Preference->set('fromuser',$params['input_fromuser']);
		}

		if( isset( $params['input_sendmail'] ) )
		{
			$this->Preference->set('sendmail',$params['input_sendmail']);
		}

		if( isset( $params['input_timeout'] ) )
		{
			$this->Preference->set('timeout',$params['input_timeout']);
		}

		if( isset( $params['input_smtpauth'] ) )
		{
			$this->Preference->set('smtpauth',$params['input_smtpauth']);
		}
		else
		{
			$this->Preference->set('smtpauth',0);
		}

		if( isset( $params['input_username'] ) )
		{
			$this->Preference->set('username',$params['input_username']);
		}

		if( isset( $params['input_password'] ) )
		{
			$this->Preference->set('password',$params['input_password']);
		}

		if( isset( $params['input_charset'] ) )
		{
			$this->Preference->set('charset',trim($params['input_charset']));
		}

		$this->reset();

		if( isset( $params['sendtest'] ) )
		{
			// here we're gonna send a nice, hard coded test message
			if( !isset( $params['input_testaddress'] ) || trim($params['input_testaddress']) == '' )
			{
				$this->_DisplayErrorPage($params, $this->lang('error_notestaddress'));
				return;
			}
			else
			{
				$this->add_address($params['input_testaddress']);
				$this->set_body($this->lang('testbody'));
				$this->set_subject($this->lang('testsubject'));
				$this->send();
				$this->reset(); // yes, another reset
			}
		}

		$this->Redirect->module_url(array('action' => 'defaultadmin', 'params' => $params));
	}

	////////////////////////////////////////////////////////////
	//                     UTILITIES                          //
	////////////////////////////////////////////////////////////
	function _load()
	{
		if ($this->the_mailer == NULL)
		{
			require_once(cms_join_path(dirname(__FILE__), 'swift', 'lib', 'swift_required.php'));
			$this->reset(false);
		}
	}

	////////////////////////////////////////////////////////////
	//                     API FUNCTIONS                      //
	////////////////////////////////////////////////////////////

	/* Subject and body related functions */
	function get_subject()
	{
		$this->_load();
		return $this->the_mailer->getSubject();
	}

	function set_subject($txt)
	{
		$this->_load();
		$this->the_mailer->setSubject($txt);
	}
	
	function get_body()
	{
		$this->_load();
		return $this->the_mailer->getBody();
	}

	function set_body( $txt )
	{
		$this->_load();
		$this->the_mailer->setBody($txt);
	}

	function get_alt_body()
	{
		$this->_load();
		return ''; //TODO: Figure this out
	}

	function set_alt_body($txt, $content_type = 'text/plain')
	{
		$this->_load();
		$this->the_mailer->addPart($txt, $content_type);
	}
	
	function get_priority()
	{
		$this->_load();
		return $this->the_mailer->getPriority();
	}

	function set_priority($txt = SWIFT_PRIORITY_HIGH)
	{
		$this->_load();
		$this->the_mailer->setPriority($txt);
	}

	/* Charset, content_type and encoding related functions */
	function get_charset()
	{
		$this->_load();
		return $this->the_mailer->getCharset();
	}

	function set_charset( $txt )
	{
		$this->_load();
		$this->the_mailer->setCharset($txt);
	}

	function get_content_type()
	{
		$this->_load();
		return $this->the_mailer->getContentType();
	}

	function set_content_type( $txt )
	{
		$this->_load();
		$this->the_mailer->setContentType($txt);
	}

	/* 'From' related functions */
	function get_sender()
	{
		$this->_load();
		return $this->the_mailer->getSender();
	}

	function set_sender($txt)
	{
		$this->_load();
		$this->the_mailer->setSender($txt);
	}
	
	function set_from($address, $name = '')
	{
		$this->_load();
		if ($name != '')
			$this->the_mailer->setFrom(array($address => $name));
		else
			$this->the_mailer->setFrom($address);
	}

	function get_return_path()
	{
		$this->_load();
		return $this->the_mailer->getReturnPath();
	}
	
	function set_return_path($address)
	{
		$this->_load();
		$this->the_mailer->setReturnPath($address);
	}

	/* 'To' related functions */
	function add_address($address, $name = '')
	{
		$this->_load();
		$this->the_mailer->addTo($address, $name);
	}

	function add_bcc($addr, $name = '')
	{
		$this->_load();
		$this->the_mailer->addBcc($addr, $name);
	}

	function add_cc($addr, $name = '')
	{
		$this->_load();
		$this->the_mailer->addCc($addr, $name);
	}

	function clear_all_recipients()
	{
		$this->_load();
		$this->ClearAddresses();
		$this->ClearBCCs();
		$this->ClearCCs();
	}
	
	function clear_addresses()
	{
		$this->_load();
		$this->the_mailer->setTo(array());
	}

	function clear_bcc()
	{
		$this->_load();
		$this->the_mailer->setBcc(array());
	}

	function clear_cc()
	{
		$this->_load();
		$this->the_mailer->setCc(array());
	}

	/* Attachment related functions */
	/*
	function add_attachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
	{
		$this->_load();
		return $this->the_mailer->AddAttachment( $path, $name, $encoding, $type );
	}
	*/

	/*
	function add_embedded_image( $path, $cid, $name = '', $encoding = 'base64',
		$type = 'application/octet-stream' )
	{
		$this->_load();
		return $this->the_mailer->AddEmbeddedImage( $path, $cid, 
			$name, $encoding, $type );
	}
	*/

	/*
	function add_string_attachment( $string, $filename, $encoding = 'base64',
		$type = 'application/octet-stream' )
	{
		$this->_load();
		$this->the_mailer->AddStringAttachment( $string, $filename, $encoding, $type );
	}
	*/

	/*
	function clear_attachments()
	{
		$this->_load();
		$this->the_mailer->ClearAttachments();
	}
	*/

	/* Send stuff */
	function send()
	{
		$this->_load();
		
		$mailer_pref = $this->Preference->get('mailer', 'sendmail');
		
		$transport = null;
		if ($mailer_pref == 'sendmail')
		{
			$transport = Swift_SendmailTransport::newInstance($this->Preference->get('sendmail', '/usr/sbin/sendmail') . ' -bs');
		}
		else if ($mailer_pref == 'smtp')
		{
			$transport = Swift_SmtpTransport::newInstance($this->Preference->get('host', 'localhost'), $this->Preference->get('port', 25));
			if ($this->Preference->set('smtpauth', 0) == 1)
			{
				$transport->setUsername($this->Preference->get('username', ''));
				$transport->setPassword($this->Preference->get('password', ''));
			}
		}
		else if ($mailer_pref == 'mail')
		{
			$transport = Swift_MailTransport::newInstance();
		}
		
		if ($transport != null)
		{
			$mailer = Swift_Mailer::newInstance($transport);
			if ($mailer)
			{
				$result = 0;
				
				try
				{
					$result = $mailer->send($this->the_mailer);
				}
				catch (Swift_SwiftException $e)
				{
					trigger_error($e->getMessage());
				}
				
				return $result > 0;
			}
		}
		
		return false;
	}

} // class CMSMailer

?>
