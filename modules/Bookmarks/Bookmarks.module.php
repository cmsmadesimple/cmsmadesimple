<?php
# Bookmarks. A plugin for CMS - CMS Made Simple
# Copyright (c) 2004 by Rob Allen <rob@akrabat.com>
#
# CMS- CMS Made Simple is Copyright (c) Ted Kulp (wishy@users.sf.net)
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


class Bookmarks extends CMSModule
{
	var $categories_table_name;
	var $bookmarks_to_categories_table_name;
	var $bookmarks_table_name;
	
	function Bookmarks()
	{
		$this->CMSModule();
		
		$this->categories_table_name = cms_db_prefix() . 'module_bookmarks_categories';
		$this->bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';
		$this->bookmarks_table_name = cms_db_prefix().'module_bookmarks';	
	}
	
	function GetName()
	{
		return 'Bookmarks';
	}
	
	function GetAuthor()
	{
		return 'Rob Allen';
	}
	
	function GetAuthorEmail()
	{
		return 'rob@akrabat.com';
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
		return '1.3.1';
	}

	function GetDescription($lang = 'en_US')
	{
		return '<p>Bookmarks is a module for displaying bookmarks on your page. When the
		module is installed, a Bookmarks admin page is added to the bottom menu
		that will allow you to manage your bookmarks.</p>';
	}

	function GetHelp($lang = 'en_US')
	{
		return <<<EOT

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
			<td>display_unapproved</td>
			<td>Display unapproved bookmarks. (true or false)<em>(optional)</em></td>
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
			<td>Used with addform=true. Set to the user id of the user to which email notifications of
			new bookmarks will be sent. Note that you should choose an id for a user that has an email address
			set up. By default, the admin user does not have an email address set.<em>(optional)</em></td>
		</tr>
		<tr>
			<td>email_from</td>
			<td>Used with addform=true. Set to the user id to be used in the "from" field
			for email notifications. In general, you would user the admin user's id (1), but you must set an
			email address for the admin user as initially it does not have one.<em>(optional)</em></td>
		</tr>
		</table>

EOT;
	}

	function GetChangeLog()
	{
		return <<<EOT
			<p>Author: Rob Allen &lt;rob@akrabat.com&gt;</p>
			<dl>
				<dt>Version: 0.1</dt>
				<dd>Initial release. The code framework is based on the News module by Robert
					Campbell &lt;rob@techcom.dydnsns.org&gt;</dd>
				<dt>Version: 0.9</dt>
					<dd>First release of code to rest of world!</dd>
				<dt>Version: 0.9.1</dt>
					<dd>Bug fixes to help, column handling and the admin list.</dd>
				<dt>Version: 1.0</dt>
					<dd>Support email notifications from the submit bookmarks form. Addded delete option.
					Admin list now displays bookmarks that are not attached to any category.<br />
					Now required CommonCode 1.1<br />
					Tidied up code.<br />
					</dd>
				<dt>Version: 1.0.1</dt>
					<dd>Bug fixes that prevented display of the user facing submit form.</dd>
				<dt>Version: 1.0.2</dt>
					<dd>Uses CMS functions for CommonCode dependency.</dd>
				<dt>Version: 1.1</dt>
					<dd>Update to 0.8.</dd>
				<dt>Version: 1.3</dt>
					<dd>Update to 0.9.</dd>
				<dt>Version: 1.3.1</dt>
					<dd>Fix all default options so that the documented default is used. This is most obvious
					for the display code so that if type is not set, we correctly assume "text" rather than 
					"rss" now!. Also, fixed the API calls so that About works.</dd>
			</dl>
EOT;
	}

	function Install()
	{
		$db = $this->cms->db;				/* @var $db ADOConnection */
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
	
		$this->CreatePermission('Modify Bookmarks', 'Modify Bookmarks');
	}

	function InstallPostMessage()
	{
		return 'Make sure to set the "Modify News" permission on users who will be administering News items.';
	}

	function Upgrade($oldversion, $newversion)
	{
		// No database changes yet!
	}

	function Uninstall()
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$dict = NewDataDictionary($db);
	
		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_bookmarks");
		$dict->ExecuteSQLArray($sqlarray);
	
		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_bookmarks_to_categories");
		$dict->ExecuteSQLArray($sqlarray);
	
		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_bookmarks_categories");
		$dict->ExecuteSQLArray($sqlarray);
	
