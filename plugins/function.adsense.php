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

function smarty_cms_function_adsense($params, &$smarty) {
	$ad_client = "";
	$ad_width = "120";
	$ad_height = "600";
	$ad_format = "120x600_as";
	$ad_channel = "";

	if(!empty($params['ad_client']))
		$ad_client = $params['ad_client'];
	if(!empty($params['ad_width']))
		$ad_width = $params['ad_width'];
	if(!empty($params['ad_height']))
		$ad_height = $params['ad_height'];
	if(!empty($params['ad_format']))
		$ad_format = $params['ad_format'];
	if(!empty($params['ad_channel']))
		$ad_channel = $params['ad_channel'];
	
	$result = "\n<!-- Begin Google AdSense Ad -->\n";
	$result .= "\n<script type=\"text/javascript\"><!--\n";
	$result .= "google_ad_client = \"$ad_client\";\n";
	$result .= "google_ad_width = \"$ad_width\";\n";
	$result .= "google_ad_height = \"$ad_height\";\n";
	$result .= "google_ad_format = \"$ad_format\";\n";
	$result .= "google_ad_channel = \"$ad_channel\";\n";
	$result .= "//--></script>\n";
	$result .= "<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\"></script>\n";
	$result .= "\n<!-- End Google AdSense Ad -->\n";

	return $result;
}

function smarty_cms_help_function_adsense() {
	?>
	<h3>Testing...</h3>
	<?
}

?>
