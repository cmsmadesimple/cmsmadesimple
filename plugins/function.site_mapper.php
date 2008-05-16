<?php
#CMS - CMS Made Simple
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

function smarty_cms_function_site_mapper($params, &$smarty)
{
  $params['module'] = 'MenuManager';
  if( !isset($params['template']) )
    {
      $params['template'] = 'minimal_menu.tpl';
    }
  return cms_module_plugin($params,$smarty);
}

function smarty_cms_help_function_site_mapper() {
	?>
	<h3>What does this do?</h3>
   <p>This is actually just a wrapper tag for the <a href="listmodules.php?action=showmodulehelp&module=MenuManager">Menu Manager module</a> to make the tag syntax easier, and to simplify creating a sitemap. 
	Instead of having to use <code>{cms_module module='MenuManager'}</code> you can now just use <code>{menu}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{site_mapper}</code> on a page or in a template. For help about the Menu Manager module, what parameters it takes etc., please refer to the <a href="listmodules.php?action=showmodulehelp&module=MenuManager">Menu Manager module help</a>.
        <p>By default, if no template option is specified the minimal_menu.tpl file will be used.</p>
	   <p>Any parameters used in the tag are available in the menumanager template as <code>{$menuparams.paramname}</code></p>
	<?php
}


?>
