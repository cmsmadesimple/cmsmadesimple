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
require_once("../lib/classes/class.template.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

$htmlblob_id = -1;
if (isset($_POST["htmlblob_id"])) $htmlblob_id = $_POST["htmlblob_id"];
else if (isset($_GET["htmlblob_id"])) $htmlblob_id = $_GET["htmlblob_id"];

check_login();
$gCms = cmsms();
$gcbops = $gCms->GetGlobalContentOperations();
$userid = get_userid();
$adminaccess = check_permission($userid, 'Modify Global Content Blocks');
$isowner = $gcbops->CheckOwnership($htmlblob_id,$userid);
$access = $adminaccess || $isowner || $gcbops->CheckAuthorship($htmlblob_id, $userid);

if (isset($_POST["cancel"])) {
  redirect("listhtmlblobs.php".$urlext);
  return;
}

$the_blob = '';
if( $htmlblob_id > 0 )
  {
    $the_blob = $gcbops->LoadHtmlBlobById($htmlblob_id);
  }
else
  {
    $the_blob = new GlobalContent();
  }
$htmlblob = $the_blob->name;
$oldhtmlblob = $the_blob->name;
$owner_id = $the_blob->owner;
$content = $the_blob->content;
$use_wysiwyg = $the_blob->use_wysiwyg;
$description = $the_blob->description;
$ajax = false;
$error = "";

if (isset($_POST['htmlblob'])) $htmlblob = trim($_POST['htmlblob']);
if (isset($_POST['oldhtmlblob'])) $oldhtmlblob = trim($_POST['oldhtmlblob']);
if (isset($_POST['content'])) $content = $_POST['content'];
if (isset($_POST['use_wysiwyg']) ) $use_wysiwyg = (int)$_POST['use_wysiwyg'];
if (isset($_POST['description']) ) $description = trim($_POST['description']);
if (isset($_POST['owner_id'])) $owner_id = $_POST['owner_id'];
if (isset($_POST['ajax']) && $_POST['ajax']) $ajax = true;

$gcb_wysiwyg = (get_site_preference('nogcbwysiwyg','0') == '0') ? 1 : 0;
if( $gcb_wysiwyg )
  {
    $gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);
  }

if ($access)
{
  if (isset($_POST["submit2"]) || isset($_POST['apply']) || $ajax)
	{
		$validinfo = true;
		if ($htmlblob == "")
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
		}
		else if ($htmlblob != $oldhtmlblob && $gcbops->CheckExistingHtmlBlobName($htmlblob, $htmlblob_id))
		{
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}
		else if($content == ""){
		  $error .= '<li>'.lang('nofieldgiven',array('content')).'</li>';
		  $validinfo = false;
		}

		if ($validinfo)
		{
			$the_blob->id = $htmlblob_id;
			$the_blob->use_wysiwyg = $use_wysiwyg;
			$the_blob->description = $description;
			$the_blob->name = $htmlblob;
			$the_blob->content = $content;
			$the_blob->owner = $owner_id;

			$the_blob->ClearAuthors();
			if (isset($_POST["additional_editors"])) {
			  foreach ($_POST["additional_editors"] as $addt_user_id) {
			    $the_blob->AddAuthor($addt_user_id);
			  }
			}

			Events::SendEvent('Core', 'EditGlobalContentPre', array('global_content' => &$the_blob));

			$result = $the_blob->save();

			if ($result)
			{
				// put mention into the admin log
				audit($the_blob->id, 'Global Content Block: '.$the_blob->name, 'Edited');

				#Clear cache
				$smarty = cmsms()->GetSmarty();
				$smarty->clear_all_cache();
				$smarty->clear_compiled_tpl();

				Events::SendEvent('Core', 'EditGlobalContentPost', array('global_content' => &$the_blob));

				if (!isset($_POST['apply'])) {
					redirect('listhtmlblobs.php'.$urlext);
					return;
				}
			}
			else
			{
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}

		if ($ajax)
		{
			header('Content-Type: text/xml');
			print '<?xml version="1.0" encoding="UTF-8"?>';
			print '<EditBlob>';
			if ($error)
			{
				print '<Response>Error</Response>';
				print '<Details><![CDATA[' . $error . ']]></Details>';
			}
			else
			{
				print '<Response>Success</Response>';
				print '<Details><![CDATA[' . lang('edithtmlblobsuccess') . ']]></Details>';
			}
			print '</EditBlob>';
			exit;
		}
	}
}

if (strlen($htmlblob) > 0)
  {
    $CMS_ADMIN_SUBTITLE = $htmlblob;
  }

// Detect if a WYSIWYG is in use, and grab its form submit action (copied from editcotent.php)
$addlScriptSubmit = '';
if( $gcb_wysiwyg && $use_wysiwyg )
  {
    $modobj = cms_utils::get_wysiwyg_module();
    if( $modobj )
      {
	  $addlScriptSubmit .= $modobj->WYSIWYGPageFormSubmit();
      }
  }

$closestr = cms_html_entity_decode(lang('close'));
$headtext = <<<EOSCRIPT
<script type="text/javascript">
  // <![CDATA[
