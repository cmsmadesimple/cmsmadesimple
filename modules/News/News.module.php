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
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $
if( !isset($gCms) ) exit;

class News extends CMSModule
{
	function GetName()
	{
		return 'News';
	}

	function GetFriendlyName()
	{
		return $this->Lang('news');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '2.12.9';
	}

	function MinimumCMSVersion()
	{
	  return '1.11';
	}

	function GetAdminDescription()
	{
		return $this->Lang('description');
	}

	function GetAdminSection()
	{
		return 'content';
	}

	function InitializeFrontend()
	{
	  $this->RestrictUnknownParams();

	  $this->SetParameterType('pagelimit',CLEAN_INT);
	  $this->SetParameterType('browsecat',CLEAN_INT);
	  $this->SetParameterType('showall',CLEAN_INT);
	  $this->SetParameterType('showarchive',CLEAN_INT);
	  $this->SetParameterType('sortasc',CLEAN_STRING); // should be int, or boolean
	  $this->SetParameterType('sortby',CLEAN_STRING);
	  $this->SetParameterType('detailpage',CLEAN_STRING);
	  $this->SetParameterType('detailtemplate',CLEAN_STRING);
	  $this->SetParameterType('formtemplate',CLEAN_STRING);
	  $this->SetParameterType('browsecattemplate',CLEAN_STRING);
	  $this->SetParameterType('summarytemplate',CLEAN_STRING);
	  $this->SetParameterType('moretext',CLEAN_STRING);
	  $this->SetParameterType('category',CLEAN_STRING);
	  $this->SetParameterType('category_id',CLEAN_STRING);
	  $this->SetParameterType('number',CLEAN_INT);
	  $this->SetParameterType('start',CLEAN_INT);
	  $this->SetParameterType('pagenumber',CLEAN_INT);
	  $this->SetParameterType('articleid',CLEAN_INT);
	  $this->SetParameterType('origid',CLEAN_INT);
	  $this->SetParameterType('showtemplate',CLEAN_STRING);
	  $this->SetParameterType('assign',CLEAN_STRING);
	  $this->SetParameterType('inline',CLEAN_STRING);
	  $this->SetParameterType('preview',CLEAN_STRING);
		
	  // form parameters
	  $this->SetParameterType('submit',CLEAN_STRING);
	  $this->SetParameterType('cancel',CLEAN_STRING);
	  $this->SetParameterType('category',CLEAN_STRING);
	  $this->SetParameterType('title',CLEAN_STRING);
	  $this->SetParameterType('content',CLEAN_STRING);
	  $this->SetParameterType('summary',CLEAN_STRING);
	  $this->SetParameterType('extra',CLEAN_STRING);
	  $this->SetParameterType('postdate',CLEAN_STRING);
	  $this->SetParameterType('postdate_Hour',CLEAN_STRING);
	  $this->SetParameterType('postdate_Minute',CLEAN_STRING);
	  $this->SetParameterType('postdate_Second',CLEAN_STRING);
	  $this->SetParameterType('postdate_Month',CLEAN_STRING);
	  $this->SetParameterType('postdate_Day',CLEAN_STRING);
	  $this->SetParameterType('postdate_Year',CLEAN_STRING);
	  $this->SetParameterType('startdate',CLEAN_STRING);
	  $this->SetParameterType('startdate_Hour',CLEAN_STRING);
	  $this->SetParameterType('startdate_Minute',CLEAN_STRING);
	  $this->SetParameterType('startdate_Second',CLEAN_STRING);
	  $this->SetParameterType('startdate_Month',CLEAN_STRING);
	  $this->SetParameterType('startdate_Day',CLEAN_STRING);
	  $this->SetParameterType('startdate_Year',CLEAN_STRING);
	  $this->SetParameterType('enddate',CLEAN_STRING);
	  $this->SetParameterType('enddate_Hour',CLEAN_STRING);
	  $this->SetParameterType('enddate_Minute',CLEAN_STRING);
	  $this->SetParameterType('enddate_Second',CLEAN_STRING);
	  $this->SetParameterType('enddate_Month',CLEAN_STRING);
	  $this->SetParameterType('enddate_Day',CLEAN_STRING);
	  $this->SetParameterType('enddate_Year',CLEAN_STRING);
	  $this->SetParameterType('useexp',CLEAN_INT);
	  $this->SetParameterType('input_category',CLEAN_STRING);
	  $this->SetParameterType('category_id',CLEAN_INT);
		
	  $this->SetParameterType(CLEAN_REGEXP.'/news_customfield_.*/',CLEAN_STRING);
	  $this->SetParameterType('junk',CLEAN_STRING);
	}


