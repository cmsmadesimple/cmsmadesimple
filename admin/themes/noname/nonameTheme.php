<?php
#Modern Theme - A theme for CMS Made Simple
#(c)2005 by Alexander Endresen - alexander@endresen.org
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

class nonameTheme extends CmsAdminThemeBase
{
  private $_errors = array();
  private $_messages = array();

  public function ShowErrors($errors,$get_var = '')
  {
    // cache errors for use in the template.
    if( $get_var != '' && isset($_GET[$get_var]) && !empty($_GET[$get_var]) )
      {
	if( is_array($_GET[$get_var]) )
	  {
	    foreach( $_GET[$get_var] as $one )
	      {
		$this->_errors[] = lang(cleanValue($one));
	      }
	  }
	else
	  {
	    $this->_errors[] = lang(cleanValue($_GET[$get_var]));
	  }
      }
    else if( is_array($errors) )
      {
	foreach( $errors as $one )
	  {
	    $this->_errors[] = $one;
	  }
      }
    else if( is_string($errors) )
      {
	$this->_errors[] = $errors;
      }
  }

  public function ShowMessage($message,$get_var='')
  {
    // cache message for use in the template.
    if( $get_var != '' && isset($_GET[$get_var]) && !empty($_GET[$get_var]) )
      {
	if( is_array($_GET[$get_var]) )
	  {
	    foreach( $_GET[$get_var] as $one )
	      {
		$this->_messages[] = lang(cleanValue($one));
	      }
	  }
	else
	  {
	    $this->_messages[] = lang(cleanValue($_GET[$get_var]));
	  }
      }
    else if( is_array($message) )
      {
	foreach( $message as $one )
	  {
	    $this->_messages[] = $one;
	  }
      }
    else if( is_string($message) )
      {
	$this->_messages[] = $message;
      }
  }

  public function do_header() {}
  public function do_footer() {}
  public function do_toppage($section_name)
  {
    debug_display('function not implemented');
  }

  public function postprocess($html)
  {
    $smarty = cmsms()->GetSmarty();
    $otd = $smarty->template_dir;
    $smarty->template_dir = dirname(__FILE__).'/templates';

    $title = $this->get_value('pagetitle');
    if( !$title )
      {
	// no title, get one from the breadcrumbs.
	$bc = $this->get_breadcrumbs();
	if( is_array($bc) && count($bc) )
	  {
	    $title = $bc[count($bc)-1]['title'];
	  }
      }

    $smarty->assign('pagetitle',$title);
    $smarty->assign('content',$html);
    $smarty->assign('config',cmsms()->GetConfig());
    $smarty->assign('theme',$this);
    if( is_array($this->_errors) && count($this->_errors) ) $smarty->assign('errors',$this->_errors);
    if( is_array($this->_messages) && count($this->_messages) ) $smarty->assign('messages',$this->_messages);

    $_contents = $smarty->fetch('pagetemplate.tpl');
    $smarty->template_dir = $otd;
    return $_contents;
  }
}
?>
