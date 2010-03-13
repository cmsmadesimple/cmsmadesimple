<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
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
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
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
#END_LICENSE
{
	echo '<div class="pagewarning">'."\n";
	echo '<h3>'.$this->Lang('notice')."</h3>\n";
	$link = '<a target="_blank" href="http://dev.cmsmadesimple.org">forge</a>';
	echo '<p>'.$this->Lang('general_notice',$link,$link)."</p>\n";
	echo '<h3>'.$this->Lang('use_at_your_own_risk')."</h3>\n";
	echo '<p>'.$this->Lang('compatibility_disclaimer')."</p></div>\n";

	$active_tab = -1;
	if( isset($params['active_tab']))
	{
		$active_tab = $params['active_tab'];
	}

	echo $this->Tabs->start_tab_headers();
	if( $this->Permission->check('Modify Modules') )
	{
		echo $this->Tabs->set_tab_header('installed_modules',$this->Lang('installed_modules'),
			$active_tab == 'installed_modules' );
		echo $this->Tabs->set_tab_header('newversions',$this->Lang('newversions'),
			$active_tab == 'newversions' );
		echo $this->Tabs->set_tab_header('modules',$this->Lang('availmodules'),
			$active_tab == 'modules' );
	}
	if( $this->Permission->check('Modify Site Preferences') )
	{
		echo $this->Tabs->set_tab_header('prefs',$this->Lang('preferences'),
			$active_tab == 'prefs' );
	}
	echo $this->Tabs->end_tab_headers();

	echo $this->Tabs->start_tab_content();
	if( $this->Permission->check('Modify Modules') )
	{
		echo $this->Tabs->start_tab('installed_modules');
		include(dirname(__FILE__).'/function.installed_modules.php');
		echo $this->Tabs->end_tab();
		
		echo $this->Tabs->start_tab('newversions');
		include(dirname(__FILE__).'/function.newversionstab.php');
		echo $this->Tabs->end_tab();

		echo $this->Tabs->start_tab('modules');
		$this->_DisplayAdminModulesTab( $id, $params, $returnid );
		echo $this->Tabs->end_tab();
	}
	if( $this->Permission->check('Modify Site Preferences') )
	{
		echo $this->Tabs->start_tab('prefs');
		$this->_DisplayAdminPrefsTab( $id, $params, $returnid );
		echo $this->Tabs->end_tab();
	}
	echo $this->Tabs->end_tab_content();
}
?>