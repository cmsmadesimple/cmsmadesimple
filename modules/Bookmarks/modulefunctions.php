<?php
#Bookmarks. A plugin for CMS - CMS Made Simple
#Copyright (c) 2004 by Rob Allen <rob@akrabat.com>
#
#CMS- CMS Made Simple is Copyright (c) Ted Kulp (wishy@users.sf.net)
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

include_once(dirname(__FILE__).'/bookmarks.inc.php');

function bookmarks_module_install($cms)
{
	$db = $cms->db; 					/* @var $db ADOConnection */
	$dict = NewDataDictionary($db); 	/* @var $dict ADODB_DataDict */

	// create categories table
	$fields = "
		category_id I KEY,
		category_name C(255),
		category_order I
	";

	$table_opt_array = array('mysql' => 'TYPE=MyISAM');
	$sql_array = $dict->CreateTableSQL(cms_db_prefix()."module_bookmarks_categories",
	$fields, $table_opt_array );
	$dict->ExecuteSQLArray( $sql_array );

	$db->CreateSequence( cms_db_prefix()."module_bookmarks_categories_seq" );

	// create bookmarks table
	$fields = "
		bookmark_id I KEY,
		bookmark_title C(255),
		bookmark_url C(255),
		bookmark_summary X,
		bookmark_created_by I,
		bookmark_approved I1 DEFAULT 0,
		bookmark_create_date T,
		bookmark_modified_date T
	";

	$table_opt_array = array('mysql' => 'TYPE=MyISAM');
	$sql_array = $dict->CreateTableSQL(cms_db_prefix()."module_bookmarks",
	$fields, $table_opt_array );
	$dict->ExecuteSQLArray( $sql_array );

	$db->CreateSequence( cms_db_prefix()."module_bookmarks_seq" );

	// create bookmarks_to_categories table
	$fields = "
		category_id I KEY,
		bookmark_id I KEY
	";

	$table_opt_array = array('mysql' => 'TYPE=MyISAM');
	$sql_array = $dict->CreateTableSQL(cms_db_prefix()."module_bookmarks_to_categories",
	$fields, $table_opt_array );
	$dict->ExecuteSQLArray( $sql_array );

	cms_mapi_create_permission( $cms, 'Modify Bookmarks', 'Modify Bookmarks');
}


function bookmarks_module_upgrade(/* $cms, $oldversion, $newversion */)
{
	// Nothing to upgrade yet!
}

function bookmarks_module_uninstall( $cms )
{
	$db = $cms->db;
	$dict = NewDataDictionary( $db );

	$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_bookmarks" );
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_bookmarks_to_categories" );
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_bookmarks_categories" );
	$dict->ExecuteSQLArray($sqlarray);

	$db->DropSequence( cms_db_prefix()."module_bookmarks_seq" );
	$db->DropSequence( cms_db_prefix()."module_bookmarks_categories_seq" );
	cms_mapi_remove_permission($cms, 'Modify Bookmarks');
}

function bookmarks_module_execute( $cms, $id, $params )
{
	bookmarks_module_executeuser_display($cms, $id, $cms->variables['page'], $params );
}

function bookmarks_module_executeuser( $cms, $id, $return_id, $params )
{
	$action = get_request_value($id.'action', 'main');

	switch($action)
	{
		case 'bookmarks_useradd';
		case $id.'bookmarks_useradd';
			bookmarks_module_frontend_add($cms, $id, $return_id, $params);
			break;

		default:
			bookmarks_module_executeuser_display($cms, $id, $return_id, $params );
	}
}

