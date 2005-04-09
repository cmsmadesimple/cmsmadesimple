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
		return '1.7';
	}

	function GetAdminDescription()
	{
		return 'Add, edit and remove News entries';
	}

	function GetAdminSection()
	{
		return 'content';
	}

    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify News');
    }

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
<span class="cms-news-title"><a name="{$entry->id}"></a>{$entry->title}</span><br />
{if ($params.swaptitledate == \'true\' || $params.swaptitledate == \'1\')}
        <span class="cms-news-date">{$entry->date}</span><br />
{/if}
</span> <!-- end cms_module-news-header -->
<span class="cms-news-content">{$entry->data}</span>
{if $entry->moretext != \'\'}
        <br />
        <a href="index.php?page="{$params.summary}#{$entry->id}">{$entry->moretext}</a>
{/if}
</span> <!-- end cms-module-news -->
{/foreach}
<!-- End News Display Template -->';
	}

	function GetDisplayRSSTemplate()
	{
		return '
<?xml version='1.0'?>
<rss version='2.0'>
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
			start_time T,
			end_time T,
			icon C(255),
			create_date T,
			modified_date T
		";

		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news", 
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_news_seq");
		$this->CreatePermission('Modify News', 'Modify News');

		# Setup display template
		$this->SetTemplate('displayhtml', $this->GetDisplayHtmlTemplate());

		# Setup RSS template
		$this->SetTemplate('displayrss', $this->GetDisplayRSSTemplate());
	}

	function InstallPostMessage()
	{
		return 'Make sure to set the "Modify News" permission on users who will be administering News items.';
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
		}
	}

	function Uninstall()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary( $db );
		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news" );
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence( cms_db_prefix()."module_news_seq" );
		$this->RemovePermission('Modify News');
	}

	/**
	 * This is NOT overriding a CMSModule function.  It's just used internallly.
	 */
	function StripToLength($str, $len, $tags=true)
	{
		if ($tags == true)
		{
			while($str != strip_tags($str))
			{
				$str = strip_tags($str);
			}
		}

		if ($len > 0 && $len < strlen($str)) $str=substr($str,0,$len)."...";
		return $str;
	}

	/**
	 * This is NOT overriding a CMSModule function.  It's just used internallly for displaying the form, since it's used in so many places.
	 */
	function AdminForm($id, $moduleaction, $error='', $data='', $title='', $post_date='', $start_date='', $end_date='', $news_cat='', $newsid = '', $expiry='6 Months')
	{
		if ( $error != "" )
		{
		  echo "<ul class=\"error\">$error</ul>";
		} 

		$db = $this->cms->db;
		?>

		<div class="AdminForm">
		 
		<?php echo $this->CreateFormStart($id, $moduleaction)?>

		<table width="100%" border="0">
				<?php
			  $query = "SELECT news_cat FROM "
				 .cms_db_prefix()."module_news WHERE news_cat <> '' GROUP BY news_cat";
			  $dbresult = $db->Execute($query);
				  if( $dbresult && $dbresult->RowCount() > 0)
				  {
			  ?>
			  <tr>
				<th width="100">Category:</th>
				<td>
				<select name="<?php echo $id?>selcat" size="4">
				<?php
				while( $row = $dbresult->FetchRow() )
					{
				   if( strlen( $row["news_cat"] ) > 0 ) 
					   {
					 if( $row["news_cat"] == $news_cat )
						 {
						   echo "<option selected=\"selected\">".$row["news_cat"]."</option>\n";
						 }
						 else
						 {
						   echo "<option>".$row["news_cat"]."</option>\n";
						 }
					   }
					}
				?>
				</select>
				</td>
			  </tr>
			  <?php
				  }
				?>
		  <tr>
			 <th>New Category:</th>
			 <td><?php echo $this->CreateInputText($id, 'addcat', '', '40', '255')?></td>
		  </tr>
		  <tr>
			<th width="60">Title:</th>
			<td><?php echo $this->CreateInputText($id, 'newstitle', $title, '25', '255', 'class="standard"')?></td>
		  </tr>
		  <tr>
			<th>Content:</th>
			<td><?php echo $this->CreateTextArea(true, $id, $data, 'newscontent', 'syntaxHighlight', 'newscontent')?></td>
		  </tr>
		  <tr>
			<th>Post Date:</th>
			<td><?php echo $this->CreateInputText($id, 'post_date', $post_date, '12', '20')?></td>
		  </tr>
		  <tr>
			<th>Start Date:</th>
			<td><?php echo $this->CreateInputText($id, 'start_date', $start_date, '12', '20')?></td>
		  </tr>
		  <tr>
			<th>Expiry:</th>
			<td>
			<?php
				$addttext = '';
				$values = array();
				if( $moduleaction == "edit" || $moduleaction == "completeedit" )
				{
					$addttext = 'disabled="disabled"';
				}
				else
				{
					$values = array('1 Day'=>'1 Day', '1 Week'=>'1 Week', '2 Weeks'=>'2 Weeks', '1 Month'=>'1 Month', '3 Months'=>'3 Months', '6 Months'=>'6 Months', '1 Year'=>'1 Year', 'Never'=>'Never');
				}
				echo $this->CreateInputDropdown($id, 'expiry', $values, -1, $expiry, $addttext);
		?>
			</td>
		  </tr>
		  <tr>
			<th>End Date:</th>
			<td><?php echo $this->CreateInputText($id, 'end_date', $end_date, '12', '20')?></td>
		  </tr>
		  <tr>
			<th>&nbsp;</th>
			<td><em>Note:</em> Dates must be in a 'yyyy-mm-dd hh:mm:ss' format.</td>
		  </tr>
		  <tr>
			<td><?php
					if ($newsid != '')
					{
						echo $this->CreateInputHidden($id, 'newsid', $newsid);
					}
				?>
			</td>
			<td>
			  <?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?>
			  <?php echo $this->CreateInputSubmit($id, 'cancelsubmit', 'Cancel') ?>
			</td>
		  </tr>
		</table>
		 
		<?php echo $this->CreateFormEnd()?>
		 
		</div>
		<?php
	}

	function DoAction($action, $id, $params, $returnid = -1)
	{
		$db = $this->cms->db;
		switch ($action)
		{
			case "default":
				if (isset($params["makerssbutton"]))
				{
					$params = array("showtemplate"=>"false","type"=>"rss");
					echo $this->CreateLink($id, 'default', $returnid, "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Newsfeed\" />", $params);
					return;
				}

				if (isset($params["category"]))
				{
					$query =  "SELECT news_id, news_title, news_data, news_date FROM ";
					$query .= cms_db_prefix()."module_news WHERE news_cat = \"";
					$query .= $params["category"]."\" AND ((".$db->IfNull('start_time',"'1/1/1900'");
					$query .= " = '1/1/1900' AND ".$db->IfNull('end_time',"'1/1/1900'");
					$query .= " = '1/1/1900') OR (start_time < '".$db->DBTimeStamp(time()) . "'";
					$query .= " AND end_time > '".$db->DBTimeStamp(time())."')) ";
				}
				else 
				{
					$query =  "SELECT news_id, news_cat, news_title, news_data, news_date ";
					$query .= "FROM ".cms_db_prefix()."module_news WHERE ";
					$query .= "((".$db->IfNull('start_time',"'1/1/1900'")." = '1/1/1900' AND ".$db->ifNull('end_time',"'1/1/1900'");
					$query .= " = '1/1/1900') OR (start_time < '".$db->DBTimeStamp(time()) . "'";
					$query .= " AND end_time > '".$db->DBTimeStamp(time())."')) ";
				}
				
				if (isset($params["sortasc"]))
				{
					$query .= "ORDER BY news_date asc";
				}
				else
				{
					$query .= "ORDER BY news_date desc";
				}

				if(isset($params["number"]))
				{
					$dbresult = $db->SelectLimit($query, $params["number"]);
				}
				else
				{
					$dbresult = $db->Execute($query);
				}

				$dateformat = "F j, Y, g:i a";
				if (isset($params["dateformat"]))
				{
					$dateformat = $params['dateformat'];
				}
				else
				{
					$params['dateformat'] = $dateformat;
				}

				$type = "text";
				if (isset($params['type']))
				{
					$type = $params['type'];
				}
				else if (isset($params["type"]))
				{
					$type = $params["type"];
				}

				if ($dbresult && $dbresult->RowCount() > 0)
				{
					/*
					if ($type == "rss")
					{
						$variables = &$this->cms->variables;
						$variables['content-type'] = 'text/xml';
						echo "<?xml version='1.0'?>\n";
						echo "<rss version='2.0'>\n";
						echo "   <channel>\n";
						echo "  <title>".$cms->config["news_rss_title"]."</title>\n";
						echo "  <link>";
						echo cms_htmlentities($cms->config["news_url"], ENT_NOQUOTES, get_encoding($encoding));
						echo "</link>\n";
						echo "  <description>Current News entries</description>\n";
					}
					*/

					$entryarray = array();

					global $gCms;
					$this->smarty->assign_by_ref('root_url', $gCms->config['root_url']);

					while (($row = $dbresult->FetchRow()))
					{
						if ($type == "blahrss")
						{
							echo "        <item>\n";
							if( !isset( $params["category"] ) )
							{
								if (isset($params["showcategorywithtitle"]) && ($params["showcategorywithtitle"] == "true" || $params["showcategorywithtitle"] == "1"))
								{
									echo "            <title>".$row["news_cat"].": ".$row["news_title"]."</title>\n";
								}
								else
								{
									echo "            <title>".$row["news_title"]."</title>\n";
								}
							}
							else
							{
								if (isset($params["summary"]))
								{
									echo "            <title><a name=\"".$config["root_url"]."/index.php?page=".$params["summary"]."#".$row["news_id"]."\">".$row["news_title"]."</a></title>\n";
								}
								else 
								{
									echo "            <title>".strip_tags($row["news_title"])."</title>\n";
								}
							}
							if (isset($params["summary"]))
							{
								echo "            <description>".$this->StripToLength($row["news_data"],$params["length"])."</description>\n";
							}
							else
							{
								echo "            <description>".$this->StripToLength($row["news_data"],0)."</description>\n";
							} 
							echo "            <pubDate>".gmdate('D, j M Y H:i:s T', $db->UnixTimeStamp($row["news_date"]))."</pubDate>\n";
							echo "        </item>\n";
						}
						else
						{
							$onerow = new stdClass();
							$onerow->unixdate = $db->UnixTimeStamp($row['news_date']);
							$onerow->date = date($dateformat, $db->UnixTimeStamp($row['news_date']));
							$onerow->gmdate = gmdate('D, j M Y H:i:s T', $db->UnixTimeStamp($row['news_date']));
							$onerow->id = $row['news_id'];

							if (isset($params['summary']))
							{
								$onerow->data = $this->StriptoLength($row['news_data'], $params['length'], false);
								if (strlen($row['news_data']) > $params['length'])
								{
									if (isset($params['moretext']))
									{
										$onerow->moretext = $params['moretext'];
									}
									else
									{
										$onerow->moretext = 'more...';
									}
								}
								else
								{
									$onerow->moretext = '';
								}
							}
							else
							{
								$onerow->data = $row['news_data'];
								$onerow->moretext = '';
							}

							if (isset($params["showcategorywithtitle"])
										&& ($params["showcategorywithtitle"] == "true"
										|| $params["showcategorywithtitle"] == "1")
							)
							{
								$onerow->title = $row['news_cat'] . ": " . $row['news_title'];
							}
							else
							{
								$onerow->title = $row['news_title'];
							}

							array_push($entryarray, $onerow);
						}
					}

					$this->smarty->assign_by_ref('items', $entryarray);
					$this->smarty->assign_by_ref('params', $params);

					if ($type == 'rss')
					{
						echo $this->ProcessTemplateFromDatabase('displayrss');
					}
					else
					{
						echo $this->ProcessTemplateFromDatabase('displayhtml');
					}
				}

				break;

			case "defaultadmin":
				$rowsperpage=20;
				$access = $this->CheckPermission('Modify News');
				$newscat = (isset($params['news_cat'])?$params['news_cat']:"");
				$current_page = (isset($params['page'])?$params['page']:"1");
				if (!isset($current_page))
				{
				  $current_page = 1;
				}

				if (!$access)
				{
					echo "<p class=\"error\">You need the 'Modify News' permission to perform that function.</p>";
					return;
				}

				echo $this->StartTabSet();
				echo $this->StartTab('News Items');

				$query = "SELECT news_cat FROM "
					.cms_db_prefix()."module_news GROUP BY news_cat";
				$dbresult = $db->Execute($query);
				if ($dbresult && $dbresult->RowCount())
				{
					echo $this->CreateFormStart($id, 'defaultadmin');
					echo "<table><tr>";
					echo "<td><h5>Filter:</h5></td>";
					echo '<td><select name="'.$id.'news_cat">';
					echo '<option';
					if ($newscat == "All")
					{
						echo " selected=\"selected\"";
					}
					echo ">All</option>";
					while ($row = $dbresult->FetchRow())
					{
						$x = $row["news_cat"];
						if( strlen($x) == 0 )
						{
							$x = "**Empty**";
						}
						echo "<option";
						if ($newscat == $x)
						{
							echo " selected=\"selected\"";
						}
						echo ">".$x."</option>";
					}
					echo "</select></td>";
					echo "<td>".$this->CreateInputSubmit($id, 'filter', 'Select')."</td>";
					echo "</tr></table>";
					echo $this->CreateFormEnd($id);
				}

				echo $this->CreateFormStart($id, 'add');
				echo $this->CreateInputHidden($id, 'news_cat', $newscat);
				echo $this->CreateInputSubmit($id, 'submit', 'Add News Item');
				echo $this->CreateFormEnd();

				if( isset($newscat) && strlen($newscat) ) 
				{
					if( $newscat == "All" )
					{
						echo "<h4>All Entries:</h4><br />";
						$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
							.cms_db_prefix()."module_news ORDER BY news_date desc";
					}
					else if( $newscat == "**Empty**" )
					{
						echo "<h4>**Empty** Entries:</h4><br />";
						$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
							.cms_db_prefix()."module_news ORDER BY news_date desc";
					}
					else 
					{
						echo "<h4>$newscat Entries:</h4><br />";
						$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
							.cms_db_prefix()."module_news WHERE news_cat = \""
							.$newscat."\" ORDER BY news_date desc";
					}
				}
				else
				{
					echo "<h4>All Entries:</h4><br />";
					$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
						.cms_db_prefix()."module_news ORDER BY news_date desc";
				}

				$dbresult = $db->Execute($query);
				if ($dbresult && $dbresult->RowCount())
				{
					echo '<table cellspacing="0" class="AdminTable">';
					echo '<thead>';
					echo '<tr>';
					echo "<td colspan=\"6\"><div align=\"right\" class=\"clearbox\">".$this->CreatePagination($id, 'defaultadmin', $returnid, $current_page, $dbresult->RowCount(), $rowsperpage)."</td>";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "<th width=\"2%\">&nbsp;</th>\n";
					echo "<th width=\"50%\">Title</th>\n";
					echo "<th width=\"10%\">Category</th>\n";
					echo "<th width=\"20%\">Posting Date</th>\n";
					echo "<th width=\"8%\">&nbsp;</th>\n";
					echo "<th width=\"10%\">&nbsp;</th>\n";
					echo "</tr>\n";
					echo "</thead>\n";
					echo "<tbody>\n";
					$rowclass="row1";
					$r=0;

					$startn = ($current_page - 1) * $rowsperpage;
					$endn   = ($current_page * $rowsperpage) - 1;
					while ($row = $dbresult->FetchRow()) 
					{
						if( $r < $startn || $r > $endn )
						{
						   $r++;
						   continue;
						} 
						$r++;

						echo "<tr class=\"$rowclass\">\n";
						echo "<td align=\"right\">".$r."</td>\n";
						echo "<td>".$row["news_title"]."</td>\n";
						echo "<td>".$row["news_cat"]."</td>\n";
						echo "<td>".$row["news_date"]."</td>\n";
						echo "<td>".$this->CreateLink($id, 'News', $returnid, 'Edit', array("action"=>"edit","news_id"=>$row["news_id"],"news_cat"=>$newscat))."</td>\n";
						echo "<td>".$this->CreateLink($id, 'News', $returnid, 'Delete', array("action"=>"delete","news_id"=>$row["news_id"]), 'Are you sure you want to delete?')."</td>\n";
						echo "</tr>\n";
						($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
					}
					echo "</tbody>\n";
					echo "</table>\n";
				}
				else
				{
					echo "<p><b>No</b> news items found for category: ".$newscat."</p>";
				}

				echo $this->EndTab();

				echo $this->StartTab('Template');
				
				echo $this->CreateFormStart($id, 'updatetemplate');

				echo '<p>'.$this->CreateTextArea(false, $id, $this->GetTemplate('displayhtml'), 'templatecontent', '').'</p>';

				echo $this->CreateInputSubmit($id, 'submitbutton', 'Submit');

				echo $this->CreateFormEnd();

				echo $this->EndTab();

				echo $this->StartTab('RSS Template');
				
				echo $this->CreateFormStart($id, 'updatersstemplate');

				echo '<p>'.$this->CreateTextArea(false, $id, $this->GetTemplate('displayrss'), 'rsstemplatecontent', '').'</p>';

				echo $this->CreateInputSubmit($id, 'rsssubmitbutton', 'Submit');

				echo $this->CreateFormEnd();

				echo $this->EndTab();

				echo $this->EndTabSet();

				break;

			case "Add":
			case "Select":
				echo "<p>Error!</p>"; 
				break;

			case "updatetemplate":
				$this->SetTemplate('displayhtml', $params['templatecontent']);
				$this->Redirect($id, 'defaultadmin');
				break;

			case "updatersstemplate":
				$this->SetTemplate('displayrss', $params['rsstemplatecontent']);
				$this->Redirect($id, 'defaultadmin');
				break;

			case "edit":
				$newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
				$query = "SELECT * FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
				$dbresult = $db->Execute($query, array($newsid));
				if ($dbresult && $dbresult->RowCount() > 0) 
				{
					$row = $dbresult->FetchRow();
					$news_cat = $row["news_cat"];
					$title = $row["news_title"];
					$data = $row["news_data"];
					$start_date = '';
					$end_date = '';
					if (isset($row["news_date"]))
					{
						$post_date = $row["news_date"];
					}
					if (isset($row["start_time"]))
					{
						$start_date = $row["start_time"];
					}
					if (isset($row["end_time"]))
					{
						$end_date = $row["end_time"];
					}
					$this->AdminForm($id, 'completeedit', '', $data, $title, $post_date, $start_date, $end_date, $news_cat, $newsid);
				}
				break;

			case "add":
				$post_date = rtrim(ltrim($db->DBTimeStamp(time()), "'"), "'");
				$this->AdminForm($id, 'completeadd', '', '', '', $post_date);
				break;

			case "delete":
				$query = "DELETE FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
				$dbresult = $db->Execute($query, array($params['news_id']));
				$this->Audit($params['news_id'], 'News', 'Deleted New Item');
				$this->Redirect($id, 'defaultadmin', $returnid);
				break;

			case "completeadd":

				$error = '';

				if (isset($params['cancelsubmit']))
				{
					$this->Redirect($id, 'defaultadmin');
				}
				$validinfo = true;

				$newsid = (isset($params['newsid'])?$params['newsid']:"");
				$newscat = (isset($params['newscat'])?$params['newscat']:"");
				if ($newscat == "")
				{
					$newscat = (isset($params['selcat'])?$params['selcat']:"");
					if ($newscat == "")
					{
						$newscat = (isset($params['addcat'])?$params['addcat']:"");
					}
				}
				$title = (isset($params['newstitle'])?$params['newstitle']:"");
				$data = (isset($params['newscontent'])?$params['newscontent']:"");
				$post_date = (isset($params['post_date'])?$params['post_date']:"");
				$start_date = (isset($params['start_date'])?$params['start_date']:"");
				$end_date = (isset($params['end_date'])?$params['end_date']:"");
				$expiry = (isset($params['expiry'])?$params['expiry']:"");

				if ($newscat == "")
				{
					$error .= "<li>No Category given</li>";
					$validinfo = false;
				}

				if ($title == "")
				{
					$error .= "<li>No title given</li>";
					$validinfo = false;
				}

				if ($data == "")
				{
					$error .= "<li>No content given</li>";
					$validinfo = false;
				}

				if ($post_date == "")
				{
					$error .= "<li>No post date given</li>";
					$validinfo = false;
				}
				else if ($db->DBTimeStamp($post_date) === FALSE)
				{
					$error .= "<li>Post date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
					$validinfo = false;
				}

				if ($start_date !== "" && $end_date === "")
				{
					$error .= "<li>Entering a start date requires an end date also.</li>";
					$validinfo = false;
				}

				if ($end_date !== "" && $start_date === "")
				{
					$error .= "<li>Entering an end date requires a start date also.</li>";
					$validinfo = false;
				}

				if ($start_date !== "" && $end_date !== "" && $validinfo)
				{
					if ($db->DBTimeStamp($start_date) === FALSE)
					{
						$error .= "<li>Start date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
						$validinfo = false;
					}
					if ($db->DBTimeStamp($end_date) === FALSE)
					{
						$error .= "<li>End date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
						$validinfo = false;
					}
				}

				if ($validinfo)
				{
					$new_id = $db->GenID(cms_db_prefix()."module_news_seq");
					$querystart = "INSERT INTO ".cms_db_prefix()."module_news (news_cat, news_id, news_title, news_data, news_date, create_date";
					$queryend = ") VALUES (?,?,?,?,?,?";
					$params = array($newscat, $new_id, $title, $data, $post_date, $db->DBTimeStamp(time()));
					if (strlen($end_date) > 0)
					{
						$querystart .= ", end_time";
						$queryend .= ",?";
						array_push($params, rtrim(ltrim($db->DBTimeStamp($end_date), "'"), "'")); 
					}
					else if( $expiry != "" && $expiry != "Never" )
					{
						$querystart .= ", end_time";
						$queryend .= ",?";
						$time = strtotime("+".$expiry);
						array_push($params, rtrim(ltrim($db->DBTimeStamp($time), "'"), "'")); 
						if( $start_date == "" ) 
						{
							$start_date = $db->DBTimeStamp(time());
						}
					}
					if ($start_date != "")
					{
						$querystart .= ", start_time";
						$queryend .= ",?";
						array_push($params, rtrim(ltrim($db->DBTimeStamp($start_date), "'"), "'")); 
					}
					$query = $querystart . $queryend . ")";
					$dbresult = $db->Execute($query, $params);
					$this->Audit($new_id, 'News', 'Added News Item');
					$this->Redirect($id, 'defaultadmin');
					return;
				}
				else
				{
					$this->AdminForm($id, 'completeadd', $error, $data, $title, $post_date, $start_date, $end_date, '', $newsid, $expiry);
				}
				break;

			case "completeedit":

				if (isset($params['cancelsubmit']))
				{
					$this->Redirect($id, 'defaultadmin');
				}

				$validinfo = true;
				$error = '';

				$newsid = (isset($params['newsid'])?$params['newsid']:"");
				$newscat = (isset($params['newscat'])?$params['newscat']:"");
				if ($newscat == "")
				{
					$newscat = (isset($params['selcat'])?$params['selcat']:"");
					if ($newscat == "")
					{
						$newscat = (isset($params['addcat'])?$params['addcat']:"");
					}
				}
				$title = (isset($params['newstitle'])?$params['newstitle']:"");
				$data = (isset($params['newscontent'])?$params['newscontent']:"");
				$post_date = (isset($params['post_date'])?$params['post_date']:"");
				$start_date = (isset($params['start_date'])?$params['start_date']:"");
				$end_date = (isset($params['end_date'])?$params['end_date']:"");
				$expiry = (isset($params['expiry'])?$params['expiry']:"");

				if ($newscat == "")
				{
					$error .= "<li>No Category given</li>";
					$validinfo = false;
				} 

				if ($title == "") 
				{
					$error .= "<li>No title given</li>";
					$validinfo = false;
				}

				if ($data == "")
				{ 
					$error .= "<li>No content given</li>";
					$validinfo = false;
				}

				if ($post_date == "")
				{ 
					$error .= "<li>No post date given</li>";
					$validinfo = false;
				}
				else if ($db->DBTimeStamp($post_date) === FALSE)
				{
					$error .= "<li>Post date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
					$validinfo = false;
				}

				if ($start_date !== "" && $end_date === "")
				{
					$error .= "<li>Entering a start date requires an end date also.</li>";
					$validinfo = false;
				}

				if ($end_date !== "" && $start_date === "")
				{
					$error .= "<li>Entering an end date requires a start date also.</li>";
					$validinfo = false;
				}

				if ($start_date !== "" && $end_date !== "" && $validinfo)
				{
					if ($db->DBTimeStamp($start_date) === FALSE)
					{
						$error .= "<li>Start date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
						$validinfo = false;
					}
					if ($db->DBTimeStamp($end_date) === FALSE)
					{
						$error .= "<li>End date not in a valid yyyy-mm-dd hh:mm:ss format</li>";
						$validinfo = false;
					}
				}

				if ($validinfo)
				{
					$query = "UPDATE ".cms_db_prefix()."module_news SET news_cat = ?, news_title = ?, news_data = ?, modified_date = ?";
					$params = array($newscat, $title, $data, $db->DBTimeStamp(time()));

					if ($start_date != "")
					{
						$query .= ", start_time = ?";
						array_push($params, rtrim(ltrim($db->DBTimeStamp($start_date), "'"), "'"));
					}
					else
					{
						$query .= ", start_time = ?";
						array_push($params, NULL);
					}

					if ($end_date != "")
					{
						$query .= ", end_time = ?";
						array_push($params, rtrim(ltrim($db->DBTimeStamp($end_date), "'"), "'"));
					}
					else
					{
						$query .= ", end_time = ?";
						array_push($params, NULL);
					}

					if ($post_date != "")
					{
						$query .= ", news_date = ?";
						array_push($params, rtrim(ltrim($db->DBTimeStamp($post_date), "'"), "'"));
					}

					$query .= " WHERE news_id = ?";
					array_push($params, $newsid);
					$dbresult = $db->Execute($query, $params);

					$this->Audit($newsid, 'News', 'Edited News Item');
					$this->Redirect($id, 'defaultadmin');
					return;
				}
				else
				{
					$this->AdminForm($id, 'completeedit', $error, $data, $title, $post_date, $start_date, $end_date, $newscat, $newsid, $expiry);
				}
				break;
		}
	}

	function GetHelp($lang='en_US')
	{
		return "
		<h3>What does this do?</h3>
		<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to the bottom menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
		<h3>Security</h3>
		<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
		<h3>How do I use it?</h3>
		<p>The easiest way to use it is in conjunction with the cms_module tag.  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{cms_module module=\"news\" number=\"5\" category=\"beer\"}</code></p>
		<h3>What Parameters Exist?</h3>
		<p>
		<ul>
		<li><em>(optional)</em> number=\"5\" - Maximum number of items to display =- leaving empty will show all items</li>
		<li><em>(optional)</em> dateformat - Date/Time format using parameters from php's date function.  See <a href=\"http://php.net/date\" target=\"_blank\">here</a> for a parameter list and information.</li>
		<li><em>(optional)</em> makerssbutton=\"true\" - Make a button to
		link to an RSS feed of the News items. Two values in config.php are required for this to work.<br />
		<code>\$config[\"news_url\"]</code><br />
		<code>\$config[\"news_rss_title\"]</code></li>
		<li><em>(optional)</em> swaptitledate=\"true\" - Switch the order
		of the date and title</li>
		<li><em>(optional)</em> category=\"category\" - Only display items for that category.  leaving empty, will show all categories</li>
		<li><em>(optional)</em> summary=\"page\" - Activate summary mode, links are placed in the title of each summary article, and the page is trimmed to \"length\" characters</li>
		<li><em>(optional)</em> length=\"80\" - Used in summary mode (see above) this trims the length of each article to the specified number of characters after stripping all html tags.</li>
		<li><em>(optional)</em> showcategorywithtitle=\"true\" - Display the title with the category in front of it (Category: Title).  Leave false for old style behavior.</li>
		<li><em>(optional)</em> moretext=\"more...\" - Text to display at the end of a news item if it goes over the summary length.  Defaults to \"more...\".</li>
		<li><em>(optional)</em> sortasc=\"true\" - Sort news items in ascending date order rather than descending.</li>
		</ul>
		</p>
		";
	}

	function GetAuthor()
	{
		return 'Robert Campbell';
	}

	function GetAuthorEmail()
	{
		return 'rob@techcom.dyndns.org';
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
			$url = $config["root_url"]."/".$this->mId.$config["page_extension"];
		}
		else 
		{
			$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$this->mId;
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