		$db->DropSequence(cms_db_prefix()."module_bookmarks_seq");
		$db->DropSequence(cms_db_prefix()."module_bookmarks_categories_seq");
		$this->RemovePermission('Modify Bookmarks');
	}
		

	function GetBookmark($bookmark_id, $default_to_approved=0)
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$bookmarks_table_name = cms_db_prefix().'module_bookmarks';
		$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';
	
		$sql = "SELECT * from $bookmarks_table_name WHERE bookmark_id = $bookmark_id";
		$rs = $db->Execute($sql);
		if($rs->RowCount() > 0)
		{
			$result = $rs->FetchRow();
			$result['categories'] = array();
			// now pick up categories
			$sql = "SELECT category_id from $bookmarks_to_categories_table_name WHERE bookmark_id = $bookmark_id";
			$rs = $db->Execute($sql);
			while($row = $rs->FetchRow())
			{
				$result['categories'][] = $row['category_id'];
			}
		}
		else
		{
			// create an empty record
			$result = array();
			$result['bookmark_id'] = -1;
			$result['bookmark_title'] = '';
			$result['bookmark_url'] = '';
			$result['bookmark_summary'] = '';
			$result['bookmark_created_by'] = -1;
			$result['bookmark_approved'] = $default_to_approved;
			$result['bookmark_create_date'] = NULL;
			$result['bookmark_modified_date'] = NULL;
			$result['categories'] = array();
		}
	
		return $result;
	}

		
	function GetCategories($order_by='category_order, category_name')
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$categories_table_name = $this->categories_table_name;
		$sql = "SELECT * FROM $categories_table_name";
		if($order_by != '')
	    {
	        $sql .= " ORDER BY $order_by";
	    }
	
	    $result = array();
	    $rs = $db->Execute($sql);
	    if($rs && $rs->RowCount() > 0)
	        $result = $rs->GetArray();
	    return $result;
	}	
	
	function AllowAccess($permission='Modify Bookmarks')
	{
		$access = $this->CheckPermission($permission);
		if (!$access)  {
			echo "<p class=\"error\">You need the '$permission' permission to perform that function.</p>";
			return false;
		}
		else return true;
	
	}
	
	function DoAction($name, $id, $parameters, $returnid='')
	{
		switch($name)
		{
			case 'default':  // default user front end page
				$this->UserDisplay($id, $parameters, $returnid);
				break;
				
			case 'bookmarks_useradd':
				$this->UserAddBookmark($id, $parameters, $returnid);
				break;
				
			case 'defaultadmin': // default admin page
				if($this->AllowAccess())
				{
					$this->AdminDisplayDefaultAdminPage($id, $parameters, $returnid);
				}
				break;
				
			case 'admin_categories_update':
				if($this->AllowAccess())
				{
					$this->AdminUpdateCategories($id, $parameters, $returnid);
				}
				break;
				
			case 'admin_add_bookmark':
			case 'admin_edit_bookmark':
				if($this->AllowAccess())
				{
					$this->AdminDisplayAddBookmark($id, $parameters, $returnid);
				}
				break;
				
			case 'admin_manage_categories':
				if($this->AllowAccess())
				{
					$this->AdminDisplayCategories($id, $parameters, $returnid);
				}
				break;
				
			case 'admin_bookmarks_update':
				if($this->AllowAccess())
				{
					$this->AdminUpdateBookmarks($id, $parameters, $returnid);
					$url = $this->CreateLink($id, 'defaultadmin', $returnid, $contents='', $params=array('result'=>'1'), '', true);
					$url = str_replace('&amp;', '&', $url);
					
					redirect($url);
				}
				break;
				
			case 'admin_delete_bookmark':
				if($this->AllowAccess())
				{
					$this->AdminDeleteBookmark($id, $parameters, $returnid);
					$url = $this->CreateLink($id, 'defaultadmin', $returnid, $contents='', $params=array('result'=>'1'), '', true);
					$url = str_replace('&amp;', '&', $url);
					
					redirect($url);
				}
				break;
				
				
			default:
				break;
		}
	}
	
	function AdminDisplayDefaultAdminPage($id, $parameters, $returnid)
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$categories = $this->GetCategories();
		if(count($categories) == 0)
		{
			// Display category admin
			$this->AdminDisplayCategories($id, $parameters, $returnid);
		}
		else
		{
			$add_bookmark_link = $this->CreateLink($id, 'admin_add_bookmark', $returnid, $contents='Add Bookmark', $params=array());
			//$manage_bookmark_link = $this->CreateLink($id, 'defaultadmin', $returnid, $contents='Manage Bookmarks', $params=array());
			$manage_categories_link = $this->CreateLink($id, 'admin_manage_categories', $returnid, $contents='Manage Categories', $params=array());
			
			echo <<<EOT
			<p>Manage Bookmarks | $add_bookmark_link | $manage_categories_link</p>	
			<h4 class="admintitle">Manage Bookmarks</h4>
	
EOT;
			// display manage bookmarks page
			$this->AdminDisplayManageBookmarks($id, $parameters, $returnid);
		}		
	}
	
	function AdminDisplayAddBookmark($id, $parameters, $returnid)
	{
			
		$db = $this->cms->db; /* @var $db ADOConnection */
		$categories = $this->GetCategories();
		$bookmark_id = empty($parameters['bookmark_id']) ? -1 : $parameters['bookmark_id'];
		$bookmark = $this->GetBookmark($bookmark_id, 1);

		$button_text = 'Add';
		if($bookmark_id > 0)
		{
			$button_text = 'Update';
		}


		//$add_bookmark_link = $this->CreateLink($id, 'admin_add_bookmark', $returnid, $contents='Add Bookmark', $params=array());
		$manage_bookmark_link = $this->CreateLink($id, 'defaultadmin', $returnid, $contents='Manage Bookmarks', $params=array());
		$manage_categories_link = $this->CreateLink($id, 'admin_manage_categories', $returnid, $contents='Manage Categories', $params=array());
			
		echo <<<EOT
		<p>$manage_bookmark_link | Add Bookmark | $manage_categories_link</p>	
		<h4 class="admintitle">Manage Bookmarks</h4>
	
EOT;

		
		
		$approved_checked_off = '';
		$approved_checked_on = '';
		if($bookmark['bookmark_approved'] != 0)
			$approved_checked_on = 'checked=checked';
		else
			$approved_checked_off = 'checked=checked';
	
		echo $this->CreateFormStart($id, 'admin_bookmarks_update', $returnid, $method='post', $enctype='');
		echo $this->CreateInputHidden($id, 'bookmark_id', $bookmark_id);
		echo <<<EOT
	
	<table border=0 cellpadding=5 cellspacing=0>
	    <tr>
	        <td align="right">Title:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_title', $bookmark['bookmark_title'], 50);
	    echo <<<EOT
	        </td>
	    </tr>
	    <tr>
	        <td align="right">Address:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_url', $bookmark['bookmark_url'], 50);
	    echo <<<EOT
			</td>
	    </tr>
	    <tr>
	    	<td style='padding-right: 5px;'>Approved:</td>
	    	<td>
EOT;
		echo $this->CreateInputRadioGroup($id, 'bookmark_approved', array('Yes'=>1,'No'=>0), $bookmark['bookmark_approved']);
		
	    echo <<<EOT
			</td>
	    <tr>
	    <tr>
	        <td align="right">Summary:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_summary', $bookmark['bookmark_summary'], 50);
	    echo <<<EOT
			</td>
	    </tr>
	    <tr>
	        <td align="right">Categories:</td>
	        <td>
EOT;

		$num_cats = count($categories);
		$num_cols = 2;
		$rows_per_col = intval($num_cats / $num_cols);
		$count = 0;
		for($i = 0; $i < $num_cats; $i++,$count ++)
		{
			if($count >= $rows_per_col)
			{
				$count = 0;
				echo "</td><td valign='top' style='padding-left: 40px;' >\n";
				$padding = 0;
			}
	
			if($i < $num_cats)
			{
				$category = $categories[$i];
				$cat_id = $category['category_id'];
				$cat_name = $category['category_name'];
				$checked = '';
				if(in_array($cat_id, $bookmark['categories']))
				$checked = 'checked';
				echo "<div>";
				echo $this->CreateInputCheckbox($id, 'bookmark_categories[]', $cat_id, $cat_id);
				echo $cat_name; 
				echo "</div>\n";
			}
		}

		echo <<<EOT
	        </td>
	    </tr>
	    <tr>
	        <td colspan=4 align="right">
	            <input type="button" value="Cancel" onclick='javascript:window.history.go(-1)' />
EOT;

		echo $this->CreateInputSubmit($id, '', $button_text);
		echo <<<EOT
	        </td>
	    </tr>
	</table>
	
EOT;

		echo $this->CreateFormEnd();
	}
	
	function AdminDisplayCategories($id, $parameters, $returnid)
	{
		$add_bookmark_link = $this->CreateLink($id, 'admin_add_bookmark', $returnid, $contents='Add Bookmark', $params=array());
		$manage_bookmark_link = $this->CreateLink($id, 'defaultadmin', $returnid, $contents='Manage Bookmarks', $params=array());
		
		echo <<<EOT
		<p>$manage_bookmark_link | $add_bookmark_link | Manage Categories</p>
	    <h4>Manage Categories</h4>
	
EOT;

		$db = $this->cms->db; /* @var $db ADOConnection */
		$categories = $this->GetCategories();
		$num_cats = count($categories);
	
		echo $this->CreateFormStart($id, 'admin_categories_update', $returnid, $method='post', $enctype='');
	    echo <<<EOT
	
		<table border=0 cellspacing=0 cellpadding=3>
		<tr>
			<th>Name</th>
			<th>Order</th>
		</tr>

EOT;
		$categories[$num_cats]['category_id'] = -1;
		$categories[$num_cats]['category_name'] = '';
		$categories[$num_cats]['category_order'] = 50;
		$num_cats ++;
	
		$num_cols = 2;
		$rows_per_col = intval($num_cats / $num_cols);
	
		$count = 1;
		for($i = 0; $i < $num_cats; $i++,$count ++)
		{
			if($i < $num_cats)
			{
				$category = $categories[$i];
				$category_id = $category['category_id'];
				$category_name = (empty($category['category_name']) && $id > 0) ? '== NOT SET ==' : $category['category_name'];
				$category_order = $category['category_order'];
				
				echo '<tr><td>';
				echo $this->CreateInputHidden($id, 'category_ids[]', $category_id);
				echo $this->CreateInputText($id, 'category_names[]', $category_name);
				echo '</td><td>';
				echo $this->CreateInputText($id, 'category_orders[]', $category_order);
				echo "</td></tr>\n";

			}
		}
				
		// submit button
		echo "<tr><td valign='top' colspan='2' align='center'>";
		echo $this->CreateInputSubmit($id, '', 'Update Categories');
		echo '</td></tr></table>';
		echo $this->CreateFormEnd();
	}
	
	function AdminUpdateCategories($id, $parameters, $returnid)
	{
	    $db = $this->cms->db; /* @var $db ADOConnection */
	    $db->debug = true; /* @var $rs ADORecordset */

		$ids = $parameters['category_ids'];
		$names = $parameters['category_names'];
		$orders = $parameters['category_orders'];
	
		$num_records = count($ids);
		for($i = 0; $i < $num_records; $i++)
		{
			// don't trust user input, but do $name later as quote() will add '' to it.
			$category_id = intval($ids[$i]);
			$category_order = intval($orders[$i]);
			$category_name = $names[$i];
			if($category_id > -1)
			{
				if($category_name == '')
				{
					// delete this category - remove the links first though.
					$sql = 'DELETE FROM ' . $this->bookmarks_to_categories_table_name . ' WHERE category_id = ' . $category_id;
					$db->Execute($sql);
	
					$sql = 'DELETE FROM ' . $this->categories_table_name . ' WHERE category_id = ' . $category_id;
					$db->Execute($sql);
				}
				else
				{
					$category_name = $db->quote($names[$i], get_magic_quotes_runtime());
					$sql = 'UPDATE ' . $this->categories_table_name . " SET
							category_name = $category_name
								,category_order = $category_order
							WHERE category_id = $category_id";
					$db->Execute($sql);
				}
			}
			elseif($category_name != '')
			{
				$category_name = $db->quote($names[$i], get_magic_quotes_runtime());
				$new_id = $db->GenID($this->categories_table_name.'_seq');
	
				$sql = 'INSERT INTO ' . $this->categories_table_name . " (category_id, category_name, category_order)
						VALUES ($new_id, $category_name, $category_order)";
				$db->Execute($sql);
			}
		}		
		
		$url = $this->CreateLink($id, 'admin_manage_categories', $returnid, $contents='', $params=array('result'=>'1'), '', true);
		$url = str_replace('&amp;', '&', $url);
		redirect($url);
	}
	
	function AdminUpdateBookmarks($id, $parameters, $returnid)
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';

	
		/* @var $rs ADORecordset */
		$user_id = $this->cms->variables['user_id'];
		$categories = get_parameter_value($parameters, 'bookmark_categories');
		$bookmark_id = get_parameter_value($parameters, 'bookmark_id', -1);
		$bookmark_title = $db->quote(get_parameter_value($parameters, 'bookmark_title'), get_magic_quotes_runtime());
		$bookmark_url = $db->quote(get_parameter_value($parameters, 'bookmark_url'), get_magic_quotes_runtime());
		$bookmark_summary = $db->quote(get_parameter_value($parameters, 'bookmark_summary'), get_magic_quotes_runtime());
		$bookmark_approved = get_parameter_value($parameters, 'bookmark_approved', 0);
	
		if($bookmark_id > -1)
		{
			// update
			$sql = "UPDATE " . $this->bookmarks_table_name . " SET
						bookmark_title = $bookmark_title
						,bookmark_url = $bookmark_url
						,bookmark_summary = $bookmark_summary
						,bookmark_approved = $bookmark_approved
						,bookmark_modified_date = NOW()
					WHERE bookmark_id = $bookmark_id";
			$db->Execute($sql);
		}
		else
		{
			$bookmark_id = $db->GenID(cms_db_prefix()."module_bookmarks_seq");
	
			$sql = "INSERT INTO " . $this->bookmarks_table_name . " (
						bookmark_id
						,bookmark_title
						,bookmark_url
						,bookmark_summary
						,bookmark_created_by
						,bookmark_approved
						,bookmark_create_date
						,bookmark_modified_date
					) VALUES (
					$bookmark_id
					,$bookmark_title
					,$bookmark_url
					,$bookmark_summary
					,$user_id
					,$bookmark_approved
					,NOW()
					,NOW()
					)";
			$db->Execute($sql);
		}
	
		// delete current bookmarks_to_categories records for this bookmark
		$sql = "DELETE FROM " . $this->bookmarks_to_categories_table_name . " WHERE bookmark_id = $bookmark_id";
		$db->Execute($sql);
	
		// update bookmarks_to_categories
		if(count($categories) > 0)
		{
			foreach($categories as $category_id)
			{
				$category_id = intval($category_id);
				if($category_id > 0)
				{
					$sql = "INSERT INTO " . $this->bookmarks_to_categories_table_name . "
								(category_id, bookmark_id)
							VALUES
								($category_id, $bookmark_id)";
					$db->Execute($sql);
				}
			}
		}		
	
	}
	
	function AdminDeleteBookmark($id, $parameters, $returnid)
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		$bookmarks_table_name = $this->bookmarks_table_name;
		$bookmarks_to_categories_table_name = $this->bookmarks_to_categories_table_name;
	
		$bookmark_id = get_parameter_value($parameters, 'bookmark_id', -1);
	
		// delete current bookmarks_to_categories records for this bookmark
		$sql = "DELETE FROM $bookmarks_to_categories_table_name WHERE bookmark_id = $bookmark_id";
		$db->Execute($sql);
	
		// delete this bookmark
		$sql = "DELETE FROM $bookmarks_table_name WHERE bookmark_id = $bookmark_id";
		$db->Execute($sql);
	}
	
	function AdminDisplayManageBookmarks($id, $parameters, $returnid)
	{
		$keyword = get_parameter_value($parameters, 'keyword', '', 'Bookmarks-keyword');
		$not_approved_only = get_parameter_value($parameters, 'not_approved_only', 0, 'Bookmarks-not_approved_only');
	
		echo "<p>", $this->CreateFormStart($id, 'defaultadmin', $returnid, $method='get', $enctype='');
		echo <<<EOT
	<table border=0 cellpadding=0 cellspacing=0 style='margin-bottom: 10px'>
	    <tr>
	        <td align="right" style='padding-right: 5px;'>Filter By:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'keyword', $keyword, 25);
		echo <<<EOT
	</td>
	    </tr>
	    <tr>
	    	<td style='padding-right: 5px;'>Approval Status:</td>
	    	<td>
