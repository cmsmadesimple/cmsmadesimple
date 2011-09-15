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

/**
 * This page is both the interface of the CSS editing, and used for actually
 * updating the CSS in the DB. The first part checks that all parameters are
 * valids, and then insert into the DB and redirect.
 *
 * The second part show the form to edit the CSS
 *
 * It takes one argument when called externally :
 * - $css_id : the id of the css to edit
 *
 * @since	0.6
 * @author	calexico
 */


$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$gCms = cmsms();
$styleops = $gCms->GetStylesheetOperations();
$db = $gCms->GetDb();
$dateformat = trim(get_preference(get_userid(),'date_format_string','%x %X')); 
		  if( empty($dateformat) )
		   {
			 $dateformat = '%x %X';
		   }
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
		redirect("listcssassoc.php".$urlext."&type=template&id=" . $templateid);
	else
		redirect("listcss.php".$urlext);
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
		else if ($styleops->CheckExistingStylesheetName($css_name, $css_id))
		{
			$error .= "<li>".lang('cssalreadyused')."</li>";
			$validinfo = false;
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
			//$result = $db->Execute($query,array($css_name, $css_text, $media_type, $db->DBTimeStamp(time()), $css_id));
			
		  $styleops = cmsms()->GetStylesheetOperations();
			
			$onestylesheet = $styleops->LoadStylesheetByID($css_id);
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
			
			Events::SendEvent('Core', 'EditStylesheetPre', array('stylesheet' => &$onestylesheet));
			
			$result = $onestylesheet->Save();

			// Update the css hash
			// deprecated:  this was used by the stylesheet.php function which we no longer distribute
			// as of CMSMS 1.10.
			$config = $gCms->GetConfig();
			$hashfile = cms_join_path($config['root_path'],'tmp','cache','csshash.dat');
			$md5sum = md5($css_text);
			$csshash = csscache_csvfile_to_hash($hashfile);
			$csshash[$css_id] = $md5sum;
			csscache_hash_to_csvfile($hashfile,$csshash);
			
			
			if ($result)
			{
				#Start using new name, just in case this is an apply
				$orig_css_name = $css_name;
				
				Events::SendEvent('Core', 'EditStylesheetPost', array('stylesheet' => &$onestylesheet));

				// put mention into the admin log
				audit($css_id, 'Stylesheet: '.$css_name, 'Changed');

				# we now have to check which templates are associated with this CSS and update their modified date.
				$cssquery = "SELECT assoc_to_id FROM ".cms_db_prefix()."css_assoc
					WHERE	assoc_type		= 'template'
					AND		assoc_css_id	=  ?";
				$cssresult = $db->Execute($cssquery,array($css_id));

				# now updating templates
				while ($cssresult && $line = $cssresult->FetchRow())
				{
					$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$db->DBTimeStamp(time())." 
						WHERE template_id = '".$line["assoc_to_id"]."'";
					$result = $db->Execute($query);

					if (FALSE == $result)
					{
						$error .= "<li>".lang('errorupdatingtemplate')."</li>";
					}
				}
					
				if (!isset($_POST["apply"]))
				{
					if ($from == 'templatecssassoc')
						redirect("listcssassoc.php".$urlext."&type=template&id=" . $templateid);
					else
						redirect("listcss.php".$urlext);
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
		$query = "SELECT * from ".cms_db_prefix()."css WHERE css_id = ?"; 
		$result = $db->Execute($query,array($css_id));

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
$syntaxmodule = get_preference(get_userid(FALSE),'syntaxhighlighter');
if( $syntaxmodule && ($module = ModuleOperations::get_instance()->get_module_instance($syntaxmodule)) )
  {
    if( $module->IsSyntaxHighlighter() && $module->SyntaxActive() )
      {
	$addlScriptSubmit .= $module->SyntaxPageFormSubmit();
      }
  }

$closestr = cms_html_entity_decode(lang('close'));
$headtext = <<<EOSCRIPT
<script type="text/javascript">
// <![CDATA[
jQuery(document).ready(function(){
  jQuery('input[name=apply]').click(function(){
    $addlScriptSubmit
    var data = jQuery('#Edit_CSS').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'ajax', 'value': 1});
    data.push({ 'name': 'apply', 'value': 1 });
    $.post('{$_SERVER['REQUEST_URI']}',data,function(resultdata,text){
	     var resp = $(resultdata).find('Response').text();
	     var details = $(resultdata).find('Details').text();
             var htmlShow = '';
	     if( resp == 'Success' )
	       {
		 htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + details + '<\/p><\/div>';
		 $('input[name=cancel]').fadeOut();
		 $('input[name=cancel]').attr('value','{$closestr}');
		 $('input[name=cancel]').fadeIn();
	       }
             else
               {
		 htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
		 htmlShow += details;
		 htmlShow += '<\/ul><\/div>';
               }
	     $('#Edit_CSS_Result').html(htmlShow);
	   },
	   'xml');
    return false;
  });
});
// ]]>
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
        <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" />
				<input type="submit" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" />
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput">
				<input type="hidden" name="orig_css_name" value="<?php echo $orig_css_name?>" />
				<input type="text" class="name" name="css_name" maxlength="255" value="<?php echo $css_name?>" />				
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinput">
			  <?php echo create_textarea(false, $css_text, 'css_text', 'pagebigtextarea', 'css_text', '', '', '80', '15','','css')?>
			<!--  <textarea class="pagebigtextarea" name="css_text"><_?php echo $css_text?></textarea>-->
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('mediatype')?>:</p>
			<div class="pageinput">
<?php

#open up the list to array


if (!is_array($media_type)) {
  $media_type = explode(", " , $media_type);
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
	    $types .= '<input id="media_type_'.$i.'" name="media_type['.$i.']" type="checkbox" value="'.$onetype.'"';

	    if (is_array($media_type)) {
	      if (in_array($onetype, $media_type) )
		{
		  $types .= ' checked="checked" ';
		}
	    }
	    $types .= " />\n\n";
	    $types .= '<label for="media_type_'.$i.'">'. lang("mediatype_".$onetype) .'</label><br />'."\n";
	  }
	$types .= "</fieldset>";

	echo $types;
?>


			</div>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('last_modified_at')?>:</p>
			<p class="pageinput"><?php echo  strftime( $dateformat , strtotime($lastmodified) )  ?></p>
		</div>		
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="css_id" value="<?php echo $css_id?>" />
				<input type="hidden" name="from" value="<?php echo $from?>" />
				<input type="hidden" name="templateid" value="<?php echo $templateid?>" />
				<input type="hidden" name="editcss" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" />
				<input type="submit" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" />
			</p>
		</div>
	</form>
</div>

<?php

} # end of displaying form

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