function bookmarks_module_executeuser_display($cms, $id, $return_id, $params )
{
	// handling of display of {cms_module module=Bookmarks}
	if(isset($params["makerssbutton"]))
	{
		unset($params["makerssbutton"]);
		$params = array_merge($params, array("showtemplate"=>"false","type"=>"rss"));
		echo cms_mapi_create_user_link('Bookmarks', $id, $return_id, $params, "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Feed\" />");
		return;
	}

	if(isset($params["auto_detect_link"]))
	{
		unset($params["auto_detect_link"]);
		$params = array_merge($params, array("showtemplate"=>"false","type"=>"rss"));
		$link_code = "<link rel='alternate' type='application/rss+xml' title='Bookmarks RSS' ";
		$link = cms_mapi_create_user_link('Bookmarks', $id, $return_id, $params, "");
		$link = str_replace("<a", $link_code, $link);
		echo $link;
		return;
	}

	if(isset($params["addform"]))
	{
		bookmarks_module_frontend_display_form($cms, $id, $return_id, $params);
		return;
	}

	$type = get_param_value($id, 'type', $params, 'text');
	$display_approved = get_param_value($id, 'display_approved', $params, false);
	$show_category_with_title = get_param_value($id, 'show_category_with_title', $params);
	$category = get_param_value($id, 'category', $params);
	$number = get_param_value($id, 'number', $params);
	$order_by_date = get_param_value($id, 'order_by_date', $params, false);
	if($order_by_date)
	{
		$show_category_with_title = false;
	}

	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';
	$bookmarks_table_name = cms_db_prefix().'module_bookmarks';

	$db = $cms->db;
	if($type == "text")
	{
		$sql = "SELECT $bookmarks_to_categories_table_name.category_id
					,$categories_table_name.category_name
					,$bookmarks_table_name.*
			FROM $bookmarks_table_name
			INNER JOIN $bookmarks_to_categories_table_name
			   ON $bookmarks_table_name.bookmark_id = $bookmarks_to_categories_table_name.bookmark_id
			INNER JOIN $categories_table_name
			   ON $bookmarks_to_categories_table_name.category_id = $categories_table_name.category_id
		";

		$where = 'WHERE';
		if($display_approved == false)
		{
			$sql .= "$where $bookmarks_table_name.bookmark_approved <> 0 ";
			$where = 'AND';
		}
		if($category)
		{
			$cats = explode(',', $category);
			$sql .= $where . ' (';
			$count = 0;
			foreach($cats as $cat)
			{
				$cat = trim($cat);
				if($count != 0)
				{
					$sql .= ' OR ';
				}
				$count++;
				$sql .= "$categories_table_name.category_name LIKE '$cat' ";
			}
			$sql .=	') ';
		}

		if($order_by_date)
		{
			$sql .= "ORDER BY $bookmarks_table_name.bookmark_modified_date";
		}
		else
		{
			$sql .= "ORDER BY $categories_table_name.category_order
	    				,$categories_table_name.category_name
	        	        ,$bookmarks_table_name.bookmark_title ";
		}
	}
	else
	{
		$sql = "SELECT DISTINCT $bookmarks_table_name.*
			FROM $bookmarks_table_name
			INNER JOIN $bookmarks_to_categories_table_name
			   ON $bookmarks_table_name.bookmark_id = $bookmarks_to_categories_table_name.bookmark_id
			INNER JOIN $categories_table_name
			   ON $bookmarks_to_categories_table_name.category_id = $categories_table_name.category_id
			";

		$where = 'WHERE';
		if($display_approved == false)
		{
			$sql .= "$where $bookmarks_table_name.bookmark_approved <> 0 ";
			$where = 'AND';
		}

		if($category)
		{
			$cats = explode(',', $category);
			$sql .= $where . ' (';
			$count = 0;
			foreach($cats as $cat)
			{
				$cat = trim($cat);
				if($count != 0)
				{
					$sql .= ' OR ';
				}
				$count++;
				$sql .= "$categories_table_name.category_name LIKE '$cat' ";
			}
			$sql .=	') ';
		}

		$sql .= "ORDER BY $bookmarks_table_name.bookmark_modified_date DESC";
	}

	if($number)
	{
		$dbresult = $db->SelectLimit($sql,$number); /* @var $dbresult ADORecordSet */
	}
	else
	{
		$dbresult = $db->Execute( $sql ); /* @var $dbresult ADORecordSet */
	}

	if(isset($params["assign"]))
	{
		// if assigning, just return array of data
		return $dbresult->GetArray();
	}

	if ($dbresult && $dbresult->RowCount() > 0)
	{
		if ($type == "rss")
		{
			// find most recent date - first record as we are date_ordered
			$date = $dbresult->fields['bookmark_modified_date'];
			$date = date('r', strtotime($date));
			$title = "Boomarks";

			if (isset($params[$id."title"]))
				$title = $params[$id."title"];
			while(@ob_end_clean());
			header('Content-type: text/xml');

			echo "<?xml version='1.0'?>\n";
			echo "<rss version='2.0'>\n";
			echo "   <channel>\n";
			echo "   <title>$title</title>\n";
			echo "   <link>{$cms->config['root_url']}</link>\n";
			echo "   <pubDate>$date</pubDate>\n";
			echo "   <lastBuildDate>$date</lastBuildDate>\n";
			if(isset($params["category"]))
				echo "   <description>{$params["category"]} Bookmarks</description>\n";
			else
				echo "   <description>Bookmarks</description>\n";

		}
		else
		{
			$number_of_columns = get_param_value($id, 'columns', $params, 3);

			$num_rows = $dbresult->RecordCount();
			$rows_per_column = intval($num_rows / $number_of_columns) + 10; /* 10 is a fudge factor to make it look better! */

			$current_category_name = '';
			$columns = array();
			$col_number = 0;
			ob_start();

		}
		
		$target = '';
		if(isset($params["target"]))
		{
			$target = ' target = "' . $params["target"] . '" ';
		}

		$row_count = 0;
		while( ($row = $dbresult->FetchRow()) )
		{
			$row_count++;
			$category_name = $row['category_name'];
			$bookmark_title = $row['bookmark_title'];
			$bookmark_url = $row['bookmark_url'];

			if(strstr($bookmark_url, '//') === false)
			{
				$bookmark_url = 'http://' . $bookmark_url;
			}

			if ($type == "rss")
			{
				$bookmark_modified_date = $row['bookmark_modified_date'];
				$bookmark_modified_date_rfc822 = date('r', strtotime($bookmark_modified_date));
				$bookmark_summary = $row['bookmark_summary'];

				echo "   <item>\n";
				echo "      <title>$bookmark_title</title>\n";
				echo "      <link>$bookmark_url</link>\n";
				if($bookmark_summary)
					echo "      <description>$bookmark_summary</description>\n";
				echo "      <pubDate>$bookmark_modified_date_rfc822</pubDate>\n";
				echo "   </item>\n";
			}
			else
			{
				$bookmark_summary = empty($params["summaries"]) ? '' : "<div class='cms-module-bookmarks-summary'>" . $row['bookmark_summary'] . "</div>";

				if($current_category_name != $category_name)
				{
					// new category - can we start a new column?
					$current_category_name = $category_name;

					// close the list
					if($row_count != 1)
					{
						echo "\t\t</ul>\n";
						$string = ob_get_contents();
						ob_end_clean();
						$columns[$col_number] .= $string;
						$col_number++;
						if($col_number >= $number_of_columns)
							$col_number = 0;
					ob_start();
					}

					// start new column
					if($show_category_with_title)
						echo "\t\t<div class='cms-module-bookmarks-header'>$current_category_name</div>\n";
					echo "\t\t<ul class='cms-module-bookmarks-list'>\n";
				}

				echo "\t\t\t<li><a class='bookmark-link' $target href='$bookmark_url'>$bookmark_title</a>$bookmark_summary</li>\n";
			}
		}

		if ($type == "rss")
		{
			echo "   </channel>\n";
			echo "</rss>\n";
			exit;
		}
		else
		{
			// close off final column
			echo "\t\t</ul>\n";
			$string = ob_get_contents();
			ob_end_clean();
			$columns[$col_number] .= $string;

			// display in a table
			echo "<!-- Bookmarks Module: number of bookmarks: $num_rows -->\n";
			echo "<table id='cms-module-bookmarks' border='0' cellpadding='0' cellspacing='0'>\n<tr>\n";

			foreach($columns as $col)
			{
				echo "<td valign='top'>\n";
				echo $col;
				echo "</td>\n";
			}
			echo "</tr>\n</table>\n";
			echo "<!-- End of Bookmarks Module -->\n";
		}
	}
}

