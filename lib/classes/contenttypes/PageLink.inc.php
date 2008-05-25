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
#$Id: Link.inc.php 4225 2007-10-14 16:31:00Z calguy1000 $

class PageLink extends ContentBase
{

    function PageLink() {
        $this->ContentBase();
        $this->mProperties->SetAllowedPropertyNames(array('page', 'params', 'target'));
    }
	
    function FriendlyName()
    {
      return lang('contenttype_pagelink');
    }
	
    function SetProperties()
    {
		$this->mProperties->Add('int', 'page');
		$this->mProperties->add('string', 'params');
		$this->mProperties->Add('string', 'target');
		
		//Turn off caching
		$this->mCachable = false;
    }

    function FillParams($params)
    {
		if (isset($params))
			{
				$parameters = array('page', 'params','target');
				foreach ($parameters as $oneparam)
					{
						if (isset($params[$oneparam]))
							{
								$this->SetPropertyValue($oneparam, $params[$oneparam]);
							}
					}

				// make sure target keeps empty even with the new dropdown value
				if (isset($_POST['target']) && $_POST['target'] == "---")
					{
						$this->SetPropertyValue('target', '');
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
						$this->SetAlias(trim($params['alias']));
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
		
		if ($this->GetPropertyValue('page') == '-1')
			{
				$errors[]= lang('nofieldgiven',array(lang('page')));
				$result = false;
			}
		
		return (count($errors) > 0?$errors:FALSE);
    }

    function Show()
    {
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		
		$ret = array();
		
		$ret[]= array(lang('title').':','<input type="text" name="title" value="'.cms_htmlentities($this->mName).'" />');
		$ret[]= array(lang('menutext').':','<input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" />');
		if (check_permission(get_userid(), 'Modify Page Structure') || ($adding == true && check_permission(get_userid(), 'Add Pages')))
			{
				$ret[]= array(lang('parent').':', $contentops->CreateHierarchyDropdown($this->mId, $this->mParentId));
			}

		$ret[]= array(lang('destination_page').':', 
					  $contentops->CreateHierarchyDropdown($this->mId, 
									       $this->GetPropertyValue('page'), 'page'));
		
 		$ret[]= array(lang('additional_params').':','<input type="text" name="params" size="80" value="'.cms_htmlentities($this->GetPropertyValue('params')).'" />');
		
// 		$text = '<option value="---">'.lang('no_file_url').'</option>';
// 	$text .= $this->directoryToSelect($gCms->config['uploads_path'], true, $gCms->config['uploads_path'], $this->GetPropertyValue('url'));
// 	$ret[]= array(lang('file_url').':','<select name="file_url">'.$text.'</select>');
	
	$text = '<option value="---">'.lang('none').'</option>';
	$text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
	$text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
	$text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
	$text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
	$ret[]= array(lang('target').':','<select name="target">'.$text.'</select>');
	$ret[]= array(lang('active').':','<input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />');
	$ret[]= array(lang('showinmenu').':','<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');
	$ret[]= array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->mAlias.'" />');
	$ret[]= array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');
	$ret[]= array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="10" value="'.cms_htmlentities($this->mTabIndex).'" />');
	$ret[]= array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');

	if (!$adding && $showadmin)
	{
		$userops =& $gCms->GetUserOperations();
	    $ret[]= array(lang('owner').':', $userops->GenerateDropdown($this->Owner()));
	}

	if ($adding || $showadmin)
	{
	  if( $adding )
	    {
	      $addeditors = get_site_preference('additional_editors','');
	      $addteditors = explode(",",$addeditors);
	      $ret[]= $this->ShowAdditionalEditors($addteditors);
	    }
	  else
	    {
	      $ret[]= $this->ShowAdditionalEditors();
	    }
	}

	return $ret;
    }

    function GetURL($rewrite = true)
    {
      $page = $this->GetPropertyValue('page');
      $params = $this->GetPropertyValue('params');
      
      global $gCms;
      $contentops =& $gCms->GetContentOperations();
      $destcontent =& $contentops->LoadContentFromId($page);
      if( is_object( $destcontent ) ) 
	return $destcontent->GetURL();
      /*
      $alias = $destcontent->Alias();
      $config = &$gCms->GetConfig();
      $url = "";
      
      // use root_url for default content
      if($this->mDefaultContent) {
	$url =  $config['root_url']. '/';
	return $url;
      }
      
      if ($config["assume_mod_rewrite"] && $rewrite == true)
	{
	  if ($config['use_hierarchy'] == true)
	    {
	      $url = $config['root_url']. '/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
	    }
	  else
	    {
	      $url = $config['root_url']. '/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
	    }
	}
      else
	{
	  if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
	    {
	      if ($config['use_hierarchy'] == true)
		{
		  $url = $config['root_url'] . '/index.php/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
	      else
		{
		  $url = $config['root_url'] . '/index.php/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
	    }
	  else
	    {
	      $url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias;
	    }
	}
      return cms_htmlentities($url.$params);
      */
    } 
}

# vim:ts=4 sw=4 noet
?>
