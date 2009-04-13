<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class Link extends ContentBase
{

    function Link() {
        $this->ContentBase();
        $this->mProperties->SetAllowedPropertyNames(array('url', 'target','extra1','extra2','extra3',
							  'image', 'thumbnail'));
    }

    function IsCopyable()
    {
        return TRUE;
    }

    function FriendlyName()
    {
      return lang('contenttype_link');
    }

    function SetProperties()
    {
      parent::SetProperties();
      $this->AddBaseProperty('url',10,1);

      #Turn off caching
      $this->mCachable = false;
    }

    function FillParams($params)
    {
	if (isset($params))
	{
	  $parameters = array('url', 'target', 'extra1', 'extra2', 'extra3',
			      'image', 'thumbnail');
	  foreach ($parameters as $oneparam)
	    {
	      if (isset($params[$oneparam]))
		{
		  $this->SetPropertyValue($oneparam, $params[$oneparam]);
		}
	    }
	    if (isset($_POST['file_url']) && $_POST['file_url'] != "---")
	    {
	    	$this->SetPropertyValue('url', $params['file_url']);
            } 
            # make sure target keeps empty even with the new dropdown value
	    if (isset($_POST['target']) && $_POST['target'] == "---")
	    {
	    	$this->SetPropertyValue('target', '');
            } 
	    if (isset($params['extra1']) )
	      {
		$this->SetPropertyValue('extra1',trim($_POST['extra1']));
	      }
	    if (isset($params['extra2']) )
	      {
		$this->SetPropertyValue('extra2',trim($_POST['extra2']));
	      }
	    if (isset($params['extra3']) )
	      {
		$this->SetPropertyValue('extra3',trim($_POST['extra3']));
	      }
	    if (isset($params['image']) )
	      {
		$this->SetPropertyValue('image',trim($_POST['image']));
	      }
	    if (isset($params['thumbnail']) )
	      {
		$this->SetPropertyValue('thumbnail',trim($_POST['thumbnail']));
	      }
	    if (isset($params['title']))
	    {
		$this->mName = $params['title'];
	    }
	    if (isset($params['menutext']))
	    {
		$this->mMenuText = $params['menutext'];
	    }
	    if (isset($params['accesskey']))
	    {
		$this->mAccessKey = $params['accesskey'];
	    }
	    if (isset($params['titleattribute']))
	    {
		$this->mTitleAttribute = $params['titleattribute'];
	    }
	    if (isset($params['tabindex']))
	    {
		$this->mTabIndex = $params['tabindex'];
	    }
	    if (isset($params['alias']))
	    {
	      $this->SetAlias(trim($params['alias']),$this->doAutoAliasIfEnabled);
	    }
	    else if( $this->doAutoAliasIfEnabled)
            {
	      $this->SetAlias('');
	    }
	    if (isset($params['parent_id']))
	    {
		if ($this->mParentId != $params['parent_id'])
		{
		    $this->mHierarchy = '';
		    $this->mItemOrder = -1;
		}
		$this->mParentId = $params['parent_id'];
	    }
	    if (isset($params['active']))
	    {
		$this->mActive = true;
	    }
	    else
	    {
		$this->mActive = false;
	    }
	    if (isset($params['showinmenu']))
	    {
		$this->mShowInMenu = true;
	    }
	    else
	    {
		$this->mShowInMenu = false;
	    }
	}
    }

    function ValidateData()
    {
	$errors = array();

	if ($this->mName == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('title')));
	    $result = false;
	}

	if ($this->mMenuText == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('menutext')));
	    $result = false;
	}

	if ($this->mAlias != $this->mOldAlias || $this->mAlias == '') #Should only be empty if auto alias is false
	{
     	    global $gCms;
	    $contentops =& $gCms->GetContentOperations();
	    $error = $contentops->CheckAliasError($this->mAlias, $this->mId);
	    if ($error !== FALSE)
	    {
		$errors[]= $error;
		$result = false;
	    }
	}


	if ($this->GetPropertyValue('url') == '' && TRUE == empty($_POST['file_url']))
	{
	    $errors[]= lang('nofieldgiven',array(lang('url')));
	    $result = false;
	}

	return (count($errors) > 0?$errors:FALSE);
    }

    function Show()
    {
    }

    function TabNames()
    {
      $res = array(lang('main'));
      if( check_permission(get_userid(),'Modify Page Structure') )
	{
	  $res[] = lang('options');
	}
      return $res;
    }

    function display_single_element($one,$adding)
    {
      switch($one) {
	case 'url':
	  {
	    return array(lang('url').':','<input type="text" name="url" size="80" value="'.cms_htmlentities($this->GetPropertyValue('url')).'" />');
	  }
	  break;

      default:
	return parent::display_single_element($one,$adding);
      }
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
      global $gCms;

      switch($tab)
	{
	case '0':
	  return $this->display_attributes($adding);
	  break;
	case '1':
	  return $this->display_attributes($adding,1);
	  break;
	}
    }

	/* Modfied from the Diagnostics module's directoryToArray. Thanks to SjG */
	function directoryToSelect($directory, $recursive, &$orig_dir, $url)
	{
		$select = '';
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file[0] != '.') { // Mod Skip hidden file/dir
					if (is_dir($directory. "/" . $file)) {
						if($recursive) {
							$select .= $this->directoryToSelect($directory. "/" . $file, $recursive, $orig_dir, $url);
						}
						$file = $directory . "/" . $file;
						$file = preg_replace("/\/\//si", "/", $file);
						// $array_items[$file] = "(dir)";
					} else {
						$file = $directory . "/" . $file;
						// $stats = stat ( $file );
						$file = preg_replace("/\/\//si", "/", $file);
						// $array_items[$file] = "(".$stats[7].") " . date("Y-m-d H:i:s",$stats[9]);
						$uploads_dir = basename($orig_dir);
						$file = str_replace($orig_dir, '', $file);
						$file = $uploads_dir.$file;
						// $array_items[$file] = $file;
						$select .= '<option value="'.$file.'"'.($url==$file?' selected="selected"':'').'>'.$file.'</option>';
					}
				}
			}
			closedir($handle);
		}
		return $select;
	}


    function GetURL($rewrite = true)
    {
	return cms_htmlentities($this->GetPropertyValue('url'));
    }
}

# vim:ts=4 sw=4 noet
?>
