<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_cms_jquery($params, &$smarty)
{
	$exclude = isset($params['exclude']) && !empty($params['exclude'])?$params['exclude']:'';
	$cdn = isset($params['cdn']) && !empty($params['cdn'])?$params['cdn']:false;
	$append = isset($params['append']) && !empty($params['append'])?$params['append']:'';
	$ssl = isset($params['ssl']) && !empty($params['ssl'])?$params['ssl']:false;
	$custom_root = isset($params['custom_root']) && !empty($params['custom_root'])?$params['custom_root']:'';

ob_start(); ?>
<?php AdminTheme::OutputHeaderJavascript($exclude,$ssl,$cdn,$append,$custom_root); ?>
<?php $out = ob_get_contents(); ob_end_clean();
  if( isset($params['assign']) )
    {
      $smarty->assign(trim($params['assign']),$out);
      return;
    }
  return $out;
}

function smarty_cms_help_function_cms_jquery()
{
	echo lang('help_function_cms_jquery');
}

function smarty_cms_about_function_cms_jquery()
{
	?>
	<p>Author: Tapio L&ouml;ytty &lt;tapsa@blackmilk.fi&gt;</p>
	<p>Version: 0.1</p>
	<p>
	Change History:
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>