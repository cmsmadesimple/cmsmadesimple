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
	$ad_client = "";
	$ad_width = "120";
	$ad_height = "600";
	$ad_format = "120x600_as";
	$ad_channel = "";
	$ad_type = "";
	$color_border = "";
	$color_bg = "";
	$color_link = "";
	$color_url = "";
	$color_text = "";
	
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
	if(!empty($params['ad_type']))
		$ad_type = $params['ad_type'];
	if(!empty($params['color_border']))
		$color_border = $params['color_border'];
	if(!empty($params['color_bg']))
		$color_bg = $params['color_bg'];
	if(!empty($params['color_link']))
		$color_link = $params['color_link'];
	if(!empty($params['color_url']))
		$color_url = $params['color_url'];
	if(!empty($params['color_text']))
		$color_text = $params['color_text'];

	
	//$result = "\n<!-- Begin Google AdSense Ad -->\n";
	$result .= "\n<script type=\"text/javascript\"><!--\n";
	$result .= "google_ad_client = \"$ad_client\";\n";
	$result .= "google_ad_width = \"$ad_width\";\n";
	$result .= "google_ad_height = \"$ad_height\";\n";
	$result .= "google_ad_format = \"$ad_format\";\n";
	$result .= "google_ad_type = \"$ad_type\";\n";
	$result .= "google_ad_channel = \"$ad_channel\";\n";
	$result .= "google_color_border = \"$color_border\";\n";
	$result .= "google_color_bg = \"$color_bg\";\n";
	$result .= "google_color_link = \"$color_link\";\n";
	$result .= "google_color_url = \"$color_url\";\n";
	$result .= "google_color_text = \"$color_text\";\n";
	$result .= "//--></script>\n";
	$result .= "<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\"></script>\n";
	//$result .= "\n<!-- End Google AdSense Ad -->\n";

	return $result;
}

function smarty_cms_help_function_adsense() {
	?>
	<h3>What does this do?</h3>
	<p>Google adsense is a popular advertising program for websites.  This tag will take the basic parameters that would be provided by the adsense program and puts them in a easy to use tag that makes your templates look much cleaner.  See <a href="http://www.google.com/adsense" target="_blank">here</a> for more details on adsense.</p>
	<h3>How do I use it?</h3>
	<p>First, sign up for a google adsense account and get the parameters for your ad.  Then just use the tag in your page/template like so: <code>{adsense ad_client="pub-random#" ad_width="120" ad_height="600" ad_format="120x600_as"}</code>
	<h3>What parameters does it take?</h3>
	<p>All parameters are optional, though skipping one might not necessarily made the ad work right.  Options are:
	<ul>
		<li>ad_client - This would be the pub_random# id that would represent your adsense account number</li>
		<li>ad_width - width of the ad</li>
		<li>ad_height - height of the ad</li>
		<li>ad_format - "format" of the ad <em>e.g. 120x600_as</em></li>
		<li>ad_channel - channels are an advanced feature of adsense.  Put it here if you use it.</li>
		<li>ad_type - possible options are text, image or text_image.</li>
		<li>color_border - the color of the border. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_link - the color of the linktext. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_url - the color of the URL. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_text - the color of the text. Use HEX color or type the color name (Ex. Red)</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_adsense() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	11/11/2005 Hacked by basicus (http://basicus.com) Added the missing configuration options that AdSense provides.
	</p>
	<?php
}

?>