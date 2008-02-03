<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#$Id: listusertags.php 4287 2007-12-05 18:18:53Z savagekabbage $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$userid = get_userid();

include_once("header.php");

if (FALSE == empty($_GET['message'])) {
    echo $themeObject->ShowMessage(lang($_GET['message']));
}

if(isset($config->params['is_subsite']))
{
	echo lang('multisitedenied');
}
else
{

$multisite_object = new CmsMultisite();
$sites = $multisite_object->find_all($config['root_path'].DIRECTORY_SEPARATOR.'sites');


echo '<div class="pagecontainer">';
echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('multisite');
echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
echo '<thead>';
echo "<tr>\n";
echo "<th>".lang('http_host')."</th>\n";
echo '</thead>';
echo '<tbody>';

$curclass = "row1";

foreach($sites as $site)
{
		echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";
		echo "<td>".$site."</td>\n";
		echo "</tr>\n";

		($curclass=="row1"?$curclass="row2":$curclass="row1");
}

	?>
	</tbody>
</table>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="adduserplugin.php">
				<?php
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addmultisite'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="addmultisite.php">'.lang("addmultisite");
				?>
			</a>
		</p>
	</div>
</div>
</div>

<?php
}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