	function InitializeAdmin()
	{
	  $this->CreateParameter('pagelimit', 100000, $this->Lang('help_pagelimit'));
	  $this->CreateParameter('browsecat', 0, $this->lang('helpbrowsecat'));
	  $this->CreateParameter('showall', 0, $this->lang('helpshowall'));
	  $this->CreateParameter('showarchive', 0, $this->lang('helpshowarchive'));
	  $this->CreateParameter('sortasc', 'true', $this->lang('helpsortasc'));
	  $this->CreateParameter('sortby', 'news_date', $this->lang('helpsortby'));
	  $this->CreateParameter('detailpage', 'pagealias', $this->lang('helpdetailpage'));
	  $this->CreateParameter('detailtemplate', '', $this->lang('helpdetailtemplate'));
	  $this->CreateParameter('summarytemplate', '', $this->lang('helpsummarytemplate'));
	  $this->CreateParameter('formtemplate', '', $this->lang('helpformtemplate'));
	  $this->CreateParameter('browsecattemplate', '', $this->lang('helpbrowsecattemplate'));
	  $this->CreateParameter('moretext', 'more...', $this->lang('helpmoretext'));
	  $this->CreateParameter('category', 'category', $this->lang('helpcategory'));
	  $this->CreateParameter('number', 100000, $this->lang('helpnumber'));
	  $this->CreateParameter('start', 0, $this->lang('helpstart'));
	  $this->CreateParameter('action','default',$this->Lang('helpaction'));
	  $this->CreateParameter('articleid','',$this->Lang('help_articleid'));
	}

	function AllowSmartyCaching()
	{
	  return TRUE;
	}

	function LazyLoadFrontend()
	{
	  return TRUE;
	}

	function LazyLoadAdmin()
	{
	  return TRUE;
	}

	function VisibleToAdminUser()
	{
	  return $this->CheckPermission('Modify News') || 
	    $this->CheckPermission('Modify Site Preferences') || 
	    $this->CheckPermission('Modify Templates') || 
	    $this->CheckPermission('Approve News');
	}
	
    function GetDfltEmailTemplate()
    {
      $text = "A new news article has been posted to your website.  The details are as follows:\n";
      $text .= "Title:      {\$title}\n";
      $text .= "IP Address: {\$ipaddress}\n";
      $text .= "Summary:    {\$summary|strip_tags}\n";
      $text .= "Post Date:  {\$postdate|date_format}\n";
      $text .= "Start Date: {\$startdate|date_format}\n";
      $text .= "End Date:   {\$enddate|date_format}\n";
      return $text;
    }

    function InstallPostMessage()
    {
      return $this->Lang('postinstall');
    }

    function GetHelp()
    {
      return $this->Lang('help');
    }

    function GetAuthor()
    {
      return 'Ted Kulp';
    }

    function GetAuthorEmail()
    {
      return 'wishy@cmsmadesimple.org';
    }

    function GetChangeLog()
    {
      return file_get_contents(dirname(__FILE__).'/changelog.inc');
    }
	
    function GetEventDescription( $eventname )
    {
      return $this->lang('eventdesc-' . $eventname);
    }

