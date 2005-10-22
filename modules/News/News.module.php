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
#$Id$

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
		return '2.0';
	}

	function GetAdminDescription()
	{
		return $this->Lang('description');
	}

	function GetAdminSection()
	{
		return 'content';
	}

	function SetParameters()
	{
		$this->CreateParameter('sortasc', 'true', $this->lang('helpsortasc'));
		$this->CreateParameter('sortby', 'news_date', $this->lang('helpsortby'));
		$this->CreateParameter('detailtemplate', 'sometemplate.tpl', $this->lang('helpdetailtemplate'));
		$this->CreateParameter('summarytemplate', 'sometemplate.tpl', $this->lang('helpsummarytemplate'));
		$this->CreateParameter('moretext', 'more...', $this->lang('helpmoretext'));
		$this->CreateParameter('category', 'category', $this->lang('helpcategory'));
		$this->CreateParameter('makerssbutton', 'true', $this->lang('helpmakerssbutton'));
		$this->CreateParameter('dateformat', '%b %d, %Y', $this->lang('helpdateformat'));
		$this->CreateParameter('number', '5', $this->lang('helpnumber'));
	}

	function ContentPostRender(&$content)
	{
		$this->log->debug('Starting News ContentPostRender');
		#if (eregi('\{cms_module module=[\"\']?news[\"\']?', $content))
		if (strpos($content, '<!-- Displaying News Module -->') !== FALSE)
		{
			global $gCms;
			$config = $this->cms->config;

			$params = array("showtemplate"=>"false");
			$url = $config['root_url'].'/index.php?module=News&amp;id=cntnt01&amp;cntnt01action=rss&amp;cntnt01showtemplate=false&amp;cntnt01returnid='.$gCms->variables['content_id'];

			$text = '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$url.'" />';
			$content = str_replace('</head>', $text.'</head>', $content);
		}
		$this->log->debug('Leaving News ContentPostRender');
	}

    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify News');
    }

	/**
	 * This function is not in the API!!!
	 */
	function GetSummaryHtmlTemplate()
	{
		return '<!-- Start News Display Template -->
{foreach from=$items item=entry}

<p>

{$entry->titlelink}

<br />{$entry->category}

{if $entry->formatpostdate}

<br />{$entry->formatpostdate}

{/if}

{if $entry->summary}

<br />{$entry->summary}
<br />[{$entry->morelink}]

{else if $entry->content}

<br />{$entry->content}

{/if}

</p>

{/foreach}
<!-- End News Display Template -->';
	}

	function GetDetailHtmlTemplate()
	{
		return '<h3 id="NewsPostDetailTitle">{$entry->title}</h3>

<hr id="NewsPostDetailHorizRule">

{if $entry->category}

<div id="NewsPostDetailCategory">
{$entry->category}
</div>

{/if}

{if $entry->postdate}

<div id="NewsPostDetailDate">
Posted: {$entry->postdate|date_format}
</div>

{/if}

{if $entry->summary}

<div id="NewsPostDetailSummary">
<strong>
{$entry->summary}
</strong>
</div>

{/if}

<div id="NewsPostDetailContent">
{$entry->content}
</div>

<div>
{$entry->printlink}
</div>';
	}

	function Install()
	{
		$db = $this->cms->db;

		$dict = NewDataDictionary($db);
		$flds = "
			news_id I KEY,
			news_category_id I,
			news_title C(255),
			news_data X,
			news_date T,
			summary X,
			start_time T,
			end_time T,
			status C(25),
			icon C(255),
			create_date T,
			modified_date T
		";

		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news", 
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_news_seq");

		$flds = "
			news_category_id I KEY,
			news_category_name C(255),
			parent_id I,
			hierarchy C(255),
			long_name X,
			create_date T,
			modified_date T
		";

		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_categories", 
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_news_categories_seq");

		#Set Permission
		$this->CreatePermission('Modify News', 'Modify News');

		# Setup summary template
		$this->SetTemplate('displaysummary', $this->GetSummaryHtmlTemplate());

		# Setup detail template
		$this->SetTemplate('displaydetail', $this->GetDetailHtmlTemplate());

		# Setup General category
		$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
		$query = 'INSERT INTO '.cms_db_prefix().'module_news_categories (news_category_id, news_category_name, parent_id, create_date, modified_date) VALUES (?,?,?,?,?)';
		$db->Execute($query, array($catid, 'General', -1, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		$this->UpdateHierarchyPositions();
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function Upgrade($oldversion, $newversion)
	{
		$current_version = $oldversion;
		switch($current_version)
		{
			case "1.0":
				$db = $this->cms->db;
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "start_time T, end_time T, icon C(255)");
				$dict->ExecuteSQLArray($sqlarray);
				$current_version = "1.1";
			case "1.1":
			case "1.2":
			case "1.3":
			case "1.4":
			case "1.5":
				$db = $this->cms->db;
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_cat C(255)");
				$dict->ExecuteSQLArray($sqlarray);
				$current_version = "1.6";
			case "1.6":
				# Setup display template
				#$this->SetTemplate('displayhtml', $this->GetDisplayHtmlTemplate());
				#$this->SetTemplate('displayrss', $this->GetDisplayRSSTemplate());
				$current_version = "1.7";
			case '1.7':
				#Makey new tables....

				$db = $this->cms->db;

				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "status C(25)");
				$dict->ExecuteSQLArray($sqlarray);

				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "summary X");
				$dict->ExecuteSQLArray($sqlarray);

				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_category_id I");
				$dict->ExecuteSQLArray($sqlarray);

				$query = "UPDATE ".cms_db_prefix()."module_news SET summary = ?, status = ?";
				$db->Execute($query, array('', 'published'));

				$flds = "
					news_category_id I KEY,
					news_category_name C(255),
					parent_id I,
					hierarchy C(255),
					long_name X,
					create_date T,
					modified_date T
				";
				$dict = NewDataDictionary($db);

				$taboptarray = array('mysql' => 'TYPE=MyISAM');
				$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_categories", 
						$flds, $taboptarray);
				$dict->ExecuteSQLArray($sqlarray);

				$db->CreateSequence(cms_db_prefix()."module_news_categories_seq");

				$query = "SELECT DISTINCT news_cat FROM ".cms_db_prefix()."module_news WHERE news_cat IS NOT NULL";
				$dbresult = $db->Execute($query);
				while ($row = $dbresult->FetchRow())
				{
					$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
					$query = "INSERT INTO ".cms_db_prefix()."module_news_categories (news_category_id, news_category_name, parent_id, hierarchy, long_name, create_date, modified_date) VALUES (?,?,?,?,?,?,?)";
					$db->Execute($query,array($catid, $row['news_cat'], -1, '', '', $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));

					$query = "UPDATE ".cms_db_prefix()."module_news SET news_category_id = ? WHERE news_cat = ?";
					$db->Execute($query, array($catid, $row['news_cat']));
				}

				# Setup summary template
				$this->SetTemplate('displaysummary', $this->GetSummaryHtmlTemplate());

				# Setup detail template
				$this->SetTemplate('displaydetail', $this->GetDetailHtmlTemplate());

				$this->UpdateHierarchyPositions();

				$current_version = "2.0";
		}
	}

	function Uninstall()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary( $db );

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news" );
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_categories" );
		$dict->ExecuteSQLArray($sqlarray);

		/*
		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_article_categories" );
		$dict->ExecuteSQLArray($sqlarray);
		*/

		$db->DropSequence( cms_db_prefix()."module_news_seq" );
		$db->DropSequence( cms_db_prefix()."module_news_categories_seq" );

		$this->RemovePermission('Modify News');
	}

    function UpdateHierarchyPositions()
    {
		$db = $this->cms->db;

		$query = "SELECT news_category_id, news_category_name FROM ".cms_db_prefix()."module_news_categories";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount())
		{
			while ($row = $dbresult->FetchRow())
			{
				$current_hierarchy_position = "";
				$current_long_name = "";
				$content_id = $row['news_category_id'];
				$current_parent_id = $row['news_category_id'];
				$count = 0;

				while ($current_parent_id > -1)
				{
					$query = "SELECT news_category_id, news_category_name, parent_id FROM ".cms_db_prefix()."module_news_categories WHERE news_category_id = ?";
					$dbresult2 = $db->Execute($query, array($current_parent_id));
					if ($dbresult2 && $dbresult2->RowCount())
					{
						$row = $dbresult2->FetchRow();
						$current_hierarchy_position = str_pad($row['news_category_id'], 5, '0', STR_PAD_LEFT) . "." . $current_hierarchy_position;
						$current_long_name = $row['news_category_name'] . ' | ' . $current_long_name;
						$current_parent_id = $row['parent_id'];
						$count++;
					}
					else
					{
						$current_parent_id = 0;
					}
				}

				if (strlen($current_hierarchy_position) > 0)
				{
					$current_hierarchy_position = substr($current_hierarchy_position, 0, strlen($current_hierarchy_position) - 1);
				}

				if (strlen($current_long_name) > 0)
				{
					$current_long_name = substr($current_long_name, 0, strlen($current_long_name) - 3);
				}


				$query = "UPDATE ".cms_db_prefix()."module_news_categories SET hierarchy = ?, long_name = ? WHERE news_category_id = ?";
				$db->Execute($query, array($current_hierarchy_position, $current_long_name, $content_id));
			}
		}
    }

	function CreateParentDropdown($id, $catid = -1, $selectedvalue = -1)
	{
		$db = $this->cms->db;

		$longname = '';

		$items['(None)'] = '-1';

		$query = "SELECT hierarchy, long_name FROM ".cms_db_prefix()."module_news_categories WHERE news_category_id = ?";
		$dbresult = $db->Execute($query, array($catid));
		if ($dbresult && $dbresult->RowCount())
		{
			while ($row = $dbresult->FetchRow())
			{
				$longname = $row['hierarchy'] . '%';
			}
		}

		$query = "SELECT news_category_id, news_category_name, hierarchy, long_name FROM ".cms_db_prefix()."module_news_categories WHERE hierarchy not like ? ORDER by hierarchy";
		$dbresult = $db->Execute($query, array($longname));
		if ($dbresult && $dbresult->RowCount())
		{
			while ($row = $dbresult->FetchRow())
			{
				$items[$row['long_name']] = $row['news_category_id'];
			}
		}

		return $this->CreateInputDropdown($id, 'parent', $items, -1, $selectedvalue);
	}

	function DoAction($action, $id, $params, $returnid = -1)
	{
		$db = $this->cms->db;
		switch ($action)
		{
			case "default":

				if (isset($params["makerssbutton"]))
				{
					$this->log->debug('Making RSS Button');
					$params = array("showtemplate"=>"false");
					echo $this->CreateLink($id, 'rss', $returnid, "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Newsfeed\" />", $params,'',false,false,'',true);
					return;
				}

				$entryarray = array();
				$query = '';

				if (isset($params["category"]) && $params["category"] != '')
				{
					$categories = explode(',', $params['category']);
					$query = "SELECT mn.*, mnc.news_category_name FROM " . cms_db_prefix() . "module_news mn LEFT OUTER JOIN " . cms_db_prefix() . "module_news_categories mnc ON mnc.news_category_id = mn.news_category_id WHERE status = 'published' AND (";
					$count = 0;
					foreach ($categories as $onecat)
					{
						if ($count > 0)
						{
							$query .= ' OR ';
						}
						$query .= "mnc.long_name like '" . trim(str_replace('*', '%', $onecat)) . "'";
						$count++;
					}
					$query .= ") AND ((" . $db->IfNull('start_time', "'1/1/1900'") . " = '1/1/1900' AND " . $db->IfNull('end_time', "'1/1/1900'") . " = '1/1/1900') OR (start_time < '" . $db->DBTimeStamp(time()) . "'" . " AND end_time > '" . $db->DBTimeStamp(time())."')) ";
				}
				else
				{
					$query = "SELECT mn.*, mnc.news_category_name FROM " . cms_db_prefix() . "module_news mn LEFT OUTER JOIN " . cms_db_prefix() . "module_news_categories mnc ON mnc.news_category_id = mn.news_category_id WHERE status = 'published' AND ((" . $db->IfNull('start_time', "'1/1/1900'") . " = '1/1/1900' AND " . $db->IfNull('end_time', "'1/1/1900'") . " = '1/1/1900') OR (start_time < '" . $db->DBTimeStamp(time()) . "'" . " AND end_time > '" . $db->DBTimeStamp(time())."')) ";
				}

				if (isset($params["sortby"])) 
                { 
                    $query .= "ORDER BY '" . $params['sortby'] . "' "; 
                } 
                else 
                { 
                    $query .= "ORDER BY news_date "; 
                } 

                if (isset($params["sortasc"])) 
                { 
                    $query .= "asc"; 
                } 
                else 
                { 
                    $query .= "desc"; 
                }
                
				$this->log->debug($query);

				$dbresult = '';
				if(isset($params['number']))
				{
					$dbresult = $db->SelectLimit($query, $params["number"]);
				}
				else
				{
					$dbresult = $db->Execute($query);
				}

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $row['news_title'];
					$onerow->content = $row['news_data'];
					$onerow->summary = (trim($row['summary'])!='<br/>'?$row['summary']:'');
					$onerow->postdate = $row['news_date'];
					$onerow->formatpostdate = strftime((isset($params['dateformat']) && $params['dateformat'] != ''?$params['dateformat']:'%b %d, %Y'), $db->UnixTimeStamp($row['news_date']));
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];
					$onerow->category = $row['news_category_name'];

					$moretext = isset($params['moretext'])?$params['moretext']:$this->Lang('more');
					$sendtodetail = array('articleid'=>$row['news_id']);
					if (isset($params['detailtemplate']))
					{
						$sendtodetail['detailtemplate'] = $params['detailtemplate'];
					}

					$onerow->titlelink = $this->CreateLink($id, 'detail', $returnid, $row['news_title'], $sendtodetail,'',false,false,'',true);
					$onerow->morelink = $this->CreateLink($id, 'detail', $returnid, $moretext, $sendtodetail,'',false,false,'',true);
					$sendtoprint = array('articleid'=>$row['news_id'],'showtemplate'=>'false');
					if (isset($params['lang']))
					{
						$sendtodetail['lang'] = $params['lang'];
						$sendtoprint['lang'] = $params['lang'];
					}
					$onerow->printlink = $this->CreateLink($id, 'print', $returnid, $this->Lang('print'), $sendtoprint);

					array_push($entryarray, $onerow);
				}

				$this->smarty->assign_by_ref('items', $entryarray);

				#Display template
				echo "<!-- Displaying News Module -->\n";

				if (isset($params['summarytemplate']))
				{
					echo $this->ProcessTemplate($params['summarytemplate']);
				}
				else
				{
					echo $this->ProcessTemplateFromDatabase('displaysummary');
				}

				break;

			case "detail":

				$query = "SELECT mn.*, mnc.news_category_name FROM ".cms_db_prefix()."module_news mn LEFT OUTER JOIN ".cms_db_prefix()."module_news_categories mnc ON mnc.news_category_id = mn.news_category_id WHERE status = 'published' AND news_id = ?";
				$dbresult = $db->Execute($query, array($params['articleid']));

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $row['news_title'];
					$onerow->content = $row['news_data'];
					$onerow->summary = $row['summary'];
					$onerow->postdate = $row['news_date'];
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];
					$onerow->category = $row['news_category_name'];

					$sendtoprint = array('articleid'=>$row['news_id'],'showtemplate'=>'false');
					if (isset($params['lang']))
					{
						$sendtoprint['lang'] = $params['lang'];
					}
					$onerow->printlink = $this->CreateLink($id, 'print', $returnid, $this->Lang('print'), $sendtoprint);

					$this->smarty->assign_by_ref('entry', $onerow);
				}

				#Display template
				if (isset($params['detailtemplate']))
				{
					echo $this->ProcessTemplate($params['detailtemplate']);
				}
				else
				{
					echo $this->ProcessTemplateFromDatabase('displaydetail');
				}

				break;

			case "print":

				$query = "SELECT mn.*, mnc.news_category_name FROM ".cms_db_prefix()."module_news mn LEFT OUTER JOIN ".cms_db_prefix()."module_news_categories mnc ON mnc.news_category_id = mn.news_category_id WHERE status = 'published' AND news_id = ?";
				$dbresult = $db->Execute($query, array($params['articleid']));

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $row['news_title'];
					$onerow->content = $row['news_data'];
					$onerow->summary = $row['summary'];
					$onerow->postdate = $row['news_date'];
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];
					$onerow->category = $row['news_category_name'];

					$this->smarty->assign_by_ref('entry', $onerow);
				}

				echo $this->ProcessTemplate('articleprint.tpl');

				break;

			case "rss":

				$entryarray = array();

				$query = "SELECT mn.*, mnc.news_category_name FROM ".cms_db_prefix()."module_news mn LEFT OUTER JOIN ".cms_db_prefix()."module_news_categories mnc ON mnc.news_category_id = mn.news_category_id WHERE status = 'published' ORDER by news_date DESC";
				$dbresult = '';
				if(isset($params['number']))
				{
					$dbresult = $db->SelectLimit($query, $params["number"]);
				}
				else
				{
					$dbresult = $db->Execute($query);
				}

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $row['news_title'];
					$onerow->content = $row['news_data'];
					$onerow->summary = $row['summary'];
					$onerow->strippedcontent = strip_tags($onerow->content);
					$onerow->strippedsummary = strip_tags($onerow->summary);
					$onerow->postdate = $row['news_date'];
					$onerow->gmdate = gmdate('D, j M Y H:i:s T', $db->UnixTimeStamp($row['news_date']));
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];
					$onerow->category = $row['news_category_name'];

					array_push($entryarray, $onerow);
				}

				$this->smarty->assign_by_ref('items', $entryarray);

				global $gCms;

				$this->smarty->assign_by_ref('root_url', $gCms->config['root_url']);
				$this->smarty->assign_by_ref('query_var', $gCms->config['query_var']);

				#Display template
				$variables = &$this->cms->variables;
				$variables['content-type'] = 'application/rss+xml';
				$variables['content-filename'] = 'feed.xml';

				echo $this->ProcessTemplate('rssfeed.tpl');

				break;

			case "addcategory":

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				if (isset($params['cancel']))
				{
					$this->Redirect($id, 'defaultadmin', $returnid);
				}

				$name = '';
				if (isset($params['name']))
				{
					$name = $params['name'];
					if ($name != '')
					{
						$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
						$query = 'INSERT INTO '.cms_db_prefix().'module_news_categories (news_category_id, news_category_name, parent_id, create_date, modified_date) VALUES (?,?,?,?,?)';
						$db->Execute($query, array($catid, $name, $params['parent'], $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
						$this->UpdateHierarchyPositions();
						$this->Redirect($id, 'defaultadmin', $returnid);
					}
					else
					{
						//Handle error
					}
				}

				#Display template
				$this->smarty->assign('startform', $this->CreateFormStart($id, 'addcategory', $returnid));
				$this->smarty->assign('endform', $this->CreateFormEnd());
				$this->smarty->assign('nametext', $this->Lang('name'));
				$this->smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
				$this->smarty->assign('parentdropdown', $this->CreateParentDropdown($id, -1, -1));
				$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
				$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
				$this->smarty->assign('parenttext', lang('parent'));
				echo $this->ProcessTemplate('editcategory.tpl');

				break;

			case "editcategory":

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				if (isset($params['cancel']))
				{
					$this->Redirect($id, 'defaultadmin', $returnid);
				}

				$catid = '';
				if (isset($params['catid']))
				{
					$catid = $params['catid'];
				}

				$parentid = '-1';
				if (isset($params['parent']))
				{
					$parentid = $params['parent'];
				}

				$name = '';
				if (isset($params['name']))
				{
					$name = $params['name'];
					if ($name != '')
					{
						$query = 'UPDATE '.cms_db_prefix().'module_news_categories SET news_category_name = ?, parent_id = ?, modified_date = ? WHERE news_category_id = ?';
						$db->Execute($query, array($name, $parentid, $db->DBTimeStamp(time()), $catid));
						$this->UpdateHierarchyPositions();
						$this->Redirect($id, 'defaultadmin', $returnid);
					}
					else
					{
						//Handle error
					}
				}
				else
				{
					$query = 'SELECT * FROM '.cms_db_prefix().'module_news_categories WHERE news_category_id = ?';
					$dbresult = $db->Execute($query, array($catid));

					while (($row = $dbresult->FetchRow()))
					{
						$name = $row['news_category_name'];
						$parentid = $row['parent_id'];
					}
				}

				#Display template
				$this->smarty->assign('startform', $this->CreateFormStart($id, 'editcategory', $returnid));
				$this->smarty->assign('endform', $this->CreateFormEnd());
				$this->smarty->assign('nametext', $this->Lang('name'));
				$this->smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
				$this->smarty->assign('parentdropdown', $this->CreateParentDropdown($id, $catid, $parentid));
				$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'catid', $catid));
				$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
				$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
				$this->smarty->assign('parenttext', lang('parent'));
				echo $this->ProcessTemplate('editcategory.tpl');

				break;

			case "deletecategory":

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				$catid = '';
				if (isset($params['catid']))
				{
					$catid = $params['catid'];
				}

				//Reset all categories using this parent to have no parent (-1)
				$query = 'UPDATE '.cms_db_prefix().'module_news_categories SET parent_id=?, modified_date=? WHERE parent_id=?';
				$db->Execute($query, array(-1, $db->DBTimeStamp(time()), $catid));


				//Now remove the category
				$query = "DELETE FROM ".cms_db_prefix()."module_news_categories WHERE news_category_id = ?";
				$db->Execute($query, array($catid));

				//And remove it from any articles
				$query = "DELETE FROM module_news_article_categories WHERE news_category_id = ?";
				$db->Execute($query, array($catid));

				$this->UpdateHierarchyPositions();
				$this->Redirect($id, 'defaultadmin', $returnid);

				break;

			case "addarticle":

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				if (isset($params['cancel']))
				{
					$this->Redirect($id, 'defaultadmin', $returnid);
				}

				$content = '';
				if (isset($params['content']))
				{
					$content = $params['content'];
				}

				$summary = '';
				if (isset($params['summary']))
				{
					$summary = $params['summary'];
				}

				$status = 'published';
				if (isset($params['status']))
				{
					$status = $params['status'];
				}

				$usedcategory = '';
				if (isset($params['category']))
				{
					$usedcategory = $params['category'];
				}

				$postdate = time();
				if (isset($params['postdate_Month']))
				{
					$postdate = mktime($params['postdate_Hour'], $params['postdate_Minute'], $params['postdate_Second'], $params['postdate_Month'], $params['postdate_Day'], $params['postdate_Year']);
				}

				$useexp = 0;
				if (isset($params['useexp']))
				{
					$useexp = 1;
				}

				$startdate = time();
				if (isset($params['startdate_Month']))
				{
					$startdate = mktime($params['startdate_Hour'], $params['startdate_Minute'], $params['startdate_Second'], $params['startdate_Month'], $params['startdate_Day'], $params['startdate_Year']);
				}
				$enddate = strtotime('+6 months', time());
				if (isset($params['enddate_Month']))
				{
					$enddate = mktime($params['enddate_Hour'], $params['enddate_Minute'], $params['enddate_Second'], $params['enddate_Month'], $params['enddate_Day'], $params['enddate_Year']);
				}

				$title = '';
				if (isset($params['title']))
				{
					$title = $params['title'];
					if ($title != '')
					{
						if ($content != '')
						{
							$articleid = $db->GenID(cms_db_prefix()."module_news_seq");
							$query = 'INSERT INTO '.cms_db_prefix().'module_news (news_id, news_category_id, news_title, news_data, summary, status, news_date, start_time, end_time, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
							if ($useexp == 1)
							{
								$db->Execute($query, array($articleid, $usedcategory, $title, $content, $summary, $status, $db->DBTimeStamp($postdate), $db->DBTimeStamp($startdate), $db->DBTimeStamp($enddate), $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
							}
							else
							{
								$db->Execute($query, array($articleid, $usedcategory, $title, $content, $summary, $status, $db->DBTimeStamp($postdate), NULL, NULL, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
							}

							#foreach ($usedcategories as $onecategory)
							#{
							#	$query = 'INSERT INTO '.cms_db_prefix().'module_news_article_categories (news_category_id, news_id) VALUES (?,?)';
							#	$db->Execute($query, array($usedcategory, $articleid));
							#}

							$this->Redirect($id, 'defaultadmin', $returnid);
						}
						else
						{
							echo '<p class="error">'.$this->Lang('nocontentgiven').'</p>';
						}
					}
					else
					{
						echo '<p class="error">'.$this->Lang('notitlegiven').'</p>';
					}
				}

				$statusdropdown = array();
				$statusdropdown['Draft'] = 'draft';
				$statusdropdown['Published'] = 'published';

				$categorylist = array();
				$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
				$dbresult = $db->Execute($query);

				while ($row = $dbresult->FetchRow())
				{
					$categorylist[$row['long_name']] = $row['news_category_id'];
				}

				#Display template
				$this->smarty->assign('startform', $this->CreateFormStart($id, 'addarticle', $returnid));
				$this->smarty->assign('endform', $this->CreateFormEnd());
				$this->smarty->assign('titletext', $this->Lang('title'));
				$this->smarty->assign('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
				$this->smarty->assign('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
				$this->smarty->assign('inputsummary', $this->CreateTextArea(true, $id, $summary, 'summary', '', '', '', '', '80', '3'));
				$this->smarty->assign_by_ref('postdate', $postdate);
				$this->smarty->assign('postdateprefix', $id.'postdate_');
				$this->smarty->assign('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
				$this->smarty->assign_by_ref('startdate', $startdate);
				$this->smarty->assign('startdateprefix', $id.'startdate_');
				$this->smarty->assign_by_ref('enddate', $enddate);
				$this->smarty->assign('enddateprefix', $id.'enddate_');
				$this->smarty->assign('status', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
				$this->smarty->assign('inputcategory', $this->CreateInputDropdown($id, 'category', $categorylist, -1, $usedcategory));
				$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
				$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

				$this->smarty->assign('titletext', $this->Lang('title'));
				$this->smarty->assign('categorytext', $this->Lang('category'));
				$this->smarty->assign('summarytext', $this->Lang('summary'));
				$this->smarty->assign('contenttext', $this->Lang('content'));
				$this->smarty->assign('postdatetext', $this->Lang('postdate'));
				$this->smarty->assign('statustext', lang('status'));
				$this->smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
				$this->smarty->assign('startdatetext', $this->Lang('startdate'));
				$this->smarty->assign('enddatetext', $this->Lang('enddate'));

				echo $this->ProcessTemplate('editarticle.tpl');

				break;

			case "editarticle":

				if (isset($params['cancel']))
				{
					$this->Redirect($id, 'defaultadmin', $returnid);
				}

				$articleid = '';
				if (isset($params['articleid']))
				{
					$articleid = $params['articleid'];
				}

				$content = '';
				if (isset($params['content']))
				{
					$content = $params['content'];
				}

				$summary = '';
				if (isset($params['summary']))
				{
					$summary = $params['summary'];
				}

				$status = 'draft';
				if (isset($params['status']))
				{
					$status = $params['status'];
				}

				$usedcategory = '';
				if (isset($params['category']))
				{
					$usedcategory = $params['category'];
				}

				$postdate = time();
				if (isset($params['postdate_Month']))
				{
					$postdate = mktime($params['postdate_Hour'], $params['postdate_Minute'], $params['postdate_Second'], $params['postdate_Month'], $params['postdate_Day'], $params['postdate_Year']);
				}
			
				$useexp = 0;
				if (isset($params['useexp']))
				{
					$useexp = 1;
				}

				$startdate = time();
				if (isset($params['startdate_Month']))
				{
					$startdate = mktime($params['startdate_Hour'], $params['startdate_Minute'], $params['startdate_Second'], $params['startdate_Month'], $params['startdate_Day'], $params['startdate_Year']);
				}

				$enddate = strtotime('+6 months', time());
				if (isset($params['enddate_Month']))
				{
					$enddate = mktime($params['enddate_Hour'], $params['enddate_Minute'], $params['enddate_Second'], $params['enddate_Month'], $params['enddate_Day'], $params['enddate_Year']);
				}

				$title = '';
				if (isset($params['title']))
				{
					$title = $params['title'];
					if ($title != '')
					{
						if ($content != '')
						{
							$query = 'UPDATE '.cms_db_prefix().'module_news SET news_title=?, news_data=?, summary=?, status=?, news_date=?, news_category_id=?, start_time=?, end_time=?, modified_date=? WHERE news_id = ?';
							if ($useexp == 1)
							{
								$db->Execute($query, array($title, $content, $summary, $status, $db->DBTimeStamp($postdate), $usedcategory, $db->DBTimeStamp($startdate), $db->DBTimeStamp($enddate), $db->DBTimeStamp(time()), $articleid));
							}
							else
							{
								$db->Execute($query, array($title, $content, $summary, $status, $db->DBTimeStamp($postdate), $usedcategory, NULL, NULL, $db->DBTimeStamp(time()), $articleid));
							}

							$this->Redirect($id, 'defaultadmin', $returnid);
						}
						else
						{
							echo '<p class="error">'.$this->Lang('nocontentgiven').'</p>';
						}
					}
					else
					{
						echo '<p class="error">'.$this->Lang('notitlegiven').'</p>';
					}
				}
				else
				{
					$query = 'SELECT * FROM '.cms_db_prefix().'module_news WHERE news_id = ?';
					$dbresult = $db->Execute($query, array($articleid));

					while (($row = $dbresult->FetchRow()))
					{
						$title = $row['news_title'];
						$content = $row['news_data'];
						$summary = $row['summary'];
						$status = $row['status'];
						$usedcategory = $row['news_category_id'];
						$postdate = $db->UnixTimeStamp($row['news_date']);
						if (isset($row['start_time']))
						{
							$useexp = 1;
							$startdate = $db->UnixTimeStamp($row['start_time']);
							$enddate = $db->UnixTimeStamp($row['end_time']);
						}
						else
						{
							$useexp = 0;
						}
					}
				}

				$statusdropdown = array();
				$statusdropdown['Draft'] = 'draft';
				$statusdropdown['Published'] = 'published';

				$categorylist = array();
				$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
				$dbresult = $db->Execute($query);

				while ($row = $dbresult->FetchRow())
				{
					$categorylist[$row['long_name']] = $row['news_category_id'];
				}

				#Display template
				$this->smarty->assign('startform', $this->CreateFormStart($id, 'editarticle', $returnid));
				$this->smarty->assign('endform', $this->CreateFormEnd());
				$this->smarty->assign('titletext', $this->Lang('title'));
				$this->smarty->assign('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
				$this->smarty->assign('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
				$this->smarty->assign('inputsummary', $this->CreateTextArea(true, $id, $summary, 'summary', '', '', '', '', '80', '3'));
				$this->smarty->assign('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
				$this->smarty->assign_by_ref('postdate', $postdate);
				$this->smarty->assign('postdateprefix', $id.'postdate_');
				$this->smarty->assign_by_ref('startdate', $startdate);
				$this->smarty->assign('startdateprefix', $id.'startdate_');
				$this->smarty->assign_by_ref('enddate', $enddate);
				$this->smarty->assign('enddateprefix', $id.'enddate_');
				$this->smarty->assign('status', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
				$this->smarty->assign('inputcategory', $this->CreateInputDropdown($id, 'category', $categorylist, -1, $usedcategory));
				$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'articleid', $articleid));
				$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
				$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

				$this->smarty->assign('titletext', $this->Lang('title'));
				$this->smarty->assign('categorytext', $this->Lang('category'));
				$this->smarty->assign('summarytext', $this->Lang('summary'));
				$this->smarty->assign('contenttext', $this->Lang('content'));
				$this->smarty->assign('postdatetext', $this->Lang('postdate'));
				$this->smarty->assign('statustext', lang('status'));
				$this->smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
				$this->smarty->assign('startdatetext', $this->Lang('startdate'));
				$this->smarty->assign('enddatetext', $this->Lang('enddate'));

				echo $this->ProcessTemplate('editarticle.tpl');

				break;

			case "deletearticle":

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				$articleid = '';
				if (isset($params['articleid']))
				{
					$articleid = $params['articleid'];
				}

				//Now remove the article
				$query = "DELETE FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
				$db->Execute($query, array($articleid));

				//And remove it from any categories
				$query = "DELETE FROM module_news_article_categories WHERE news_id = ?";
				$db->Execute($query, array($articleid));

				$this->Redirect($id, 'defaultadmin', $returnid);

				break;

			case "updatesummarytemplate":

				$this->SetTemplate('displaysummary', $params['templatecontent']);
				$this->Redirect($id, 'defaultadmin');

				break;

			case "updatedetailtemplate":

				$this->SetTemplate('displaydetail', $params['templatecontent2']);
				$this->Redirect($id, 'defaultadmin');

				break;

			case "defaultadmin":

				global $gCms;

				if (!$this->CheckPermission('Modify News'))
				{
					echo '<p class="error">'.$this->Lang('needpermission', array('Modify News')).'</p>';
					return;
				}

				#The tabs
				echo $this->StartTabHeaders();

				echo $this->SetTabHeader('articles',$this->Lang('articles'));
				echo $this->SetTabHeader('categories',$this->Lang('categories'));
				echo $this->SetTabHeader('summary_template',$this->Lang('summarytemplate'));
				echo $this->SetTabHeader('detail_template',$this->Lang('detailtemplate'));

				echo $this->EndTabHeaders();

				#The content of the tabs
				echo $this->StartTabContent();

				echo $this->StartTab("articles");

				echo $this->CreateFormStart($id, 'defaultadmin');

				$curcategory = (isset($params['curcategory'])?$params['curcategory']:'');
				$allcategories = (isset($params['allcategories'])?$params['allcategories']:'no');
				$newcategory = $curcategory;
				
				if (isset($params['submitcategory']))
				{
					$newcategory = (isset($params['newcategory'])?$params['newcategory']:$newcategory);
				}
				
				$curcategory = $newcategory;

				$categorylist = array();
				$categorylist[$this->Lang('allcategories')] = '';
				$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
				$dbresult = $db->Execute($query);

				while ($row = $dbresult->FetchRow())
				{
					$categorylist[$row['long_name']] = $row['long_name'];
				}

				echo '<br /><p>'.$this->Lang('category').': ' . $this->CreateInputDropdown($id, 'newcategory', $categorylist, -1, $newcategory) . ' ' . $this->Lang('showchildcategories') . ': ' . $this->CreateInputCheckbox($id, 'allcategories', 'yes', $allcategories) . ' ' . $this->CreateInputSubmit($id, 'submitcategory', $this->Lang('selectcategory')) . $this->CreateInputHidden($id, 'curcategory', $curcategory) . '</p>';

				echo $this->CreateFormEnd();

				//Load the current articles
				$entryarray = array();

				$query = '';
				$dbresult = '';

				if ($curcategory != '')
				{
					$query = "SELECT n.*, nc.long_name FROM ".cms_db_prefix()."module_news n LEFT OUTER JOIN ".cms_db_prefix()."module_news_categories nc ON n.news_category_id = nc.news_category_id WHERE nc.long_name LIKE ? ORDER by news_date DESC";
					if ($allcategories == 'yes')
					{
						$dbresult = $db->Execute($query,array($curcategory . '%'));
					}
					else
					{
						$dbresult = $db->Execute($query,array($curcategory));
					}
				}
				else
				{
					$query = "SELECT n.*, nc.long_name FROM ".cms_db_prefix()."module_news n LEFT OUTER JOIN ".cms_db_prefix()."module_news_categories nc ON n.news_category_id = nc.news_category_id ORDER by news_date DESC";
					$dbresult = $db->Execute($query);
				}

				$rowclass = 'row1';

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $this->CreateLink($id, 'editarticle', $returnid, $row['news_title'], array('articleid'=>$row['news_id']));
					$onerow->data = $row['news_data'];
					$onerow->postdate = $row['news_date'];
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];
					$onerow->category = $row['long_name'];
					$onerow->rowclass = $rowclass;

					$onerow->editlink = $this->CreateLink($id, 'editarticle', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('articleid'=>$row['news_id']));
					$onerow->deletelink = $this->CreateLink($id, 'deletearticle', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('articleid'=>$row['news_id']), $this->Lang('areyousure'));

					array_push($entryarray, $onerow);

					($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
				}

				$this->smarty->assign_by_ref('items', $entryarray);
				$this->smarty->assign('itemcount', count($entryarray));

				$this->smarty->assign('addlink', $this->CreateLink($id, 'addarticle', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('addarticle'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'addarticle', $returnid, $this->Lang('addarticle'), array(), '', false, false, 'class="pageoptions"'));

				$this->smarty->assign('titletext', $this->Lang('title'));
				$this->smarty->assign('postdatetext', $this->Lang('postdate'));
				$this->smarty->assign('categorytext', $this->Lang('category'));

				#Display template
				echo $this->ProcessTemplate('articlelist.tpl');

				echo $this->EndTab();

				echo $this->StartTab("categories");
				
				#Put together a list of current categories...
				$entryarray = array();

				$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
				$dbresult = $db->Execute($query);

				$rowclass = 'row1';

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$depth = count(split('\.', $row['hierarchy']));

					$onerow->id = $row['news_category_id'];
					$onerow->name = str_repeat('&nbsp;', $depth-1).$this->CreateLink($id, 'editcategory', $returnid, $row['news_category_name'], array('catid'=>$row['news_category_id']));

					$onerow->editlink = $this->CreateLink($id, 'editcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('catid'=>$row['news_category_id']));
					$onerow->deletelink = $this->CreateLink($id, 'deletecategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('catid'=>$row['news_category_id']), $this->Lang('areyousure'));

					$onerow->rowclass = $rowclass;

					array_push($entryarray, $onerow);

					($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
				}

				$this->smarty->assign_by_ref('items', $entryarray);
				$this->smarty->assign('itemcount', count($entryarray));

				#Setup links
				$this->smarty->assign('addlink', $this->CreateLink($id, 'addcategory', $returnid, $this->Lang('addcategory'), array(), '', false, false, 'class="pageoptions"'));
				$this->smarty->assign('addlink', $this->CreateLink($id, 'addcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newfolder.gif', $this->Lang('addcategory'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'addcategory', $returnid, $this->Lang('addcategory'), array(), '', false, false, 'class="pageoptions"'));

				$this->smarty->assign('categorytext', $this->Lang('category'));

				#Display template
				echo $this->ProcessTemplate('categorylist.tpl');

				echo $this->EndTab();

				echo $this->StartTab('summary_template');
				
				echo $this->CreateFormStart($id, 'updatesummarytemplate');

				echo '<p>'.$this->CreateTextArea(false, $id, $this->GetTemplate('displaysummary'), 'templatecontent', '').'</p>';

				echo $this->CreateInputSubmit($id, 'submitbutton', $this->Lang('submit'));

				echo $this->CreateFormEnd();

				echo $this->EndTab();

				echo $this->StartTab('detail_template');
				
				echo $this->CreateFormStart($id, 'updatedetailtemplate');

				echo '<p>'.$this->CreateTextArea(false, $id, $this->GetTemplate('displaydetail'), 'templatecontent2', '').'</p>';

				echo $this->CreateInputSubmit($id, 'rsssubmitbutton', $this->Lang('submit'));

				echo $this->CreateFormEnd();

				echo $this->EndTab();

				echo $this->EndTabContent();
				
				break;
		}
	}

	function GetHelp($lang='en_US')
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
		?>
		<ul>
		<li>
		<p>Version: 1.0</p>
		<p>This module is a hacked and extended version of <em>Ted Kulp's</em> News module.  I simply added another field to the database, and some more code to make that field worl.... I also re-cleaned the code a bit, so it was a little easier to read, other than that, it's Ted's code.</p>
		</li>
		<li>
		<p>Version: 1.1</p>
		<p>Added the ability to set an automatic expiry date from a pulldown, moved the category selection, and on the main page you now filter the entries you want to see.</p>
		</li>
		<li>
		<p>Version: 1.2</p>
		<p>Added summary, no_anchor and length parameters.  In summary mode links are made to the real articles, tags are stripped, and links are insreted to the news page and the specific news item.</p>
		</li>
		<li>
		<p>Version: 1.3</p>
		<p>Minor cosmetic changes</p>
		</li>
		<li>
		<p>Version 1.5</p>
		<p>Merged into the trunk News module</p>
        </li>
		<li>
		<p>Version 1.6</p>
		<p>Added pagination, and moved the add button to the top (calguy)</p>
        </li>
		</ul>
		<?php
	}
}

class NewsModule extends CMSModuleContentType
{
	function SetProperties()
	{
		$this->mProperties->Add('string', 'number', '');
		$this->mProperties->Add('string', 'dateformat', 'F j, Y, g:i a');
		$this->mProperties->Add('string', 'category');
		$this->mProperties->Add('string', 'moretext', 'more...');
		$this->mProperties->Add('bool', 'sortasc', 0);

		#Turn on preview
		$this->mPreview = true;

		#Turn off caching
		$this->mCachable = false;
	}

	function EditAsArray($adding = false)
	{
		global $gCms;
		$config = $gCms->config;

		$ret = array();

		array_push($ret,array(lang('title').':','<input type="text" name="title" value="'.$this->mName.'">'));
		array_push($ret,array(lang('menutext').':','<input type="text" name="menutext" value="'.$this->mMenuText.'">'));
		#if (!($config['auto_alias_content'] == true && $this->mAlias == ''))
		if (!($config['auto_alias_content'] == true && $adding))
		{
			array_push($ret,array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->mAlias.'">'));
		}
		array_push($ret,array(lang('template').':',TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId)));
		array_push($ret,array($this->Lang('numbertodisplay').':','<input type="text" name="number" value="'.$this->GetPropertyValue('number').'" />'));
		array_push($ret,array($this->Lang('category').':','<input type="text" name="category" value="'.$this->GetPropertyValue('category').'" />'));
		array_push($ret,array($this->Lang('moretext').':','<input type="text" name="moretext" value="'.$this->GetPropertyValue('moretext').'" />'));
		array_push($ret,array($this->Lang('sortascending').':','<input type="checkbox" name="sortasc" '.($this->GetPropertyValue('sortasc')?' checked="true"':'').' />'));
		array_push($ret,array(lang('active').':','<input type="checkbox" name="active"'.($this->mActive?' checked="true"':'').'>'));
		array_push($ret,array(lang('showinmenu').':','<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="true"':'').'>'));
		array_push($ret,array(lang('parent').':',ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId)));

		return $ret;
	}


	function Edit($adding = false)
	{
		global $gCms;
		$config = $gCms->config;

		$text = '';

		$text .= '<tr><td>'.lang('title').':</td><td><input type="text" name="title" value="'.$this->mName.'"></td></tr>';
		$text .= '<tr><td>'.lang('menutext').':</td><td><input type="text" name="menutext" value="'.$this->mMenuText.'"></td></tr>';
		#if (!($config['auto_alias_content'] == true && $this->mAlias == ''))
		if (!($config['auto_alias_content'] == true && $adding))
		{
			$text .= '<tr><td>'.lang('pagealias').':</td><td><input type="text" name="alias" value="'.$this->mAlias.'"></td></tr>';
		}
		$text .= '<tr><td>'.lang('template').':</td><td>'.TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId).'</td></tr>';
		$text .= '<tr><td>'.$this->Lang('numbertodisplay').':</td><td><input type="text" name="number" value="'.$this->GetPropertyValue('number').'" /></td></tr>';
		$text .= '<tr><td>'.$this->Lang('category').':</td><td><input type="text" name="category" value="'.$this->GetPropertyValue('category').'" /></td></tr>';
		$text .= '<tr><td>'.$this->Lang('moretext').':</td><td><input type="text" name="moretext" value="'.$this->GetPropertyValue('moretext').'" /></td></tr>';
		$text .= '<tr><td>'.$this->Lang('sortascending').':</td><td><input type="checkbox" name="sortasc" '.($this->GetPropertyValue('sortasc')?' checked="true"':'').' /></td></tr>';
		$text .= '<tr><td>'.lang('active').':</td><td><input type="checkbox" name="active"'.($this->mActive?' checked="true"':'').'></td></tr>';
		$text .= '<tr><td>'.lang('showinmenu').':</td><td><input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="true"':'').'></td></tr>';
		$text .= '<tr><td>'.lang('parent').':</td><td>'.ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId).'</td></tr>';

		return $text;
	}

	function FillParams(&$params)
	{
		global $gCms;
		$config = $gCms->config;

		if (isset($params))
		{
			$parameters = array('number', 'category', 'moretext');
			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->SetPropertyValue($oneparam, $params[$oneparam]);
				}
			}
			if (isset($params['sortasc']) && $params['sortasc'] == 1)
			{
				$this->SetPropertyValue('sortasc', 1);
			}
			else
			{
				$this->SetPropertyValue('sortasc', 0);
			}
			if (isset($params['title']))
			{
				$this->mName = $params['title'];
			}
			if (isset($params['menutext']))
			{
				$this->mMenuText = $params['menutext'];
			}
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			else
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

	function Show()
	{
		global $gCms;
		$variables = &$gCms->variables;

		$params = array();

		if (strlen($this->GetPropertyValue('number')) > 0)
		{
			$params['number'] = $this->GetPropertyValue('number');
		}

		if (strlen($this->GetPropertyValue('category')) > 0)
		{
			$params['category'] = $this->GetPropertyValue('category');
		}

		$params['moretext'] = $this->GetPropertyValue('moretext');
		$params['sortasc'] = $this->GetPropertyValue('sortasc');

		$newnews = new News();

		//Buffer all this crap spit out by the News module and return it
		@ob_start();
		$newnews->DoAction('default', 'newsmodule', $params, $variables['page_id']);
		$text .= @ob_get_contents();
		@ob_end_clean();
		return $text;
	}

	function GetURL()
	{
		global $gCms;
		$config = $gCms->config;

		$url = "";

		if ($config["assume_mod_rewrite"])
		{
			// we could use $this->mId instead of mAlias but i think it's better
			$url = $config["root_url"]."/".$this->mAlias . $config["page_extension"];
		}
		else 
		{
			$url = $config["root_url"]."/index.php?".$config["query_var"]."=". $this->mAlias;
		}

		return $url;
	}

    function FriendlyName()
	{
		return 'News';
	}

	function ModuleName()
	{
		return 'News';
	}

	function IsDefaultPossible()
	{
		return TRUE;
	}

}

# vim:ts=4 sw=4 noet
?>
