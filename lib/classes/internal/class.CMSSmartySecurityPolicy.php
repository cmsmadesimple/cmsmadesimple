<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

/**
 * @package CMS
 */


/**
 * Generic smarty security policy.
 *
 * @since		1.11
 * @package		CMS
 * @internal
 * @ignore
 */
final class CMSSmartySecurityPolicy extends Smarty_Security 
{
  public $php_handling = Smarty::PHP_REMOVE;
  public $static_classes = null;
  public $php_modifiers = array();
  //public $php_modifiers = array('escape','count','preg_replace','lang', 'ucwords','print_r','var_dump','trim','htmlspecialchars','explode','htmlspecialchars_decode','strpos','strrpos','startswith','endswith');
  public $streams = null;
  public $allow_constants = false;
  //public $allow_super_globals = false;
  public $allow_php_tag = false;

  public function __construct($smarty)
  {
    parent::__construct($smarty);
    $config = cmsms()->GetConfig();
    $this->allow_php_tag = $config['use_smarty_php_tags'];
    $this->php_functions = array('isset', 'empty','count', 'sizeof','in_array', 'is_array','time', 'lang',
				 'nl2br','file_exists', 'is_string', 'is_object', 'is_file','print_r','var_dump','htmlspecialchars','htmlspecialchars_decode');
  }
} // end of class

?>