function bookmarks_module_executeadmin($cms,$module_id)
{
	$access = cms_mapi_check_permission($cms, "Modify Bookmarks");
	if (!$access)  {
		echo "<p class=\"error\">You need the 'Modify Bookmarks' permission to perform that function.</p>";
		return;
	}

	$action = get_request_value($module_id.'action', 'main');
	//DB($action,'action');

	switch($action)
	{
		case 'main':
			bookmarks_module_admin_main($cms, $module_id);
		break;

		case 'bookmarks':
			bookmarks_module_admin_display_bookmarks($cms, $module_id);
		break;

		case 'edit_bookmark':
			bookmarks_module_admin_edit_bookmark($cms, $module_id);
		break;

		case 'delete_bookmark':
			bookmarks_module_admin_delete_bookmark($cms, $module_id);
			Redirect($_SERVER['PHP_SELF'] . "?module=Bookmarks&{$module_id}action=bookmarks&result=1");
		break;

		case 'bookmarks_update':
			bookmarks_module_admin_update_bookmark($cms, $module_id, 1);
			redirect($_SERVER['PHP_SELF'] . "?module=Bookmarks&{$module_id}action=bookmarks&result=1");
		break;

		case 'categories':
			bookmarks_module_admin_display_category($cms, $module_id);
		break;

		case 'categories_update':
			$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
			commoncode_module_admin_update_categories($cms, $module_id, $categories_table_name,
					cms_db_prefix()."module_bookmarks_to_categories");
			redirect($_SERVER['PHP_SELF'] . '?module=Bookmarks&'.$module_id.'action=categories&result=1');
		break;

		default:
			echo "Error: unknown action: $action";
		break;
	}
}