jQuery(document).ready(function(){  
  jQuery('input[name=apply]').click(function(){
	$addlScriptSubmit
	jQuery('#Edit_Blob_Result').html('');

    var data = jQuery('#Edit_Blob').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'ajax', 'value': 1});
    data.push({ 'name': 'apply', 'value': 1 });

    jQuery.post('{$_SERVER['REQUEST_URI']}',data,function(resultdata,text)
	{
	     var resp = jQuery(resultdata).find('Response').text();
	     var details = jQuery(resultdata).find('Details').text();
		 var htmlShow = '';
		 if (resp == 'Success')
		 {
			htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + details + '<\/p><\/div>';
			jQuery('input[name=cancel]').fadeOut();
			jQuery('input[name=cancel]').attr('value','{$closestr}');
			jQuery('input[name=cancel]').fadeIn();
		 }
		 else
		 {
			htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
			htmlShow += details;
			htmlShow += '<\/ul><\/div>';
		 }
		 jQuery('#Edit_Blob_Result').html( htmlShow );	
	},'xml');
	return false;
  });
});
 // ]]>
</script>
EOSCRIPT;

include_once("header.php");

// Holder for AJAX apply result
print '<div id="Edit_Blob_Result"></div>';

$gCms = cmsms();
$db = $gCms->GetDb();


// get the current list of additional users
$query = 'SELECT user_id FROM '.cms_db_prefix().'additional_htmlblob_users WHERE htmlblob_id = ?';
$cur_addt_users = $db->GetArray($query,array($htmlblob_id));
{
  $tmp = array();
  foreach( $cur_addt_users as $one )
    {
      $tmp[] = $one['user_id'];
    }
  $cur_addt_users = $tmp;
}

// Get the list of all users
$query = "SELECT user_id, username FROM ".cms_db_prefix()."users ORDER BY username";
$users = $db->GetArray($query);

// Build the owners list
$owners = "<select name=\"owner_id\">";
foreach( $users as $row )
{
  $owners .= "<option value=\"".$row["user_id"]."\"";
  if ($row["user_id"] == $owner_id)
    {
      $owners .= " selected=\"selected\"";
    }
  $owners .= ">".$row["username"]."</option>";
}
$owners .= "</select>";

// Build the additional users list
$addt_users = "";
$groupops = $gCms->GetGroupOperations();
$groups = $groupops->LoadGroups();
foreach( $groups as $onegroup )
{
  if( $onegroup->id == 1 ) continue;
  $addt_users .= "<option value=\"".($onegroup->id*-1)."\"";
  if( in_array($onegroup->id * -1, $cur_addt_users) )
    {
      $addt_users .= "selected=\"selected\"";
    }
  $addt_users .= '>'.lang('group').":&nbsp;{$onegroup->name}</option>";
}
foreach( $users as $row )
{
  if( $row['user_id'] == 1 ) continue;
  if( $row['user_id'] == $userid ) continue;
  $addt_users .= "<option value=\"".$row["user_id"]."\"";
  if( in_array( $row['user_id'], $cur_addt_users ) )
    {
      $addt_users .= "selected=\"selected\"";
    }
  $addt_users .= ">".$row["username"]."</option>";
}

if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('edithtmlblob')))."</p></div>";
}
else
{
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>


<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('edithtmlblob'); ?>
	<form id="Edit_Blob" method="post" action="edithtmlblob.php">
        <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
			<input type="submit" name="submit2" value="<?php echo lang('submit')?>" class="pagebutton" />
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" />
				<input type="submit" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" />
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" /></p>
		</div>
                
			<?php if (get_site_preference('nogcbwysiwyg','0') == '0') { ?>
			<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('use_wysiwyg') ?>:</p>
				<p class="pagetext"><input type="hidden" name="use_wysiwyg" value="0"/><input type="checkbox" name="use_wysiwyg" onclick="this.form.submit();" value="1" <?php if($use_wysiwyg) echo ' checked="checked"' ?>/></p>
            </div>					
			<?php } ?>      
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinput"><?php echo create_textarea($gcb_wysiwyg && $use_wysiwyg, $content, 'content', 'wysiwyg', 'content');?></p>
		</div>
		<div class="pageoverflow">
		       <p class="pagetext"><?php echo lang('description')?>:</p>
		       <p class="pageinput"><textarea name="description"><?php echo $description ?></textarea></p>
		</div>
               																		               
	<?php if ($adminaccess) { ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('owner')?>:</p>
			<p class="pageinput"><?php echo $owners?></p>
		</div>
	<?php } ?>
        <?php if ($adminaccess || $isowner && ($addt_users != '')) { ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('additionaleditors')?>:</p>
			<p class="pageinput"><select name="additional_editors[]" multiple="multiple" size="6"><?php echo $addt_users?></select></p>
		</div>
	<?php } ?>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="edithtmlblob" value="true" />
				<input type="hidden" name="oldhtmlblob" value="<?php echo $oldhtmlblob; ?>" />
				<input type="hidden" name="htmlblob_id" value="<?php echo $htmlblob_id; ?>" />
				<input type="submit" name="submit2" value="<?php echo lang('submit')?>" class="pagebutton" />
				<?php if (!$adminaccess) { ?>
					<input type="hidden" name="owner_id" value="<?php echo $owner_id ?>" />
				<?php } ?>
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" />
				<input type="submit" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" />
			</p>
		</div>
	</form>
</div>

<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
