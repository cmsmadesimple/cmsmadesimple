<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
#---------------------------------------------------------------------------

final class AdminSearch_oldmodtemplate_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_oldmodtemplate_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_oldmodtemplate_search');
  }

  public function check_permission()
  {
    return check_permission(get_userid(),'Modify Templates');
  }

  public function get_matches()
  {
    $userid = get_userid();

    $db = cmsms()->GetDb();
    $query = 'SELECT module_name,template_name,content FROM '.CMS_DB_PREFIX.'module_templates WHERE content LIKE ?';
    $dbr = $db->GetArray($query,array('%'.$this->get_text().'%'));
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      foreach( $dbr as $row ) {
	// here we could actually have a smarty template to build the description.
	$pos = strpos($row['content'],$this->get_text());
	if( $pos !== FALSE ) {
	  $start = max(0,$pos - 50);
	  $end = min(strlen($row['content']),$pos+50);
	  $text = substr($row['content'],$start,$end-$start);
	  $text = cms_htmlentities($text);
	  $text = str_replace($this->get_text(),'<span class="search_oneresult">'.$this->get_text().'</span>',$text);
	  $text = str_replace("\r",'',$text);
	  $text = str_replace("\n",'',$text);
	}

	$tmp = array('title'=>"{$row['module_name']} + {$row['template_name']}",'text'=>$text);

	$output[] = $tmp;
      }

      return $output;
    }
    
  }

  public function get_section_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('sectiondesc_oldmodtemplates');
  }

} // end of class

#
# EOF
#