function bookmarks_module_help(/*$cms*/)
{
?>
<h3>What does this do?</h3>
<p>Bookmarks is a module for displaying bookmarks on your page. When the
module is installed, a Bookmarks admin page is added to the bottom menu
that will allow you to manage your bookmarks.</p>
<h3>Security</h3>
<p>The user must belong to a group with the 'Modify Bookmarks' permission
in order to add, edit, or delete Bookmarks entries.</p>
<h3>How do I use it?</h3>
<p>The easiest way to use it is in conjunction with the cms_module tag.
This will insert the module into your template or page anywhere you wish,
and display bookmarks.  The code would look something like:
<code>{cms_module module="bookmarks" columns="2" category="humour"}</code></p>
<h3>What Parameters Exist?</h3>
<table border=0 cellpadding=3 cellspacing=0>
<tr>
	<td>columns</td>
	<td>Number of columns to display the bookmark list in. <em>(optional)</em></td>
</tr>
<tr>
	<td>category</td>
	<td>Only display items for that category. Leaving unset, will show all categories. Note that
	you can limit to muliple categories by separating each one with a comman.<em>(optional)</em></td>
</tr>
<tr>
	<td>show_category_with_title</td>
	<td> Display the category at the start of the list. <em>(optional)</em></td>
</tr>
<tr>
	<td>display_approved</td>
	<td>Display unapproved bookmarks. <em>(optional)</em></td>
</tr>
<tr>
	<td>number</td>
	<td>Set to the maximum number of bookmarks to display. Most useful when used with
	order_by_date. <em>(optional)</em></td>
</tr>
<tr>
	<td>type</td>
	<td>Set to "text" for html display or "rss" for an rss feed. Default if not set is
	"text". <em>(optional)</em></td>
</tr>
<tr>
	<td>order_by_date</td>
	<td>Set to "true" to order the list of bookmarks by date. If set to true, will turn
	off show_category_with_title. <em>(optional)</em></td>
</tr>
<tr>
	<td>auto_detect_link</td>
	<td>set to "true" to output the RSS autodetect &lt;link&gt; element for use in
	&lt;head&gt;. Best used in the "Advanced"->"Head Tags" section of content.<em>(optional)</em></td>
</tr>
<tr>
	<td>makerssbutton</td>
	<td>Set to display an RSS image that links to the RSS feed. <em>(optional)</em></td>
</tr>
<tr>
	<td>summaries</td>
	<td>Set to "true" to display the summary information. <em>(optional)</em></td>
</tr>
<tr>
	<td>target</td>
	<td>Set to "_blank" to get all links to open in a new window. <em>(optional)</em></td>
</tr>
<tr>
	<td>addform</td>
	<td>Set to "true" to display a form allowing users to submit bookmarks. <em>(optional)</em></td>
</tr>
<tr>
	<td>include_back_button</td>
	<td>Used with addform=true. When set, will include a back button on the form. <em>(optional)</em></td>
</tr>
<tr>
	<td>email_to</td>
	<td>Used with addform=true. Set to the email address to which email notifications of
	new bookmarks will be sent. <em>(optional)</em></td>
</tr>
<tr>
	<td>email_from</td>
	<td>Used with addform=true. Set to the email address to be used in the "from" field
	for email notifications. <em>(optional)</em></td>
</tr>
</table>
<?php
}

# vim:ts=4 sw=4 noet
?>