    function GetEventHelp( $eventname )
    {
      return $this->lang('eventhelp-' . $eventname);
    }
	
    function SearchResult($returnid, $articleid, $attr = '')
    {
      $result = array();
		
      if ($attr == 'article')
	{
	  $db = $this->GetDb();
	  $now = $db->DbTimeStamp(time());
	  $zerotime = $db->DbTimeStamp(1);
	  $q = "SELECT news_title,news_url FROM ".cms_db_prefix()."module_news WHERE
		       news_id = ?";
	  if( $this->GetPreference('expired_searchable',1) == 0 )
	    {
	      // make sure we don't return expired articles.
	      // if we don't want em to.
	      $q .= " AND ((".$db->IfNull('end_time',$zerotime)." = ".$zerotime.") OR (end_time > $now))";
	    }
	  $dbresult = $db->Execute( $q, array( $articleid ) );
	  if ($dbresult)
	    {
	      $row = $dbresult->FetchRow();

	      //0 position is the prefix displayed in the list results.
	      $result[0] = $this->GetFriendlyName();
		
	      //1 position is the title
	      $result[1] = $row['news_title'];

	      //2 position is the URL to the title.
	      $pretty_url = $row['news_url'];
	      if( $pretty_url == '' )
		{
		  $aliased_title = munge_string_to_url($row['news_title']);
		  $prettyurl = 'news/' . $articleid.'/'.$returnid."/$aliased_title";
		}
	      $result[2] = $this->CreateLink('cntnt01', 'detail', $returnid, '', array('articleid' => $articleid) ,'', true, false, '', true, $prettyurl);
	    }
	}
	
      return $result;
    }


    function SearchResultWithParams($returnid, $articleid, $attr = '', $params = '')
    {
      $gCms = cmsms();
      $result = array();
      
      if ($attr == 'article')
	{
	  $db = $this->GetDb();
	  $q = "SELECT news_title,news_url FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
	  $row = $db->GetRow( $q, array( $articleid ) );
	  
	  if ($row)
	    {
	      //0 position is the prefix displayed in the list results.
	      $result[0] = $this->GetFriendlyName();
	      
	      //1 position is the title
	      $result[1] = $row['news_title'];
	      
	      //2 position is the URL to the title.
	      $detailpage = $returnid;
	      if( isset($params['detailpage']) )
		{
		  $manager = $gCms->GetHierarchyManager();
		  $node = $manager->sureGetNodeByAlias($params['detailpage']);
		  if (isset($node))
		    {
		      $detailpage = $node->getID();
		    }
		  else
		    {
		      $node = $manager->sureGetNodeById($params['detailpage']);
		      if (isset($node))
			{
			  $detailpage = $params['detailpage'];
			}
		    }
		}
	      if( $detailpage == '' ) $detailpage = $returnid;

	      $detailtemplate = '';
	      if( isset($params['detailtemplate']) )
		{
		  $manager = $gCms->GetHierarchyManager();
		  $node = $manager->sureGetNodeByAlias($params['detailtemplate']);
		  if (isset($node))
		    {
		      $detailtemplate = '/d,' . $params['detailtemplate'];
		    }
		}

	      if( $row['news_url'] != '' )
		{
		  $aliased_title = munge_string_to_url($row['news_title']);
		  $prettyurl = 'news/' . $articleid.'/'.$detailpage."/$aliased_title".$detailtemplate;
		}
	      else
		{
		  $prettyurl = $row['news_url'];
		}

	      $parms = array();
	      $parms['articleid'] = $articleid;
	      if( isset($params['detailtemplate']) ) $parms['detailtemplate'] = $params['detailtemplate'];
	      $result[2] = $this->CreateLink('cntnt01', 'detail', $detailpage, '', $parms ,'', true, false, '', true, $prettyurl);
	    }
	}
      
      return $result;
    }
	

