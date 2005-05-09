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

	/*
    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify News');
    }
	*/

	/**
	 * This function is not in the API!!!
	 */
	function GetDisplayHtmlTemplate()
	{
		return '
<!-- Start News Display Template -->
{foreach from=$items item=entry}
<span class="cms-module-news">
<span class="cms-module-news-header">
{if !($params.swaptitledate == \'true\' || $params.swaptitledate == \'1\')}
        <span class="cms-news-date">{$entry->date}</span><br />
{/if}
<span class="cms-news-title">
{if !($params.no_anchors == \'true\' || $params.no_anchors == \'1\')}
<a name="{$entry->id}"></a>
{/if}
{$entry->title}</span><br />
{if ($params.swaptitledate == \'true\' || $params.swaptitledate == \'1\')}
        <span class="cms-news-date">{$entry->date}</span><br />
{/if}
</span> <!-- end cms_module-news-header -->
<span class="cms-news-content">{$entry->data}</span>
{if $params.summary != \'\'}
        <br />
        <a href="index.php?{$query_var}={$params.summary}#{$entry->id}">{$entry->moretext}</a>
{/if}
</span> <!-- end cms-module-news -->
{/foreach}
<!-- End News Display Template -->';
	}

	function GetDisplayRSSTemplate()
	{
		return '
<?xml version=\'1.0\'?>
<rss version=\'2.0\'>
	<channel>
		<title>CMS Made Simple News Feed</title>
		<link>{$root_url}</link>
		<description>Current News entries</description>
{foreach from=$items item=entry}
		<item>
			<title>{$entry->title}</title>
			<pubdate>{$entry->gmdate}</pubdate>
			<description>{$entry->data}</description>
		</item>
{/foreach}
	</channel>
</rss>
		';
	}

	function Install()
	{
		$db = $this->cms->db;

		$dict = NewDataDictionary($db);
		$flds = "
			news_id I KEY,
			news_cat C(255),
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

		$flds = "
			news_category_id I KEY,
			news_id I KEY
		";

		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_article_categories", 
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		#Set Permission
		$this->CreatePermission('Modify News', 'Modify News');

		# Setup display template
		$this->SetTemplate('displayhtml', $this->GetDisplayHtmlTemplate());

		# Setup RSS template
		$this->SetTemplate('displayrss', $this->GetDisplayRSSTemplate());
	}

	function InstallPostMessage()
	{
		return lang('postinstall');
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
				$this->SetTemplate('displayhtml', $this->GetDisplayHtmlTemplate());
				$this->SetTemplate('displayrss', $this->GetDisplayRSSTemplate());
				$current_version = "1.7";
			case '1.7':
				#Makey new tables....

				$db = $this->cms->db;

				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "status C(25)");
				$dict->ExecuteSQLArray($sqlarray);

				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "summary X");
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

				$flds = "
					news_category_id I KEY,
					news_id I KEY
				";

				$taboptarray = array('mysql' => 'TYPE=MyISAM');
				$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_article_categories", 
						$flds, $taboptarray);
				$dict->ExecuteSQLArray($sqlarray);

				$query = "SELECT DISTINCT news_cat FROM ".cms_db_prefix()."module_news WHERE news_cat IS NOT NULL";
				$dbresult = $db->Execute($query);
				while ($row = $dbresult->FetchRow())
				{
					$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
					$query = "INSERT INTO ".cms_db_prefix()."module_news_categories (news_category_id, news_category_name, parent_id, hierarchy, long_name, create_date, modified_date) VALUES (?,?,?,?,?,?,?)";
					$db->Execute($query,array($catid, $row['news_cat'], -1, '', '', $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
				}

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

		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_article_categories" );
		$dict->ExecuteSQLArray($sqlarray);

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
		debug_buffer($action);
		debug_buffer($params);
		switch ($action)
		{
			case "default":

				$entryarray = array();

				$query = "SELECT * FROM ".cms_db_prefix()."module_news ORDER by news_date DESC";
				$dbresult = $db->Execute($query);

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

					$onerow->titlelink = $this->CreateLink($id, 'detail', $returnid, $row['news_title'], array('articleid'=>$row['news_id']));
					$onerow->morelink = $this->CreateLink($id, 'detail', $returnid, 'more', array('articleid'=>$row['news_id']));

					array_push($entryarray, $onerow);
				}

				$this->smarty->assign_by_ref('items', $entryarray);

				#Display template
				echo $this->ProcessTemplate('articlesummary.tpl');
				break;

			case "detail":

				$query = 'SELECT * FROM '.cms_db_prefix().'module_news WHERE news_id = ?';
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

					$this->smarty->assign_by_ref('entry', $onerow);
				}

				echo $this->ProcessTemplate('articledetail.tpl');

				break;

			case "addcategory":

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
				$this->smarty->assign_by_ref('startform', $this->CreateFormStart($id, 'addcategory', $returnid));
				$this->smarty->assign_by_ref('endform', $this->CreateFormEnd());
				$this->smarty->assign_by_ref('nametext', $this->Lang('name'));
				$this->smarty->assign_by_ref('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
				$this->smarty->assign_by_ref('parentdropdown', $this->CreateParentDropdown($id, -1, -1));
				$this->smarty->assign_by_ref('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
				$this->smarty->assign_by_ref('cancel', $this->CreateInputSubmit($id, 'cancel', 'Cancel'));
				echo $this->ProcessTemplate('editcategory.tpl');

				break;

			case "editcategory":

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
				$this->smarty->assign_by_ref('startform', $this->CreateFormStart($id, 'editcategory', $returnid));
				$this->smarty->assign_by_ref('endform', $this->CreateFormEnd());
				$this->smarty->assign_by_ref('nametext', $this->Lang('name'));
				$this->smarty->assign_by_ref('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
				$this->smarty->assign_by_ref('parentdropdown', $this->CreateParentDropdown($id, $catid, $parentid));
				$this->smarty->assign_by_ref('hidden', $this->CreateInputHidden($id, 'catid', $catid));
				$this->smarty->assign_by_ref('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
				$this->smarty->assign_by_ref('cancel', $this->CreateInputSubmit($id, 'cancel', 'Cancel'));
				echo $this->ProcessTemplate('editcategory.tpl');

				break;

			case "deletecategory":

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

				$status = 'draft';
				if (isset($params['status']))
				{
					$status = $params['status'];
				}

				$usedcategories = array();
				if (isset($params['categories']))
				{
					$usedcategories = $params['categories'];
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
						$articleid = $db->GenID(cms_db_prefix()."module_news_seq");
						$query = 'INSERT INTO '.cms_db_prefix().'module_news (news_id, news_title, news_data, summary, status, news_date, start_time, end_time, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?)';
						if ($useexp == 1)
						{
							$db->Execute($query, array($articleid, $title, $content, $summary, $status, $db->DBTimeStamp($postdate), $db->DBTimeStamp($startdate), $db->DBTimeStamp($enddate), $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
						}
						else
						{
							$db->Execute($query, array($articleid, $title, $content, $summary, $status, $db->DBTimeStamp($postdate), NULL, NULL, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
						}

						foreach ($usedcategories as $onecategory)
						{
							$query = 'INSERT INTO '.cms_db_prefix().'module_news_article_categories (news_category_id, news_id) VALUES (?,?)';
							$db->Execute($query, array($onecategory, $articleid));
						}

						$this->Redirect($id, 'defaultadmin', $returnid);
					}
					else
					{
						//Handle error
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
				$this->smarty->assign_by_ref('startform', $this->CreateFormStart($id, 'addarticle', $returnid));
				$this->smarty->assign_by_ref('endform', $this->CreateFormEnd());
				$this->smarty->assign_by_ref('titletext', $this->Lang('title'));
				$this->smarty->assign_by_ref('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
				$this->smarty->assign_by_ref('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
				$this->smarty->assign_by_ref('inputsummary', $this->CreateTextArea(true, $id, $summary, 'summary', '', '', '', '', '80', '3'));
				$this->smarty->assign_by_ref('postdate', $postdate);
				$this->smarty->assign('postdateprefix', $id.'postdate_');
				$this->smarty->assign_by_ref('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp));
				$this->smarty->assign_by_ref('startdate', $startdate);
				$this->smarty->assign('startdateprefix', $id.'startdate_');
				$this->smarty->assign_by_ref('enddate', $enddate);
				$this->smarty->assign('enddateprefix', $id.'enddate_');
				$this->smarty->assign_by_ref('status', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
				$this->smarty->assign_by_ref('inputcategory', $this->CreateInputSelectList($id, 'categories[]', $categorylist, $usedcategories));
				$this->smarty->assign_by_ref('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
				$this->smarty->assign_by_ref('cancel', $this->CreateInputSubmit($id, 'cancel', 'Cancel'));
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

				$usedcategories = array();
				if (isset($params['categories']))
				{
					$usedcategories = $params['categories'];
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
						$query = 'UPDATE '.cms_db_prefix().'module_news SET news_title=?, news_data=?, summary=?, status=?, news_date=?, start_time=?, end_time=?, modified_date=? WHERE news_id = ?';
						if ($useexp == 1)
						{
							$db->Execute($query, array($title, $content, $summary, $status, $db->DBTimeStamp($postdate), $db->DBTimeStamp($startdate), $db->DBTimeStamp($enddate), $db->DBTimeStamp(time()), $articleid));
						}
						else
						{
							$db->Execute($query, array($title, $content, $summary, $status, $db->DBTimeStamp($postdate), NULL, NULL, $db->DBTimeStamp(time()), $articleid));
						}

						$query = 'DELETE FROM '.cms_db_prefix().'module_news_article_categories WHERE news_id = ?';
						$db->Execute($query, array($articleid));

						foreach ($usedcategories as $onecategory)
						{
							$query = 'INSERT INTO '.cms_db_prefix().'module_news_article_categories (news_category_id, news_id) VALUES (?,?)';
							$db->Execute($query, array($onecategory, $articleid));
						}

						$this->Redirect($id, 'defaultadmin', $returnid);
					}
					else
					{
						//Handle error
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

					$query = "SELECT * FROM ".cms_db_prefix()."module_news_article_categories WHERE news_id = ?";
					$dbresult = $db->Execute($query, array($articleid));

					while ($row = $dbresult->FetchRow())
					{
						array_push($usedcategories, $row['news_category_id']);
					}
					debug_buffer($usedcategories);
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
				$this->smarty->assign_by_ref('startform', $this->CreateFormStart($id, 'editarticle', $returnid));
				$this->smarty->assign_by_ref('endform', $this->CreateFormEnd());
				$this->smarty->assign_by_ref('titletext', $this->Lang('title'));
				$this->smarty->assign_by_ref('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
				$this->smarty->assign_by_ref('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
				$this->smarty->assign_by_ref('inputsummary', $this->CreateTextArea(true, $id, $summary, 'summary', '', '', '', '', '80', '3'));
				$this->smarty->assign_by_ref('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp));
				$this->smarty->assign_by_ref('postdate', $postdate);
				$this->smarty->assign('postdateprefix', $id.'postdate_');
				$this->smarty->assign_by_ref('startdate', $startdate);
				$this->smarty->assign('startdateprefix', $id.'startdate_');
				$this->smarty->assign_by_ref('enddate', $enddate);
				$this->smarty->assign('enddateprefix', $id.'enddate_');
				$this->smarty->assign_by_ref('status', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
				debug_buffer($categorylist);
				debug_buffer($usedcategories);
				$this->smarty->assign_by_ref('inputcategory', $this->CreateInputSelectList($id, 'categories[]', $categorylist, $usedcategories));
				$this->smarty->assign_by_ref('hidden', $this->CreateInputHidden($id, 'articleid', $articleid));
				$this->smarty->assign_by_ref('submit', $this->CreateInputSubmit($id, 'submit', 'Submit'));
				$this->smarty->assign_by_ref('cancel', $this->CreateInputSubmit($id, 'cancel', 'Cancel'));
				echo $this->ProcessTemplate('editarticle.tpl');

				break;

			case "deletearticle":

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

			case "defaultadmin":

				echo $this->StartTabSet();

				echo $this->StartTab($this->Lang('articles'));

				//Load the current articles
				$entryarray = array();

				$query = "SELECT * FROM ".cms_db_prefix()."module_news ORDER by news_date DESC";
				$dbresult = $db->Execute($query);

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$onerow->id = $row['news_id'];
					$onerow->title = $row['news_title'];
					$onerow->data = $row['news_data'];
					$onerow->postdate = $row['news_date'];
					$onerow->startdate = $row['start_time'];
					$onerow->enddate = $row['end_time'];

					$onerow->editlink = $this->CreateLink($id, 'editarticle', $returnid, $this->Lang('edit'), array('articleid'=>$row['news_id']));
					$onerow->deletelink = $this->CreateLink($id, 'deletearticle', $returnid, $this->Lang('delete'), array('articleid'=>$row['news_id']), $this->Lang('areyousure'));

					array_push($entryarray, $onerow);
				}

				$this->smarty->assign_by_ref('items', $entryarray);

				$this->smarty->assign_by_ref('addlink', $this->CreateLink($id, 'addarticle', $returnid, $this->Lang('addarticle')));

				#Display template
				echo $this->ProcessTemplate('articlelist.tpl');

				echo $this->EndTab();

				echo $this->StartTab($this->Lang('categories'));

				#Put together a list of current categories...
				$entryarray = array();

				$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
				$dbresult = $db->Execute($query);

				while ($row = $dbresult->FetchRow())
				{
					$onerow = new stdClass();

					$depth = count(split('\.', $row['hierarchy']));
					debug_buffer($row['hierarchy']);

					$onerow->id = $row['news_category_id'];
					$onerow->name = str_repeat('&nbsp;', $depth-1).$row['news_category_name'];
					$onerow->editlink = $this->CreateLink($id, 'editcategory', $returnid, $this->Lang('edit'), array('catid'=>$row['news_category_id']));
					$onerow->deletelink = $this->CreateLink($id, 'deletecategory', $returnid, $this->Lang('delete'), array('catid'=>$row['news_category_id']), $this->Lang('areyousure'));

					array_push($entryarray, $onerow);
				}

				$this->smarty->assign_by_ref('items', $entryarray);

				#Setup links
				$this->smarty->assign_by_ref('addlink', $this->CreateLink($id, 'addcategory', $returnid, $this->Lang('addcategory')));

				#Display template
				echo $this->ProcessTemplate('categorylist.tpl');

				echo $this->EndTab();

				echo $this->EndTabSet();

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
		$this->mProperties->Add('bool', 'swaptitledate', 1);
		$this->mProperties->Add('string', 'category');
		$this->mProperties->Add('string', 'summary');
		$this->mProperties->Add('int', 'length', 80);
		$this->mProperties->Add('bool', 'showcategorywithtitle', 1);
		$this->mProperties->Add('string', 'moretext', 'more...');
		$this->mProperties->Add('bool', 'sortasc', 0);

		#Turn on preview
		$this->mPreview = true;

		#Turn off caching
		$this->mCachable = false;
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
		$text .= '<tr><td>Number to Display (none show all):</td><td><input type="text" name="number" value="'.$this->GetPropertyValue('number').'" /></td></tr>';
		$text .= '<tr><td>Date Format:</td><td><input type="text" name="dateformat" value="'.$this->GetPropertyValue('dateformat').'" /></td></tr>';
		$text .= '<tr><td>Swap Date and Title:</td><td><input type="checkbox" name="swaptitledate" '.($this->GetPropertyValue('swaptitledate')?' checked="true"':'').' /></td></tr>';
		$text .= '<tr><td>Category:</td><td><input type="text" name="category" value="'.$this->GetPropertyValue('category').'" /></td></tr>';
		$text .= '<tr><td>Summary:</td><td><input type="text" name="summary" value="'.$this->GetPropertyValue('summary').'" /></td></tr>';
		$text .= '<tr><td>Summary Length:</td><td><input type="text" name="length" value="'.$this->GetPropertyValue('length').'" /></td></tr>';
		$text .= '<tr><td>Show Category:</td><td><input type="checkbox" name="showcategorywithtitle" '.($this->GetPropertyValue('showcategorywithtitle')?' checked="true"':'').' /></td></tr>';
		$text .= '<tr><td>More Text:</td><td><input type="text" name="moretext" value="'.$this->GetPropertyValue('moretext').'" /></td></tr>';
		$text .= '<tr><td>Sort Ascending:</td><td><input type="checkbox" name="sortasc" '.($this->GetPropertyValue('sortasc')?' checked="true"':'').' /></td></tr>';
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
			$parameters = array('number', 'category', 'summary', 'length', 'moretext', 'dateformat');
			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->SetPropertyValue($oneparam, $params[$oneparam]);
				}
			}
			if (isset($params['swaptitledate']))
			{
				$this->SetPropertyValue('swaptitledate', 1);
			}
			else
			{
				$this->SetPropertyValue('swaptitledate', 0);
			}
			if (isset($params['showcategorywithtitle']))
			{
				$this->SetPropertyValue('showcategorywithtitle', 1);
			}
			else
			{
				$this->SetPropertyValue('showcategorywithtitle', 0);
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

		$params = array();

		if (strlen($params['number']) > 0)
		{
			$params['number'] = $this->GetPropertyValue('number');
		}

		if (strlen($params['category']) > 0)
		{
			$params['category'] = $this->GetPropertyValue('category');
		}

		if (strlen($params['summary']) > 0)
		{
			$params['summary'] = $this->GetPropertyValue('summary');
		}

		if (strlen($params['length']) > 0)
		{
			$params['length'] = $this->GetPropertyValue('length');
		}

		$params['moretext'] = $this->GetPropertyValue('moretext');
		$params['showcategorywithtitle'] = $this->GetPropertyValue('showcategorywithtitle');
		$params['swaptitledate'] = $this->GetPropertyValue('swaptitledate');
		$params['sortasc'] = $this->GetPropertyValue('sortasc');

		$newnews = new News();

		//Buffer all this crap spit out by the News module and return it
		@ob_start();
		$newnews->DoAction('default', 'newsmodule', $params);
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

	function IsDefaultPossible()
	{
		return TRUE;
	}

}

# vim:ts=4 sw=4 noet
?>
