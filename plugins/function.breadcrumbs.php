<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_cms_function_breadcrumbs($params, &$smarty)
{
echo '<span style="font-weight: bold; color: #f00;">WARNING:<br />The &#123breadcrumbs&#125 tag is removed from CMSMS Core<br />Instead, now use in your HTML template: &#123nav_breadcrumbs&#125 !</span>';

// put mention into the admin log
audit('', '&#123breadcrumbs&#125 tag', 'is removed from CMSMS Core. Instead, now use in your HTML template: &#123nav_breadcrumbs&#125 !');
}
?>