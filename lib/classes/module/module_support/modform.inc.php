<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

/**
 * Methods for modules to do form related functions
 *
 * @since		1.0
 * @package		CMS
 */

function cms_module_CreateFormStart(&$modinstance, $id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='', $html_id = '', $use_current_page_as_action = false)
{
	global $gCms;

	$formcount = 1;
	$variables = &$gCms->variables;

	if (isset($variables['formcount']))
		$formcount = $variables['formcount'];

	$id = cms_htmlentities($id);
	$action = cms_htmlentities($action);
	$returnid = cms_htmlentities($returnid);
	$method = cms_htmlentities($method);
	$enctype = cms_htmlentities($enctype);
	$idsuffix = cms_htmlentities($idsuffix);
	$extra = cms_htmlentities($extra);

	if ($html_id == '')
		$html_id = $id.$action.$idsuffix;

	if ($returnid == null)
		$returnid = '';

	$goto = '';
	if ($use_current_page_as_action)
		$goto = CmsRequest::get_requested_uri();
	else
		$goto = ($returnid==''?'moduleinterface.php':'index.php');

	$text = '<form id="'.$html_id.'" name="'.$id.$action.$idsuffix.'" method="'.$method.'" action="'.$goto.'"';
	if ($enctype != '')
	{
		$text .= ' enctype="'.$enctype.'"';
	}
	if ($extra != '')
	{
		$text .= ' '.$extra;
	}

	if ($use_current_page_as_action)
		$text .= '><div class="hidden">';
	else
		$text .= '><div class="hidden"><input type="hidden" name="mact" value="'.$modinstance->get_name().','.$id.','.$action.','.($inline == true?1:0).'" />';
		
		
	if ($returnid != '')
	{
		$text .= '<input type="hidden" name="'.$id.'returnid" value="'.$returnid.'" />';
		if ($inline)
		{
			$text .= '<input type="hidden" name="'.$modinstance->cms->config['query_var'].'" value="'.$returnid.'" />';
		}
	}
	foreach ($params as $key=>$value)
	{
	  $value = cms_htmlentities($value);
	  if ($key != 'module' && $key != 'action' && $key != 'id')
	    $text .= '<input type="hidden" name="'.$id.$key.'" value="'.$value.'" />';
	}
	$text .= "</div>\n";

	$formcount = $formcount + 1;
	$variables['formcount'] = $formcount;

	return $text;
}

function cms_module_CreateLabelForInput(&$modinstance, $id, $name, $labeltext='', $addttext='', $html_id = '')
{
	$labeltext = cms_htmlentities($labeltext);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);
	
	$text = '<label for="' . $html_id . '"';
	if ($addttext != '')
	{
		$text .= ' ' . $addttext;
	}
	$text .= '>'.$labeltext.'</label>'."\n";
	return $text;
}

function cms_module_CreateInputText(&$modinstance, $id, $name, $value='', $size='10', $maxlength='255', $addttext='', $html_id = '')
{
	$value = cms_htmlentities($value);
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$size = cms_htmlentities($size);
	$maxlength = cms_htmlentities($maxlength);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$value = str_replace('"', '&quot;', $value);
	$text = '<input type="text" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
	if ($addttext != '')
	{
		$text .= ' ' . $addttext;
	}
	$text .= " />\n";
	return $text;
}

function cms_module_CreateInputTextWithLabel(&$modinstance, $id, $name, $value='', $size='10', $maxlength='255', $addttext='', $label='', $labeladdtext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$value = cms_htmlentities($value);
	$size = cms_htmlentities($size);
	$maxlength = cms_htmlentities($maxlength);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	if ($label == '')
	{
		$label = $name;
	}
	$text = '<label for="'.$id . $name.'" id="'.$html_id.'" '.$labeladdtext.'>'.$label.'</label>'."\n";
	$text .= $modinstance->CreateInputText($id, $name, $value, $size, $maxlength, $addttext, $html_id);
	$text .= "\n";
	return $text;
}

function cms_module_CreateInputFile(&$modinstance, $id, $name, $accept='', $size='10',$addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$accept = cms_htmlentities($accept);
	$size = cms_htmlentities($size);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text='<input type="file" name="'.$id.$name.'" id="'.$html_id.'" size="'.$size.'"';
	if ($accept != '')
	{
		$text .= ' accept="' . $accept.'"';
	}
	if ($addttext != '')
	{
		$text .= ' ' . $addttext;
	}
	$text .= " />\n";
	return $text;
}

function cms_module_CreateInputPassword(&$modinstance, $id, $name, $value='', $size='10', $maxlength='255', $addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$value = cms_htmlentities($value);
	$size = cms_htmlentities($size);
	$maxlength = cms_htmlentities($maxlength);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$value = str_replace('"', '&quot;', $value);
	$text = '<input type="password" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
	if ($addttext != '')
	{
		$text .= ' ' . $addttext;
	}
	$text .= " />\n";
	return $text;
}