    function SearchReindex(&$module)
    {
      $db = $this->GetDb();
		
      $query = 'SELECT * FROM '.cms_db_prefix().'module_news ORDER BY news_date';
      $result = $db->Execute($query);

      while ($result && !$result->EOF)
	{
	  if ($result->fields['status'] == 'published')
	    {
	      $module->AddWords($this->GetName(), 
				$result->fields['news_id'], 'article', 
				$result->fields['news_data'] . ' ' . $result->fields['summary'] . ' ' . $result->fields['news_title'] . ' ' . $result->fields['news_title'],
				($result->fields['end_time'] != NULL && $this->GetPreference('expired_searchable',0) == 0) ?  $db->UnixTimeStamp($result->fields['end_time']) : NULL);
	    }
	  $result->MoveNext();
	}
    }


  function myRedirectToTab( $id, $tab, $params = '' )
  {
    $parms = array();
    if( is_array( $params ) )
      {
	$parms = $params;
      }
    $parms = array('active_tab' => $tab );
    $this->myRedirect( $id, 'defaultadmin', $parms );
  }


  function myRedirect( $id, $action, $params = '' )
  {
    unset( $params['action'] );
    $this->Redirect( $id, $action, '', $params );
  }


  function GetTypesDropdown( $id, $name, $selected = '' )
  {
    $items = array($this->Lang('textbox') => 'textbox', 
		   $this->Lang('checkbox') => 'checkbox',
		   $this->Lang('textarea') => 'textarea',
		   $this->Lang('file') => 'file');
    return $this->CreateInputDropdown($id, $name, $items, -1, $selected);
  }
  

  function GetNotificationOutput($priority = 2)
  {
    // if this user has permission to change News articles from
    // Draft to published, and there are draft news articles
    // then display a nice message.
    // this is a priority 2 item.
    if( $priority >= 2 )
      {
	$output = array();
	if( $this->CheckPermission('Approve News') )
	  {
	    $db = $this->GetDb();
	    $query = 'SELECT count(news_id) FROM '.cms_db_prefix().'module_news n
                   WHERE status != \'published\'
                     AND (('.$db->IfNull('end_time',$db->DbTimeStamp(1)).' = '.$db->DbTimeStamp(1).') OR (end_time > '.$db->DbTimeStamp(time()).')) ';
	    $count = $db->GetOne($query);
	    if( $count )
	      {
		$obj = new StdClass;
		$obj->priority = 2;
		$link = $this->CreateLink('m1_','defaultadmin','',
					  $this->Lang('notify_n_draft_items_sub',$count));
		$obj->html = $this->Lang('notify_n_draft_items',$link);
		$output[] = $obj;
	      }
	  }
      }
    return $output;
  }

  
  public function CreateStaticRoutes()
  {
    $db = cmsms()->GetDb();

    $route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)\/d,(?P<detailtemplate>.*?)$/',
			  $this->GetName());
    cms_route_manager::add_static($route);
    $route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)$/',$this->GetName());
    cms_route_manager::add_static($route);
    $route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)$/',$this->GetName());
    cms_route_manager::add_static($route);
    $route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)$/',$this->GetName());
    cms_route_manager::add_static($route);

    $query = 'SELECT news_id,news_url FROM '.cms_db_prefix().'module_news
              WHERE status = ? AND news_url != ? AND '
      . '('.$db->ifNull('start_time',$db->DbTimeStamp(1)).' < NOW()) AND '
      . '(('.$db->IfNull('end_time',$db->DbTimeStamp(1)).' = '.$db->DbTimeStamp(1).') OR (end_time > NOW()))';
    $query .= ' ORDER BY news_date DESC';
    $tmp = $db->GetArray($query,array('published',''));

    if( is_array($tmp) )
      {
	foreach( $tmp as $one )
	  {
	    news_admin_ops::register_static_route($one['news_url'],$one['news_id']);
	  }
      }
  }
}

# vim:ts=4 sw=4 noet
?>
