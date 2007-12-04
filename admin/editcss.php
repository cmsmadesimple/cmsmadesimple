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
#

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

if (isset($_POST["cancel"]))
{
	redirect("listcss.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'editcss.php');

$contentops = $gCms->GetContentOperations();
$stylesheetops = $gCms->GetStylesheetOperations();

#Make sure we're logged in and get that user id
check_login();
$userid = get_userid();
$access = check_permission($userid, 'Add Stylesheets');

require_once("header.php");

$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

function &get_stylesheet_object($stylesheet_id)
{
	$stylesheet_object = cmsms()->cms_stylesheet->find_by_id($stylesheet_id);
	if (isset($_REQUEST['stylesheet']))
		$stylesheet_object->update_parameters($_REQUEST['stylesheet']);	
	return $stylesheet_object;
}
		//Get the css_id
		$stylesheet_id = coalesce_key($_REQUEST, 'css_id', '-1');

		//Get a working page object
		$stylesheet_object = get_stylesheet_object($stylesheet_id);

// Create the media_types array
$media_types = array(
	array('name' => "all"),
	array('name' => "aural"),
	array('name' => "braille"),
	array('name' => "embossed"),
	array('name' => "handheld"),
	array('name' => "print"),
	array('name' => "projection"),
	array('name' => "screen"),
	array('name' => "tty"),
	array('name' => "tv")
);

if ($access)
{
	if ($submit || $apply)
	{
		// Handle the media types
		$submitted_types = $_POST['media_types'];
		foreach ($submitted_types as $key => $onetype) 
		{
			if($onetype != 0)
			{	
				$types .= $media_types[$key]['name'] . ', ';			
				$media_types[$key]['selected'] = true;
			}
		}
		if ($types!='') 
		{
			$types = substr($types, 0, -2); #strip last space and comma
		} 
		else 
		{
			$types='';
		}

		$stylesheet_object->media_type = $types;	
		
		if ($stylesheet_object->save())
		{
			audit($stylesheet_object->id, $stylesheet_object->name, 'Edited Stylesheet');
			if ($submit)
			{
				redirect("listcss.php");
			}
		}
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('editstylesheet'));

//Assign the stylesheet media types
$smarty->assign('media_types', $media_types);

//Setup the stylesheet object
$smarty->assign_by_ref('stylesheet_object', $stylesheet_object);

//Can we apply?
$smarty->assign('can_apply', true);

$smarty->display('addcss.tpl');

include_once("footer.php");
?>







<?php
/* ==== OLD STUFF ====

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

global $gCms;
$styleops =& $gCms->GetStylesheetOperations();
$db =& $gCms->GetDb();

$dateformat = get_preference(get_userid(),'date_format_string','%x %X');

#******************************************************************************
# Definition of global vars
#******************************************************************************

# this var is used to store any error that may occur
$error = "";

#******************************************************************************
# we get the parameters
#******************************************************************************

# the new name of the css
$css_name = "";
if (isset($_POST["css_name"])) $css_name = $_POST["css_name"];

$from = "";
if (isset($_REQUEST["from"])) $from = $_REQUEST["from"];

$templateid = "";
if (isset($_REQUEST["templateid"])) $templateid = $_REQUEST["templateid"];

# the old name of the css (if it has changed, we'll have to check that the new
# one is not already used.&
$orig_css_name = "";
if (isset($_POST["orig_css_name"])) $orig_css_name = $_POST["orig_css_name"];

# the content of the CSS
$css_text = "";
if (isset($_POST["css_text"])) $css_text = $_POST["css_text"];

// Clean up name
$css_name = htmlspecialchars($css_name, ENT_QUOTES);

# the ID of the CSS
$css_id = -1;
if (isset($_POST["css_id"])) $css_id = $_POST["css_id"];
else if (isset($_GET["css_id"])) $css_id = $_GET["css_id"];

$media_type = array();
if (isset($_POST['media_type'])) $media_type = $_POST['media_type'];

$ajax = false;
if (isset($_POST['ajax']) && $_POST['ajax']) $ajax = true;

# if the form has beeen cancelled, we redirect
if (isset($_POST["cancel"]))
{
	if ($from == 'templatecssassoc')
		redirect("listcssassoc.php?type=template&id=" . $templateid);
	else
		redirect("listcss.php");
	return;
}

#******************************************************************************
# first, checking the user's permission
#******************************************************************************
$userid = get_userid();
$access = check_permission($userid, 'Modify Stylesheets');

if ($access)
{

	# the user has submitted the form
	if (isset($_POST["editcss"]))
	{
		$validinfo = true;

		# check if the name is valid
		if ("" == $css_name)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
		}

		# then check if new name is in use or not
		else if ($css_name != $orig_css_name)
		{
			$query = "SELECT id from ".cms_db_prefix()."css WHERE css_name = " . cms_db()->qstr($css_name);
			$result = cms_db()->Execute($query);

			if ($result && $result->RecordCount() > 0)
			{
				$error .= "<li>".lang('cssalreadyused')."</li>";
				$validinfo = false;
			}
		}

		# then check if css has content
		if ("" == $css_text)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
			$validinfo = false;
		}

#******************************************************************************
# everything looks ok, we can update the CSS
#******************************************************************************
		if ($validinfo)
		{
			//$query = "UPDATE ".cms_db_prefix()."css SET css_name = ?, css_text = ?, media_type = ?, modified_date = ? WHERE css_id = ?";
			//$result = cms_db()->Execute($query,array($css_name, $css_text, $media_type, cms_db()->DBTimeStamp(time()), $css_id));
			
			global $gCms;
			$styleops =& $gCms->GetStylesheetOperations();
			
			$onestylesheet = $styleops->load_stylesheet_by_id($css_id);
			$onestylesheet->name = $css_name;
			$onestylesheet->value = $css_text;

                        #generate comma seperated list from media types
                        $types = "";
                        foreach ($media_type as $onetype) {
                          $types .= "$onetype, ";
                        }
                        if ($types!='') {
                        $types = substr($types, 0, -2); #strip last space and comma
                        } else {
                        $types='';
                        }
			$onestylesheet->media_type = $types;
			
			//Events::SendEvent('Core', 'EditStylesheetPre', array('stylesheet' => &$onestylesheet));
			
			$result = $onestylesheet->save();

			if ($result)
			{
				#Start using new name, just in case this is an apply
				$orig_css_name = $css_name;
				
				//Events::SendEvent('Core', 'EditStylesheetPost', array('stylesheet' => &$onestylesheet));

				audit($css_id, $css_name, 'Edited CSS');

				# we now have to check which templates are associated with this CSS and update their modified date.
				$cssquery = "SELECT assoc_to_id FROM ".cms_db_prefix()."css_assoc
					WHERE	assoc_type		= 'template'
					AND		assoc_css_id	=  ?";
				$cssresult = cms_db()->Execute($cssquery,array($css_id));

				# now updating templates
				while ($cssresult && $line = $cssresult->FetchRow())
				{
					$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".cms_db()->DBTimeStamp(time())." 
						WHERE template_id = '".$line["assoc_to_id"]."'";
					$result = cms_db()->Execute($query);

					if (FALSE == $result)
					{
						$error .= "<li>".lang('errorupdatingtemplate')."</li>";
					}
				}
					
				if (!isset($_POST["apply"]))
				{
					if ($from == 'templatecssassoc')
						redirect("listcssassoc.php?type=template&id=" . $templateid);
					else
						redirect("listcss.php");
					return;
				}
			}
			else
			{
				$error .= "<li>".lang('errorupdatingcss')."</li>";
			}
		} # end of updating
	} # end of the user has submitted

	if ($ajax)
	{
		header('Content-Type: text/xml');
		print '<?xml version="1.0" encoding="UTF-8"?>';
		print '<EditCSS>';
		if ($error)
		{
			print '<Response>Error</Response>';
			print '<Details><![CDATA[' . $error . ']]></Details>';
		}
		else
		{
			print '<Response>Success</Response>';
			print '<Details><![CDATA[' . lang('editcsssuccess') . ']]></Details>';
		}
		print '</EditCSS>';
		exit;
	}
	
	# we've been called with a css id, we get it to show it on the form
	else if (-1 != $css_id)
	{

		# we get the CSS in the DB
		$query = "SELECT * from ".cms_db_prefix()."css WHERE id = ?"; 
		$result = cms_db()->Execute($query,array($css_id));

		# we put the content in vars
		if ($result && $result->RecordCount() > 0)
		{
			$row = $result->FetchRow();
			$css_name		= $row["css_name"];
			$orig_css_name	= $row["css_name"];
			$css_text		= $row["css_text"];
			$media_type		= $row["media_type"];
			$lastmodified		= $row["modified_date"];
		}
		else
		{
			$error .= "<li>".lang('errorretrievingcss')."</li>";
		}
	} # end of getting css
} # end of has access

if (strlen($css_name) > 0)
    {
    $CMS_ADMIN_SUBTITLE = $css_name;
    }
if (isset($_POST["apply"]))
    {
    	$CMS_EXCLUDE_FROM_RECENT=1;
    }

$addlScriptSubmit = '';
foreach (array_keys($gCms->modules) as $moduleKey)
{
	$module =& $gCms->modules[$moduleKey];
	if (!($module['installed'] && $module['active'] && $module['object']->IsSyntaxHighlighter()))
	{
		continue;
	}

	if ($module['object']->SyntaxActive() or get_preference(get_userid(), 'syntaxhighlighter') == $module['object']->GetName())
	{
		$addlScriptSubmit .= $module['object']->SyntaxPageFormSubmit();
	}
}

$headtext = <<<EOSCRIPT
<script type="text/javascript">
window.Edit_CSS_Apply = function(button)
{
	$addlScriptSubmit
	$('Edit_CSS_Result').innerHTML = '';
	button.disabled = 'disabled';

	var data = new Array();
	data.push('ajax=1');
	data.push('apply=1');
	var elements = Form.getElements($('Edit_CSS'));
	for (var cnt = 0; cnt < elements.length; cnt++)
	{
		var elem = elements[cnt];
		if (elem.type == 'submit')
		{
			continue;
		}
		var query = Form.Element.serialize(elem);
		data.push(query);
	}

	new Ajax.Request(
		'{$_SERVER['REQUEST_URI']}'
		, {
			method: 'post'
			, parameters: data.join('&')
			, onSuccess: function(t)
			{
				button.removeAttribute('disabled');
				var response = t.responseXML.documentElement.childNodes[0];
				var details = t.responseXML.documentElement.childNodes[1];
				if (response.textContent) { response = response.textContent; } else { response = response.text; } 
				if (details.textContent) { details = details.textContent; } else { details = details.text; }

				var htmlShow = '';
				if (response == 'Success')
				{
					htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + details + '</p></div>';
				}
				else
				{
					htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">' + details + '</div>';
				}
				$('Edit_CSS_Result').innerHTML = htmlShow;
			}
			, onFailure: function(t)
			{
				alert('Could not save: ' + t.status + ' -- ' + t.statusText);
			}
		}
	);
	return false;
}
</script>
EOSCRIPT;

include_once("header.php");

// AJAX result container
print '<div id="Edit_CSS_Result"></div>';

# if the user has no acess, we display an error
if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('editcss')))."</p></div>";
}

# else, we can display the form
else
{
	# first displaying erros if any
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('editstylesheet'); ?>
	<form id="Edit_CSS" method="post" action="editcss.php">
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" onclick="return window.Edit_CSS_Apply(this);" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('name')?>:</p>
			<p class="pageinput">
				<input type="hidden" name="orig_css_name" value="<?php echo $orig_css_name?>" />
				<input type="text" class="name" name="css_name" maxlength="255" value="<?php echo $css_name?>" />				
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('content')?>:</p>
			<p class="pageinput">
			  <?php echo create_textarea(false, $css_text, 'css_text', 'pagebigtextarea', 'css_text', '', '', '80', '15','','css')?>
			<!--  <textarea class="pagebigtextarea" name="css_text"><_?php echo $css_text?></textarea>-->
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('mediatype')?>:</p>
			<p class="pageinput">
<?php
/*
#open up the list to array


if (!is_array($media_type)) {
  $media_type = split (", " , $media_type);
}

$existingtypes = array("all", 
		       "aural", 
		       "braille", 
		       "embossed", 
		       "handheld", 
		       "print", 
		       "projection", 
		       "screen", 
		       "tty", 
		       "tv"
		       );

	$types = "";
	$types .= "<fieldset style=\"width:60em;\">\n";
	$types .= "<legend>Media type</legend>\n\n";
	$i = 0;
	foreach ($existingtypes as $onetype)
	  {
	    $i++;
	    $types .= '<input name="media_type['.$i.']" type="checkbox" value="'.$onetype.'"';

	    if (is_array($media_type)) {
	      if (in_array($onetype, $media_type) )
		{
		  $types .= ' checked="checked" ';
		}
	    }
	    $types .= " />\n\n";
	    $types .= '<label for="media_type">'. lang("mediatype_".$onetype) .'</label><br />'."\n";
	  }
	$types .= "</fieldset>";

	echo $types;
?>


			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('last_modified_at')?>:</p>
			<p class="pageinput"><?php echo  strftime( get_preference(get_userid(),'date_format_string','%x %X') , strtotime($lastmodified) )  ?></p>
		</div>		
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="css_id" value="<?php echo $css_id?>" />
				<input type="hidden" name="from" value="<?php echo $from?>" />
				<input type="hidden" name="templateid" value="<?php echo $templateid?>" />
				<input type="hidden" name="editcss" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" onclick="return window.Edit_CSS_Apply(this);" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>

<?php

} # end of displaying form

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
*/
?>