function cms_module_CreateInputHidden(&$modinstance, $id, $name, $value='', $addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$value = cms_htmlentities($value);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$value = str_replace('"', '&quot;', $value);
	$text = '<input type="hidden" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'"';
	if ($addttext != '')
	{
		$text .= ' '.$addttext;
	}
	$text .= " />\n";
	return $text;
}

function cms_module_CreateInputCheckbox(&$modinstance, $id, $name, $selected = false, $addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<input type="checkbox" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'"';
	if ($selectedvalue == $value)
	{
		$text .= ' checked="checked"';
	}
	if ($addttext != '')
	{
		$text .= ' '.$addttext;
	}
	$text .= " />\n";
	return $text;
}

function cms_module_CreateInputSubmit(&$modinstance, $id, $name, $value='', $addttext='', $image='', $confirmtext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$image = cms_htmlentities($image);

	global $gCms;
	$config =& $gCms->GetConfig();
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<input name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'" type=';

	if ($image != '')
	{
		$text .= '"image"';
		$img = $config['root_url'] . '/' . $image;
		$text .= ' src="'.$img.'"';
	}
	else
	{
		$text .= '"submit"';
	}
	if ($confirmtext != '' )
	  {
		$text .= 'onclick="return confirm(\''.$confirmtext.'\');"';
	  }
	if ($addttext != '')
	{
		$text .= ' '.$addttext;
	}

	$text .= ' />';

	return $text . "\n";
}

function cms_module_CreateInputReset(&$modinstance, $id, $name, $value='Reset', $addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$value = cms_htmlentities($value);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<input type="reset" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'"';
	if ($addttext != '')
	{
		$text .= ' '.$addttext;
	}
	$text .= ' />';
	return $text . "\n";
}

function cms_module_CreateFileUploadInput(&$modinstance, $id, $name, $addttext='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<input type="file" id="'.$html_id.'" name="'.$id.$name.'"';
	if ($addttext != '')
	{
		$text .= ' '.$addttext;
	}
	$text .= ' />';
	return $text . "\n";
}

function cms_module_create_input_dropdown(&$modinstance, $id, $name, $items, $selectedindex, $selectedvalue, $addttext, $flip_array, $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$selectedindex = cms_htmlentities($selectedindex);
	$selectedvalue = cms_htmlentities($selectedvalue);
	
	if ($flip_array)
	{
		$items = @array_flip($items);
	}
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<select name="'.$id.$name.'" id="'.$html_id.'"';
	if ($addttext != '')
	{
		$text .= " $addttext";
	}
	$text .= '>';
	$count = 0;
	if (is_array($items) && count($items) > 0)
	{
		foreach ($items as $key=>$value)
		{
			$text .= '<option value="'.$key.'"';
			if ($selectedindex == $count || $selectedvalue == $key)
			{
				$text .= ' selected="selected"';
			}
			$text .= ">$value</option>";
			$count++;
		}
	}
	$text .= "</select>\n";

	return $text;
}

function cms_module_CreateInputSelectList(&$modinstance, $id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true, $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$value = cms_htmlentities($value);
	$size = cms_htmlentities($size);
	$multiple = cms_htmlentities($multiple);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<select name="'.$id.$name.'" id="'.$html_id.'"';
	if ($addttext != '')
	{
		$text .= ' ' . $addttext;
	}
	if( $multiple )
	  {
		$text .= ' multiple="multiple" ';
	  }
	$text .= 'size="'.$size.'">';
	$count = 0;
	foreach ($items as $key=>$value)
	{
	  $value = cms_htmlentities($value);

		$text .= '<option value="'.$value.'"';
		if (in_array($value, $selecteditems))
		{
			$text .= ' ' . 'selected="selected"';
		}
		$text .= '>';
		$text .= $key;
		$text .= '</option>';
		$count++;
	}
	$text .= '</select>'."\n";

	return $text;
}

function cms_module_CreateInputRadioGroup(&$modinstance, $id, $name, $items, $selectedvalue='', $addttext='', $delimiter='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$selectedvalue = cms_htmlentities($selectedvalue);
	$delimiter = cms_htmlentities($delimiter);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '';
	$counter = 0;
	foreach ($items as $key=>$value)
	{
	  $value = cms_htmlentities($value);

		$counter = $counter + 1;
		$text .= '<input type="radio" name="'.$id.$name.'" id="'.$html_id.'" value="'.$value.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		if ($selectedvalue == $value)
		{
			$text .= ' ' . 'checked="checked"';
		}
		$text .= ' />';
		$text .= '<label for="'.$id.$name.$counter.'">'.$key .'</label>' . $delimiter;
	}

	return $text;
}

