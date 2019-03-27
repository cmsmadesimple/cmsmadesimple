<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 * Methods for modules to do form related functions; included in the module class when needed
 * @see CMSModule
 * @since		1.0
 * @package		CMS
 * @license GPL
 */

/**
 * @access private
 */
function cms_module_CreateFormStart(&$modinstance, $id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='')
{
    die('should not be called '.__FUNCTION__);
    $gCms = CmsApp::get_instance();
    static $_formcount = 1;

    $id = cms_htmlentities($id);
    $action = cms_htmlentities($action);
    $returnid = cms_htmlentities($returnid);
    $method = cms_htmlentities($method);
    $enctype = cms_htmlentities($enctype);
    $idsuffix = cms_htmlentities($idsuffix);

    if ($idsuffix == '') $idsuffix = $_formcount++;

    // use create_url, but create an ugly url for the form action.
    $goto = $modinstance->create_url($id,$action,$returnid, $inline, false, ':NOPRETTY:');
    /*
    $goto = 'moduleinterface.php';
    if( $returnid > 0 ) {
        $goto = 'index.php';
        $content_obj = \cms_utils::get_current_content();
        if( $content_obj ) $goto = $content_obj->GetURL();
    }
    if( CmsApp::get_instance()->is_https_request() && strpos($goto,':') !== FALSE ) $goto = str_replace('http:','https:',$goto);
    */

    $goto = ' action="'.$goto.'"';

    $text = '<form id="'.$id.'moduleform_'.$idsuffix.'" method="'.$method.'"'.$goto;
    $text .= ' class="cms_form"';
    if ($enctype != '') $text .= ' enctype="'.$enctype.'"';
    if ($extra != '') $text .= ' '.$extra;
    $text .= '>'."\n".'<div class="hidden">'."\n";
    // $text .= '<input type="hidden" name="mact" value="'.$modinstance->GetName().','.$id.','.$action.','.($inline == true?1:0).'" />'."\n";

    if ($returnid > 0 ) {
        $text .= '<input type="hidden" name="'.$id.'returnid" value="'.$returnid.'" />'."\n";
        if ($inline) $text .= '<input type="hidden" name="'.$modinstance->cms->config['query_var'].'" value="'.$returnid.'" />'."\n";
    }
    else {
        $text .= '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'" />'."\n";
    }
    foreach ($params as $key=>$value) {
        $value = cms_htmlentities($value);
        if ($key != 'module' && $key != 'action' && $key != 'id') {
            $text .= '<input type="hidden" name="'.$id.$key.'" value="'.$value.'" />'."\n";
        }
    }
    $text .= "</div>\n";

    return $text;
}

/**
 * @access private
 */
