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

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;

echo $this->StartTabHeaders();
echo $this->SetTabHeader('categories',$this->Lang('categories'));
echo $this->SetTabHeader('customfields',$this->Lang('customfields'));
echo $this->SetTabHeader('options',$this->Lang('options'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();

echo $this->StartTab('categories', $params);
include(dirname(__FILE__).'/function.admin_categoriestab.php');
echo $this->EndTab();

echo $this->StartTab('customfields', $params);
include(dirname(__FILE__).'/function.admin_customfieldstab.php');
echo $this->EndTab();

echo $this->StartTab('options', $params);
include(dirname(__FILE__).'/function.admin_optionstab.php');
echo $this->EndTab();

echo $this->EndTabContent();

#
# EOF
#