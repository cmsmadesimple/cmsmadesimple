<?php
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

function smarty_cms_function_adsense($params, &$smarty) {
	$parts = array("ad_client","ad_width","ad_height","ad_format","ad_channel",
			"ad_slot","ad_type","color_border","color_bg","color_link","color_url","color_text");
	$result = "\n<script type='text/javascript'><!--\n";
	foreach( $parts as $part ) 
		if( isset( $params[ $part ] ) and !empty( $params[ $part ] ) ) 
			$result .= "google_{$part} = \"".$params[$part]."\";\n";
	return "{$result}//--></script>\n<script type='text/javascript' src='http://pagead2.googlesyndication.com/pagead/show_ads.js'></script>\n";
}

function smarty_cms_help_function_adsense() {
  echo lang('help_function_adsense');
}

function smarty_cms_about_function_adsense() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
       11/22/2009 Mark Giblin: Rewrote function.<br />
       11/22/2009 Rolf Tjassens: Added google_ad_slot and deleted default parameter values<br />
       11/11/2005 Hacked by basicus (http://basicus.com) Added the missing configuration options that AdSense provides.
	</p>
	<?php
}
?>