function cms_module_CreateLabelForInput(&$modinstance, $id, $name, $labeltext='', $addttext='')
{
    $text = '<label class="cms_label" for="'.cms_htmlentities($id.$name).'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    $text .= '>'.$labeltext.'</label>'."\n";
    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputText(&$modinstance, $id, $name, $value='', $size='10', $maxlength='255', $addttext='')
{
    $value = cms_htmlentities($value);
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $size = cms_htmlentities($size);
    $maxlength = cms_htmlentities($maxlength);

    $value = str_replace('"', '&quot;', $value);

    $text = '<input type="text" class="cms_textfield" name="'.$id.$name.'" id="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    $text .= " />\n";
    return $text;
}

/**
 * @access private
 */
/**
 * @access private
 */
function cms_module_CreateInputFile(&$modinstance, $id, $name, $accept='', $size='10',$addttext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $accept = cms_htmlentities($accept);
    $size = cms_htmlentities($size);

    $text='<input type="file" class="cms_browse" name="'.$id.$name.'" size="'.$size.'"';
    if ($accept != '') $text .= ' accept="' . $accept.'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    $text .= " />\n";
    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputPassword(&$modinstance, $id, $name, $value='', $size='10', $maxlength='255', $addttext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $value = cms_htmlentities($value);
    $size = cms_htmlentities($size);
    $maxlength = cms_htmlentities($maxlength);

    $value = str_replace('"', '&quot;', $value);
    $text = '<input type="password" class="cms_password" id="'.$id.$name.'" name="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    $text .= " />\n";
    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputHidden(&$modinstance, $id, $name, $value='', $addttext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $value = cms_htmlentities($value);

    $value = str_replace('"', '&quot;', $value);
    $text = '<input type="hidden" id="'.$id.$name.'" name="'.$id.$name.'" value="'.$value.'"';
    if ($addttext != '') $text .= ' '.$addttext;
    $text .= " />\n";
    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputCheckbox(&$modinstance, $id, $name, $value='', $selectedvalue='', $addttext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $value = cms_htmlentities($value);
    $selectedvalue = cms_htmlentities($selectedvalue);

    $text = '<input type="checkbox" class="cms_checkbox" name="'.$id.$name.'" value="'.$value.'"';
    if ($selectedvalue == $value) $text .= ' ' . 'checked="checked"';
    if ($addttext != '') $text .= ' '.$addttext;
    $text .= " />\n";
    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputSubmit(&$modinstance, $id, $name, $value='', $addttext='', $image='', $confirmtext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $image = cms_htmlentities($image);

    $text = '<input class="cms_submit" name="'.$id.$name.'" id="'.$id.$name.'" value="'.$value.'" type=';

    if ($image != '') {
        $text .= '"image"';
        $img = CMS_ROOT_URL . '/' . $image;
        $text .= ' src="'.$img.'"';
    }
    else {
        $text .= '"submit"';
    }
    if ($confirmtext != '' ) $text .= ' onclick="return confirm(\''.$confirmtext.'\');"';
    if ($addttext != '') $text .= ' '.$addttext;

    $text .= ' />';
    return $text . "\n";
}

/**
 * @access private
 */
function cms_module_CreateInputReset(&$modinstance, $id, $name, $value='Reset', $addttext='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);

    $text = '<input type="reset" class="cms_reset" name="'.$id.$name.'" value="'.$value.'"';
    if ($addttext != '') $text .= ' '.$addttext;
    $text .= ' />';
    return $text . "\n";
}

/**
 * @access private
 */
function cms_module_CreateInputDropdown(&$modinstance, $id, $name, $items, $selectedindex, $selectedvalue, $addttext)
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $selectedindex = cms_htmlentities($selectedindex);
    $selectedvalue = cms_htmlentities($selectedvalue);

    $text = '<select class="cms_dropdown" name="'.$id.$name.'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    $text .= '>';
    $count = 0;
    if (is_array($items) && count($items) > 0) {
        foreach ($items as $key=>$value) {
            $text .= '<option value="'.$value.'"';
            if ($selectedindex == $count || $selectedvalue == $value) $text .= ' ' . 'selected="selected"';
            $text .= '>';
            $text .= $key;
            $text .= '</option>';
            $count++;
        }
    }
    $text .= '</select>'."\n";

    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputSelectList(&$modinstance, $id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true)
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $size = cms_htmlentities($size);
    $multiple = cms_htmlentities($multiple);

    if( strstr($name,'[]') === FALSE && $multiple ) $name.='[]';
    $text = '<select class="cms_select" name="'.$id.$name.'"';
    if ($addttext != '') $text .= ' ' . $addttext;
    if( $multiple ) $text .= ' multiple="multiple" ';
    $text .= 'size="'.$size.'">';
    $count = 0;
    foreach ($items as $key=>$value) {
        $value = cms_htmlentities($value);

        $text .= '<option value="'.$value.'"';
        if (is_array($selecteditems) && in_array($value, $selecteditems)) $text .= ' ' . 'selected="selected"';
        $text .= '>';
        $text .= $key;
        $text .= '</option>';
        $count++;
    }
    $text .= '</select>'."\n";

    return $text;
}

/**
 * @access private
 */
function cms_module_CreateInputRadioGroup(&$modinstance, $id, $name, $items, $selectedvalue='', $addttext='', $delimiter='')
{
    $id = cms_htmlentities($id);
    $name = cms_htmlentities($name);
    $selectedvalue = cms_htmlentities($selectedvalue);

    $text = '';
    $counter = 0;
    foreach ($items as $key=>$value) {
        $value = cms_htmlentities($value);

        $counter = $counter + 1;
        $text .= '<input class="cms_radio" type="radio" name="'.$id.$name.'" id="'.$id.$name.$counter.'" value="'.$value.'"';
        if ($addttext != '') $text .= ' ' . $addttext;
        if ($selectedvalue == $value) $text .= ' ' . 'checked="checked"';
        $text .= ' />';
        $text .= '<label class="cms_label" for="'.$id.$name.$counter.'">'.$key .'</label>' . $delimiter;
    }

    return $text;
}

/**
 * @access private
 */
function cms_module_CreateLink(&$modinstance, $id, $action, $returnid='', $contents='', $params=array(), $warn_message='',
							   $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='')
{
    if( !is_array($params) && $params == '' ) $params = array();
    $id = cms_htmlentities($id);
    $action = cms_htmlentities($action);
    $returnid = cms_htmlentities($returnid);
    $prettyurl = cms_htmlentities($prettyurl);

    $class = (isset($params['class'])?cms_htmlentities($params['class']):'');

    // create url....
    $text = $modinstance->create_url($id,$action,$returnid,$params,$inline,$targetcontentonly,$prettyurl);

    if (!$onlyhref) {
        $beginning = '<a';
        if ($class != '') $beginning .= ' class="'.$class.'"';
        $beginning .= ' href="';
        $text = $beginning . $text . "\"";
        if ($warn_message != '') $text .= ' onclick="return confirm(\''.$warn_message.'\');"';
        if ($addttext != '') $text .= ' ' . $addttext;
        $text .= '>'.$contents.'</a>';
    }
    return $text;
}


/**
 * @access private
 */
function cms_module_create_url(&$modinstance,$id,$action,$returnid='',$params=array(),
							   $inline=false,$targetcontentonly=false,$prettyurl='')
{
    $gCms = cmsms();
    $config = $gCms->GetConfig();
    $mact_assistant = $gCms->get_mact_encoder();
    $contentops = $gCms->GetContentOperations();

    $text = '';
    if( empty($prettyurl) && $config['url_rewriting'] != 'none' ) {
        // attempt to get a pretty url from the module... this is useful
        // incase this method is being called from outside the source module.
        // i.e: comments module wants a link to the article the comments are about
        // or something.
        $prettyurl = $modinstance->get_pretty_url($id,$action,$returnid,$params,$inline);
    }

    $base_url = CMS_ROOT_URL;

    if ($prettyurl && $prettyurl != ':NOPRETTY:' && $config['url_rewriting'] == 'mod_rewrite') {
        $text = $base_url . '/' . $prettyurl . $config['page_extension'];
    }
    else if ($prettyurl && $prettyurl != ':NOPRETTY:' && $config['url_rewriting'] == 'internal') {
        $text = $base_url . '/index.php/' . $prettyurl . $config['page_extension'];
    }
    else {
        // get the destination content object
        $extraparams = null;
        if( $returnid <= 0 ) {
            $id = 'm1_';
            $text = $config['admin_url'] .'/moduleinterface.php';
            if( isset($_SESSION[CMS_USER_KEY]) ) $extraparams[CMS_SECURE_PARAM_NAME] = $_SESSION[CMS_USER_KEY];

        }
        else {
            if( $targetcontentonly || !$inline || !$id ) $id = 'cntnt01';

            $text = $base_url.'/index.php';
            $contentobj = $contentops->LoadContentFromID($returnid);
            if( $contentobj && ($tmp = $contentobj->GetURL()) ) {
                $text = $tmp;
            } else {
                $extraparams[$config['query_var']] = $returnid;
            }
        }

        // now we do the encoding of parameters.
        $mact = $mact_assistant->create_mactinfo($modinstance,$id,$action,$inline,$params);
        // note extraparms are added to the URL, but not encoded into the _R mact stuff.
        $text .= '?'.$mact_assistant->encode_to_url($mact,$extraparams);
    }

    return $text;
}

/**
 * @access private
 * @deprecated
 */
function cms_module_CreateReturnLink(&$modinstance, $id, $returnid, $contents='', $params=array(), $onlyhref=false)
{
    // as of 2.3 this method ignores the params array, and is deprecated.
    $id = trim($id);
    $returnid = (int) $returnid;
    $contents = cms_htmlentities($contents);

    $gCms = CmsApp::get_instance();
    $manager = $gCms->GetHierarchyManager();
    $node = $manager->sureGetNodeById($returnid);
    if( !$node ) return;
    $content = $node->GetContent();
    if( !$content ) return;
    $url = $content->GetURL();
    if( !$url ) return;
    if( $onlyhref ) return $url;

    $fmt = '<a href="%s">%s</a>';
    return sprintf($fmt,$url,$contents);
}
