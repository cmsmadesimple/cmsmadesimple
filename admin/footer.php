<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
#
#$Id$

CmsAdminTheme::end();

/*
if (isset($USE_THEME) && $USE_THEME == false)
  {
    echo '<!-- admin theme disabled -->';
  }
else
  {
    $themeObject->DisplayMainDivEnd();
    $themeObject->OutputFooterJavascript();
    $themeObject->DisplayFooter();
  }

if ($gCms->config["debug"] == true)
{
	echo '<div id="_DebugFooter">';
	echo CmsProfiler::get_instance()->report();
	echo '</div> <!-- end DebugFooter -->';
}

?>

</body>
</html>

<?php

#Pull the stuff out of the buffer...
$htmlresult = '';
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
	$htmlresult = @ob_get_contents();
	@ob_end_clean();
}

#Do any header replacements (this is for WYSIWYG stuff)
$footertext = '';
$formtext = '';
$formsubmittext = '';
$bodytext = '';

$userid = get_userid();
$wysiwyg = get_preference($userid, 'wysiwyg');

foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true &&
		$gCms->modules[$key]['object']->IsWYSIWYG()
		)
	{
		$loadit=false;
		if ($gCms->modules[$key]['object']->WYSIWYGActive())
		{
			$loadit=true;
		}
		else
		{
			if (get_preference(get_userid(), 'wysiwyg')==$gCms->modules[$key]['object']->GetName())
			{
				$loadit=true;
			}
		}
		if ($loadit)
		{
			$bodytext.=$gCms->modules[$key]['object']->WYSIWYGGenerateBody();
			$footertext.=$gCms->modules[$key]['object']->WYSIWYGGenerateHeader($htmlresult);
			$formtext.=$gCms->modules[$key]['object']->WYSIWYGPageForm();
			$formsubmittext.=$gCms->modules[$key]['object']->WYSIWYGPageFormSubmit();
		}
	}
}

$htmlresult = str_replace('<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->', $footertext, $htmlresult);
$htmlresult = str_replace('##FORMSUBMITSTUFFGOESHERE##', ' '.$formtext, $htmlresult);
$htmlresult = str_replace('##INLINESUBMITSTUFFGOESHERE##', ' '.$formsubmittext, $htmlresult);
$htmlresult = str_replace('##BODYSUBMITSTUFFGOESHERE##', ' '.$bodytext, $htmlresult);

echo $htmlresult;

var_dump(memory_get_usage());
*/

# vim:ts=4 sw=4 noet
?>