function cms_module_CreateLink(&$modinstance, $id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='')
{
	$id = cms_htmlentities($id);
	$action = cms_htmlentities($action);
	$returnid = cms_htmlentities($returnid);
	$inline = cms_htmlentities($inline);
	$prettyurl = cms_htmlentities($prettyurl);

	global $gCms;
	$config =& $gCms->GetConfig();

	$class = (isset($params['class'])?cms_htmlentities($params['class']):'');

	if ($prettyurl != '' && $config['assume_mod_rewrite'] == true && $config['use_hierarchy'] == true)
	{
		$text = $config['root_url'] . '/' . $prettyurl . $config['page_extension'];
	}
	else if ($prettyurl != '' && $config['internal_pretty_urls'] == true && $config['use_hierarchy'] == true)
	{
		$text = $config['root_url'] . '/index.php/' . $prettyurl . $config['page_extension'];
	}
	else
	{
		$text = '';
		if ($targetcontentonly || ($returnid != '' && !$inline))
		{
			$id = 'cntnt01';
		}
		$goto = 'index.php';
		if ($returnid == '')
		{
			$goto = 'moduleinterface.php';
		}
		if (!$onlyhref)
		{
		}
		$text .= $config['root_url'];
		if (!($returnid != '' && $returnid > -1))
		{
			$text .= '/'.$config['admin_dir'];
		}

		#$text .= '/'.$goto.'?module='.$modinstance->get_name().'&amp;id='.$id.'&amp;'.$id.'action='.$action;
		$text .= '/'.$goto.'?mact='.$modinstance->get_name().','.$id.','.$action.','.($inline == true?1:0);

		foreach ($params as $key=>$value)
		{
		  $key = cms_htmlentities($key);
		  $value = cms_htmlentities($value);
			if ($key != 'module' && $key != 'action' && $key != 'id')
				$text .= '&amp;'.$id.$key.'='.rawurlencode($value);
		}
		if ($returnid != '')
		{
			$text .= '&amp;'.$id.'returnid='.$returnid;
			if ($inline)
			{
				$text .= '&amp;'.$config['query_var'].'='.$returnid;
			}
		}
	}

	if (!$onlyhref)
	{
		$beginning = '<a';
		if ($class != '')
		{
			$beginning .= ' class="'.$class.'"';
		}
		$beginning .= ' href="';
		$text = $beginning . $text . "\"";
		if ($warn_message != '')
		{
			$text .= ' onclick="return confirm(\''.$warn_message.'\');"';
		}

		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}

		$text .= '>'.$contents.'</a>';
	}
	return $text;
}

function cms_module_CreateContentLink(&$modinstance, $pageid, $contents='')
{
	$pageid = cms_htmlentities($pageid);
	$contents = cms_htmlentities($contents);

	global $gCms;
	$config = &$gCms->GetConfig();
	$text = '<a href="';
	if ($config["assume_mod_rewrite"])
	{
		# mod_rewrite
		$contentops =& $gCms->GetContentOperations();
		$alias = $contentops->GetPageAliasFromID( $pageid );
		if( $alias == false )
		{
			return '<!-- ERROR: could not get an alias for pageid='.$pageid.'-->';
		}
		else
		{
			$text .= $config["root_url"]."/".$alias.
			(isset($config['page_extension'])?$config['page_extension']:'.shtml');
		}
	}
	else
	{
		# mod rewrite
		$text .= $config["root_url"]."/index.php?".$config["query_var"]."=".$pageid;
	}
	$text .= '">'.$contents.'</a>';
	return $text;
}

function cms_module_CreateReturnLink(&$modinstance, $id, $returnid, $contents='', $params=array(), $onlyhref=false)
{
	$id = cms_htmlentities($id);
	$returnid = cms_htmlentities($returnid);
	$contents = $contents;

	$text = '';
	global $gCms;
	$config = &$gCms->GetConfig();
	$manager =& $gCms->GetHierarchyManager();
	$node =& $manager->sureGetNodeById($returnid);
	if (isset($node))
	{
		$content =& $node->GetContent();

		if (isset($content))
		{
			if ($content->GetURL() != '')
			{
				if (!$onlyhref)
				{
					$text .= '<a href="';
				}
				$text .= $content->GetURL();

				$count = 0;
				foreach ($params as $key=>$value)
				{
				  $key = cms_htmlentities($key);
				  $value = cms_htmlentities($value);
					if ($count > 0)
					{
						if ($config["assume_mod_rewrite"] && $rewrite == true)
							$text .= '?';
						else
							$text .= '&amp;';
					}
					else
					{
						$text .= '&amp;';
					}
					$text .= $id.$key.'='.$value;
					$count++;
				}
				if (!$onlyhref)
				{
					$text .= "\"";
					$text .= '>'.$contents.'</a>';
				}
			}
		}
	}

	return $text;
}

function cms_module_CreateFieldsetStart(&$modinstance, $id, $name, $legend_text='', $addtext='', $addtext_legend='', $html_id = '')
{
	$id = cms_htmlentities($id);
	$name = cms_htmlentities($name);
	$legend_text = cms_htmlentities($legendtext);
	
	if ($html_id == '')
		$html_id = CmsResponse::make_dom_id($id . $name);

	$text = '<fieldset '. $addtext. ' id="'.$html_id.'">'."\n";
	$text .= '<legend '. $addtext_legend .'>'."\n";
	$text .= $legend_text;
	$text .= '</legend>';
	return $text;
}

?>
