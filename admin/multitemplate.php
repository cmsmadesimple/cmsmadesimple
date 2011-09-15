<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

if (isset($_POST['cancel']))
	redirect('listtemplates.php'.$urlext);

check_login();

$action = '';
if (isset($_POST['multiaction'])) $action = $_POST['multiaction'];


$nodelist = array();
$badlist = array();

$templateops = cmsms()->GetTemplateOperations();

if (isset($_POST['idlist']))
{
	foreach (explode(':', $_POST['idlist']) as $id)
	{
		$template =& $templateops->LoadTemplateByID($id);
		$nodelist[] =& $template;
	}
}
else
{
	foreach ($_POST as $k=>$v)
	{
		if (startswith($k, 'multitemplate-'))
		{
			$id = substr($k, strlen('multitemplate-'));
			$template =& $templateops->LoadTemplateByID($id);
			if ($action == 'delete' && $template->UsageCount() > 0)
				$badlist[] =& $template; 
			else
				$nodelist[] =& $template; 
		}
	}
}

include_once("header.php");

if (count($nodelist) == 0 && count($badlist) == 0)
{
	redirect("listtemplates.php".$urlext);
}
else
{
	if ($action == 'delete')
	{
		?>
		<div class="pagecontainer">
			<p class="pageheader"><?php echo lang('deletetemplates') ?></p><br />
		<?php
		$userid = get_userid();
		$access = check_permission($userid, 'Remove Templates');
		if ($access)
		{
			echo '<form method="post" action="multitemplate.php">' . "\n";
			echo "<div>\n";
			echo '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'" />'."\n";
			echo "</div>\n";

			$idlist = array();
			if (count($nodelist) > 0)
			{
				echo '<p>'.lang('templatestodelete').'</p><p>' . "\n";
				foreach ($nodelist as $node)
				{
					echo $node->name . '<br />' . "\n";
					$idlist[] = $node->id;
				}
				echo '</p>';
			}

			if (count($badlist) > 0)
			{
				echo '<p>'.lang('wontdeletetemplateinuse').'</p><p>' . "\n";
				foreach ($badlist as $node)
				{
					echo $node->name . '<br />' . "\n";
				}
				echo '</p>';
			}

			echo '<div class="pageoverflow">
				<p class="pagetext">&nbsp;</p>
				<p class="pageinput">';

			echo '<input type="hidden" name="multiaction" value="dodelete" /><input type="hidden" name="idlist" value="'.implode(':', $idlist).'" />' . "\n";
			?>
								<?php if (count($nodelist) > 0) { ?><input type="submit" name="confirm" value="<?php echo lang('submit') ?>"  class="pagebutton" /><?php } ?>
								<input type="submit" name="cancel" value="<?php echo lang('cancel') ?>" class="pagebutton" />
							</p>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
		else
		{
			redirect('listtemplates.php'.$urlext);
		}
	}
	else if ($action == 'dodelete')
	{
		$userid = get_userid();
		$access = check_permission($userid, 'Remove Templates');
		if ($access)
		{
			foreach ($nodelist as $node)
			{
				$id = $node->id;
				$title = $node->name;
				$node->Delete();
				// put mention into the admin log
				audit($id, 'HTML-template: '.$title, 'Deleted');
			}
		}
		redirect("listtemplates.php".$urlext);
	}
	else if ($action == 'inactive')
	{
		$userid = get_userid();
		$permission = check_permission($userid, 'Modify Templates');

		foreach ($nodelist as $node)
		{
			if ($permission)
			{
				if ($node->active)
				{
					$node->active = false;
					$node->Save();
				}
			}
		}
		redirect("listtemplates.php".$urlext);
	}
	else if ($action == 'active')
	{
		$userid = get_userid();
		$permission = check_permission($userid, 'Modify Templates');

		foreach ($nodelist as $node)
		{
			if ($permission)
			{
				if (!$node->active)
				{
					$node->active = true;
					$node->Save();
				}
			}
		}
		redirect("listtemplates.php".$urlext);
	}
	else
	{
		redirect("listtemplates.php".$urlext);
	}
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