EOT;
		echo $this->CreateInputRadioGroup($id, 'not_approved_only', array('Any'=>0,'Not Approved Only'=>1), $not_approved_only);	    		
	    echo <<<EOT
	    		</td>
	    <tr>
	    	<td style='padding-right: 5px;'></td>
	    	<td><input type="submit" value="Go"></td>
	    </tr>
	</table>
	
	
EOT;
		echo $this->CreateFormEnd();
	
	
		$db = $this->cms->db;
		$db->debug = false;
	
		$sql = "SELECT ". $this->bookmarks_to_categories_table_name .".category_id
					,". $this->categories_table_name . ".category_name
					,". $this->bookmarks_table_name . ".*
			FROM ". $this->bookmarks_table_name ."
			LEFT JOIN ". $this->bookmarks_to_categories_table_name . "
			   ON ". $this->bookmarks_table_name . ".bookmark_id = ". $this->bookmarks_to_categories_table_name . ".bookmark_id
			LEFT JOIN ". $this->categories_table_name . "
			   ON ". $this->bookmarks_to_categories_table_name . ".category_id = ". $this->categories_table_name . ".category_id ";
	
		$where = 'WHERE';
		if($not_approved_only)
		{
			$sql .= "$where ". $this->bookmarks_table_name . ".bookmark_approved = 0 ";
			$where = 'AND';
		}
		if(!empty($keyword))
		{
			$sql .= "$where ". $this->bookmarks_table_name . ".bookmark_title LIKE '%$keyword%'
						OR ". $this->bookmarks_table_name . ".bookmark_url LIKE '%$keyword%'
						OR ". $this->bookmarks_table_name . ".bookmark_summary LIKE '%$keyword%' ";
		}
		$order_by = get_parameter_value($parameters, 'order_by', '');
		if(!empty($order_by))
		{
			$sql .= "ORDER BY ". $this->bookmarks_table_name . ".modified_date";
		}
		else
		{
			$sql .= "ORDER BY ". $this->categories_table_name . ".category_order
	    				,". $this->categories_table_name . ".category_name
	        	        ,". $this->bookmarks_table_name . ".bookmark_title ";
		}
		$rs = $db->Execute($sql);
	
		if ($rs && $rs->RowCount() > 0)
		{
			$number_of_columns = get_parameter_value($parameters, 'columns', 2);
	
			$num_rows = $rs->RecordCount();
			$rows_per_column = intval($num_rows / $number_of_columns) + 0; /* 10 is a fudge factor to make it look better! */
	
	
			$current_category_name = 'unknown'; 
			$row_count = 0;
		
			// true / false images
			$image_true ="<img src=\"../images/cms/true.gif\" alt=\"".lang('true')."\" title=\"".lang('true')."\" border=\"0\">";
			$image_false ="<img src=\"../images/cms/false.gif\" alt=\"".lang('false')."\" title=\"".lang('false')."\" border=\"0\">";
	
			while( ($row = $rs->FetchRow()) )
			{
				$row_count++;
				$row_class = ($row_count % 2) ? 'row1' : 'row2';
	
				$bookmark_id = $row['bookmark_id'];
				$category_name = $row['category_name'];
				$bookmark_title = $row['bookmark_title'];
				$bookmark_url = $row['bookmark_url'];
				$bookmark_summary =$row['bookmark_summary'];
				$bookmark_created_by = $row['bookmark_created_by'];
				$bookmark_approved = $row['bookmark_approved'] == 0 ? $image_false : $image_true;
				$create_date = $row['bookmark_create_date'];
				$modified_date = $row['bookmark_modified_date'];
	
				$edit_url = $this->CreateLink($id, 'admin_edit_bookmark', $returnid, $contents='Manage Bookmarks', $params=array('bookmark_id'=>$bookmark_id), '', true);
				$delete_url = $this->CreateLink($id, 'admin_delete_bookmark', $returnid, $contents='Manage Bookmarks', $params=array('bookmark_id'=>$bookmark_id), '', true);

				if($current_category_name != $category_name)
				{
					// new category
					if($category_name == '')
						$category_name = '== None ==';
					$current_category_name = $category_name;
	
					if($row_count > 1)
					{
						echo "</table>\n";
					}
					echo "<div style='font-weight: bold; margin: 5px 0px;'>Category: $current_category_name</div>\n";
					echo "<table cellspacing=0 class='admintable'>\n";
					echo "<tr><th>Title</th><th>Url</th><th style='padding: 0px 10px;'>Approved</th><th>Summary</th><th></th><th></th></tr>\n";
				}
	
				echo <<<EOT
					<tr class="$row_class">
						<td width='33%'><a href='$edit_url'>$bookmark_title</a></td>
						<td width='33%'><a class='bookmark-link' href='$bookmark_url' target='_blank'>$bookmark_url</a></td>
						<td align='center'>$bookmark_approved </td>
						<td width='33%'>$bookmark_summary </td>
						
EOT;
				// edit and delete icons - borrwed from content list for consistency
				echo "\t\t\t\t<td align='center'><a href='$edit_url'><img src='../images/cms/edit.gif' width='16' height='16' border='0' alt='" . lang('edit') . "' title='" . lang('edit') . "'></a></td>\n";
				echo "\t\t\t\t<td align='center'><a href='$delete_url' onclick=\"return confirm('" . lang('deleteconfirm') . "');\"><img src='../images/cms/delete.gif' width='16' height='16' border='0' alt='" . lang('delete') . "' title='" . lang('delete') . "'></a></td>\n";
				echo <<<EOT
					</tr>
	
EOT;
			}
	
			// close off final column
			echo "\t\t</ul>\n";
			echo "\t</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "<!-- End of Bookmarks Module -->\n";
			
		}		
	}
	
	function UserDisplay($id, $parameters, $returnid)
	{
		// handling of display of {cms_module module=Bookmarks}
		if(isset($parameters['makerssbutton']))
		{
			echo $this->CreateLink($id, $returnid, 'default', '<img border="0" src="images/cms/xml_rss.gif" alt="RSS Feed" />',
					array('showtemplate'=>'false','type'=>'rss'));
			return;
		}
	
		if(isset($parameters['auto_detect_link']))
		{
			$title = empty($parameters['title']) ? 'Bookmarks RSS' : $parameters['title'];
			$url = $this->CreateLink($id, $returnid, 'default', '',
					array('showtemplate'=>'false','type'=>'rss'), '', true);
		
			echo "<link rel='alternate' type='application/rss+xml' title='$title' href='$url' />";
			return;
		}
	
		if(isset($parameters["addform"]))
		{
			$this->DisplayAddBookmarkForm($id, $parameters, $returnid);
			return;
		}
	
		$type = get_parameter_value($parameters, 'type', 'text');
		$display_unapproved = get_parameter_value($parameters, display_unapproved, false);
		$show_category_with_title = get_parameter_value($parameters, 'show_category_with_title', '');
		$category = get_parameter_value($parameters, 'category', '');
		$number = get_parameter_value($parameters, 'number', '');
		$order_by_date = get_parameter_value($parameters, 'order_by_date', false);
		if($order_by_date)
		{
			$show_category_with_title = false;
		}
			
		$categories_table_name = $this->categories_table_name;
		$bookmarks_to_categories_table_name = $this->bookmarks_to_categories_table_name;
		$bookmarks_table_name = $this->bookmarks_table_name;
	
		$db = $this->cms->db;
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
			if($display_unapproved == false)
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
			if($display_unapproved == false)
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
	
		if(isset($parameters['assign']))
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
	
				if (isset($parameters[$id."title"]))
					$title = $parameters[$id."title"];
				while(@ob_end_clean());
				header('Content-type: text/xml');
	
				echo "<?xml version='1.0'?>\n";
				echo "<rss version='2.0'>\n";
				echo "   <channel>\n";
				echo "   <title>$title</title>\n";
				echo "   <link>{$cms->config['root_url']}</link>\n";
				echo "   <pubDate>$date</pubDate>\n";
				echo "   <lastBuildDate>$date</lastBuildDate>\n";
				if(isset($parameters["category"]))
					echo "   <description>{$parameters["category"]} Bookmarks</description>\n";
				else
					echo "   <description>Bookmarks</description>\n";
	
			}
			else
			{
				$number_of_columns = get_parameter_value($parameters, 'columns', 3);
	
				$num_rows = $dbresult->RecordCount();
				$rows_per_column = intval($num_rows / $number_of_columns) + 10; /* 10 is a fudge factor to make it look better! */
	
				$current_category_name = '';
				$columns = array();
				$col_number = 0;
				ob_start();
	
			}
			
			$target = '';
			if(isset($parameters['target']))
			{
				$target = ' target = "' . $parameters["target"] . '" ';
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
					$bookmark_summary = empty($parameters["summaries"]) ? '' : "<div class='cms-module-bookmarks-summary'>" . $row['bookmark_summary'] . "</div>";
	
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
	
			if ($type == 'rss')
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
	
	function DisplayAddBookmarkForm($id, $parameters, $returnid)
	{
		$db = $this->cms->db; /* @var $db ADOConnection */
		
		$categories = $this->GetCategories();
		$bookmark = $this->GetBookmark(-1);
	
		$include_back_button = get_parameter_value($parameters, 'include_back_button', false);
		$email_to = get_parameter_value($parameters, 'email_to', 0);
		$email_from = get_parameter_value($parameters, 'email_from', 0);
	
		$button_text = 'Add';
		if($bookmark_id > 0)
		{
			$button_text = 'Update';
		}
	
		echo $this->CreateFormStart($id, 'bookmarks_useradd', $returnid, $method='post', $enctype='');
	
		if($email_to > 0)
		{
			echo $this->CreateInputHidden($id, 'email_to', $email_to);
		}
		if($email_from > 0)
		{
			echo $this->CreateInputHidden($id, 'email_from', $email_from);
		}
	
		echo $this->CreateInputHidden($id, 'bookmark_id', $bookmark_id);
		echo $this->CreateInputHidden($id, 'referer', $_SERVER['SCRIPT_NAME']);
		echo <<<EOT
	
	<table border=0 cellpadding=5 cellspacing=0>
	    <tr>
	        <td align="right">Title:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_title', $bookmark['bookmark_title'], 50);
	    echo <<<EOT
	        </td>
	    </tr>
	    <tr>
	        <td align="right">Address:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_url', $bookmark['bookmark_url'], 50);
	    echo <<<EOT
			</td>
	    </tr>
	    <tr>
	        <td align="right">Summary:</td>
	        <td colspan=3>
EOT;
		echo $this->CreateInputText($id, 'bookmark_summary', $bookmark['bookmark_summary'], 50);
	    echo <<<EOT
			</td>
	    </tr>
	    <tr>
	        <td align="right">Categories:</td>
	        <td>
EOT;
		$num_cats = count($categories);
		$num_cols = 2;
		$rows_per_col = intval($num_cats / $num_cols);
		$count = 0;
		for($i = 0; $i < $num_cats; $i++,$count ++)
		{
			if($count >= $rows_per_col)
			{
				$count = 0;
				echo "</td><td valign='top' style='padding-left: 40px;' >\n";
				$padding = 0;
			}
	
			if($i < $num_cats)
			{
				$category = $categories[$i];
				$cat_id = $category['category_id'];
				$cat_name = $category['category_name'];
				echo "<div>";
				echo $this->CreateInputCheckbox($id, 'bookmark_categories[]', $cat_id);
				echo $cat_name; 
				echo "</div>\n";
			}
		}
		echo <<<EOT
	        </td>
	    </tr>
	    <tr>
	        <td colspan=4 align="right">
	
EOT;
	
		if($include_back_button)
		{
			echo <<<EOT
				<input type="button" value="Cancel" onclick='javascript:window.history.go(-1)' />
	
EOT;
		}
	
		echo $this->CreateInputSubmit($id, '', 'Submit');
		echo <<<EOT
	        </td>
	    </tr>
	</table>

EOT;
		echo $this->CreateFormEnd();
	
	
	}
	
	function UserAddBookmark($id, $parameters, $returnid)
	{
		$parameters['bookmark_approved']=0;
		$this->AdminUpdateBookmarks($id, $parameters, $returnid);
		$email_to = get_parameter_value($parameters, 'email_to', 0);
		if($email_to > 0)
		{
			// get email address to send email to:
			$sql = 'SELECT email FROM ' .cms_db_prefix().'users WHERE user_id=' . $email_to;
			$rs = $this->cms->db->SelectLimit($sql, 1);
			$email_to = '';
			if($rs)
			{
				$email_to = $rs->fields['email'];
			}
			if($email_to != '')
			{
				// get from email address.
				$email_from = get_parameter_value($parameters, 'email_from', 0);
				if($email_from <= 0)
				{
					// none set - pick up first email we can find.
					$email_from = 'bookmarksmodule@example.com';
					$sql = 'SELECT email FROM ' .cms_db_prefix().'users WHERE email IS NOT NULL ORDER BY user_id ASC';
					$rs = $this->cms->db->SelectLimit($sql, 1);
					if($rs)
						$email_from = $rs->fields['email'];
				}
		
				$subject = 'New Bookmark Submitted';
				$body = "A new bookmark has been submitted.\n\n";
				$body .= "Title: {$parameters[$module_id.'title']}\n";
				$body .= "Url: {$parameters[$module_id.'url']}\n";
				$body .= "Summary: {$parameters[$module_id.'summary']}\n";
		
				$headers = "From: \"CMS Boomarks Module\" <$email_from>\r\n"
		    	    ."Return-Path: \"CMS Boomarks Module\" <$email_from>\r\n"
			        ."X-Mailer: PHP/" . phpversion();
			        
			    $result = @mail($email_to, $subject, $body, $headers);
			}
		}

		$link = $this->CreateReturnLink($id, $returnid, 'Return');
		echo "<p class='cms-module-bookmarks-submitted'>Thank you for your submission. $link.</p>";
		
		
	}
	
};
?>