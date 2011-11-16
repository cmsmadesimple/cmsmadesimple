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
 * This page is responsible for showing the interface to add a new CSS. There is
 * a form that returns back to this page. The content of the form is then
 * checked to verify that all parameters are valid.
 *
 * - If all parameter are valid, the result is stored in the DB
 * - If one or more parameters are not valid, the form is redisplayed. An error
 *   message indictaes to the user what went wrong.
 *
 * There is no GET parameters.
 *
 * @since	0.6
 * @author  calexico
 */

 
$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$gCms = cmsms();
$styleops =& $gCms->GetStylesheetOperations();
$db =& $gCms->GetDb();

#******************************************************************************
# global variables definitions
#******************************************************************************

# this variable is used to store an eventual error message.
$error = "";

#******************************************************************************
# we get the content of the form if there are not empty.
#******************************************************************************

# first ; the content of the css
$css_text = "";
if (isset($_POST["css_text"])) $css_text = $_POST["css_text"];

# then its name
$css_name = "";
if (isset($_POST["css_name"])) $css_name = $_POST["css_name"];

// Now clean up name
$css_name = htmlspecialchars($css_name, ENT_QUOTES);

$media_type = array();
if (isset($_POST['media_type'])) $media_type = $_POST['media_type'];

#******************************************************************************
# if the form was cancelled, we get back to the CSS list
#******************************************************************************
if (isset($_POST["cancel"]))
{
	redirect("listcss.php".$urlext);
	return;
}

#******************************************************************************
# we now check that user has access to add CSS
#******************************************************************************
$userid = get_userid();
$access = check_permission($userid, 'Add Stylesheets');

if ($access)
{

#******************************************************************************
# if the var "addcss" is set, this means that the form has been submitted.
# we check if params are valid
#******************************************************************************
	if (isset($_POST["addcss"]))
	{
		# used to check if we will save the form or not
		$validinfo = true;

		# if no CSS name was given
		if ("" == $css_name)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
			
		}
		# the CSS has a name, we check if it already exists or not
		else 
		{
			$query = "SELECT css_id from ".cms_db_prefix()."css WHERE css_name = " . $db->qstr($css_name);
			$result = $db->Execute($query);

			if ($result && $result->RecordCount() > 0)
			{
				$error .= "<li>".lang('cssalreadyused')."</li>";
				$validinfo = false;
			}
		}

		# now checking the content of the CSS
		if ("" == $css_text)
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
			$validinfo = false;
		}

#******************************************************************************
# everythings seems to be ok, we can try to save the form
#******************************************************************************
		if ($validinfo)
		{
			$newstylesheet = new Stylesheet();
			$newstylesheet->name = $css_name;
			$newstylesheet->value = $css_text;
			$types = "";
                        
			#generate comma separated list
			foreach ($media_type as $onetype) {
                          $types .= "$onetype, ";
                        }
                        if ($types!='') {
			  $types = substr($types, 0, -2); #strip last space and comma
			} else {
			  $types='';
			}

			$newstylesheet->media_type = $types;
			
			Events::SendEvent('Core', 'AddStylesheetPre', array('stylesheet' => &$newstylesheet));

			$result = $newstylesheet->Save();

			# we now have to check that everything went well
			if ($result)
			{
				#Sent the post event
				Events::SendEvent('Core', 'AddStylesheetPost', array('stylesheet' => &$newstylesheet));
				
				# it's ok, we record the operation in the admin log
				audit($newstylesheet->id, 'Stylesheet: '.$css_name, 'Added');

				# and goes back to the css list
				redirect("listcss.php".$urlext);
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertingcss')."</li>";
			}
		}
	}
}

include_once("header.php");

#******************************************************************************
# the user does not have access : error message 
#******************************************************************************
if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addstylesheet')))."</p></div>";
}
#******************************************************************************
# the user has access, we display the form
#******************************************************************************
else
{

	# the user has correct rights, we display error message if any
	if ("" != $error)
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";		
	}

#******************************************************************************
# we now display the content of the form, in HTML
#******************************************************************************
?>


<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('addstylesheet'); ?>
		<form method="post" action="addcss.php">
		<div>
                  <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
                </div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" class="name" name="css_name" maxlength="255" value="<?php echo $css_name?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinput">
			<?php echo create_textarea(false, $css_text, 'css_text', 'pagebigtextarea', 'css_text', '', '', '80', '15','','css')?>
			<!-- <textarea class="pagebigtextarea" name="css_text" cols="" rows=""><_?php echo $css_text?></textarea>  -->
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('mediatype')?>:</p>
			<div class="pageinput">
<?php
		   $existingtypes = array("all",
					  "aural",
					  "speech",
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
        $types .= "<legend>Media type</legend>\n";
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
            $types .= '<label for="media_type_'.$i.'">'. lang("mediatype_".$onetype) ."</label>\n<br />";

          }
        $types .= "</fieldset>";

        echo $types;
?>


			</div>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="addcss" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" />
			</p>
		</div>
		</form>
</div>

<?php
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
} # end of displaying the form
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
