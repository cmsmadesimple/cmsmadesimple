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


/**
 * @return array
 * @param ADOConnection $db
 * @param integer $bookmark_id
 * @desc Retrieve a bookmark from the database as an associative array. Returns an
 *		 array with default fields otherwise.
*/
function bookmarks_module_admin_get_bookmark($db, $bookmark_id, $default_to_approved=0)
{
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

/**
 * @return void
 * @param unknown $cms
 * @param unknown $module_id
 * @desc Main response function to decide if to display the categories admin (if none are defined)
 *		 or to display the add bookmark form.
*/
function bookmarks_module_admin_main($cms, $module_id)
{
	$db = $cms->db; /* @var $db ADOConnection */
	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	$categories = commoncode_module_get_categories($db, $categories_table_name);
	if(count($categories) == 0)
	{
		// Display category admin
		bookmarks_module_admin_display_category($cms, $module_id);

	}
	else
	{
		echo <<<EOT
		<p>
		Add Bookmark
		| <a href='moduleinterface.php?module=Bookmarks&amp;{$module_id}action=bookmarks'>Manage Bookmarks</a>
		| <a href='moduleinterface.php?module=Bookmarks&amp;{$module_id}action=categories'>Manage Categories</a>
		</p>

		<h4 class="admintitle">Add Bookmark</h4>

EOT;
		bookmarks_module_admin_display_bookmark_edit_form($cms, $module_id);
	}
}

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @desc Display list of bookmarks
*/
function bookmarks_module_admin_display_bookmarks($cms, $module_id)
{
	$keyword = get_request_value($module_id.'keyword', '', 'Bookmarks');
	$not_approved_only = get_request_value($module_id.'not_approved_only', 0, 'Bookmarks');

	$not_approved_only_checked_off = '';
	$not_approved_only_checked_on = '';
	if($not_approved_only != 0)
		$not_approved_only_checked_on = 'checked=checked';
	else
		$not_approved_only_checked_off = 'checked=checked';


	echo <<<EOT
	<p>
	<a href='moduleinterface.php?module=Bookmarks'>Add Bookmark</a>
	| Manage Bookmarks
	| <a href='moduleinterface.php?module=Bookmarks&amp;{$module_id}action=categories'>Manage Categories</a>
	</p>

	<h4 class="admintitle">Manage Bookmarks</h4>


EOT;
	echo cms_mapi_create_admin_form_start("Bookmarks", $module_id, 'get');
	echo <<<EOT
<input type='hidden' name='{$module_id}action' value='bookmarks'>
<table border=0 cellpadding=0 cellspacing=0 style='margin-bottom: 10px'>
    <tr>
        <td align="right" style='padding-right: 5px;'>Filter By:</td>
        <td colspan=3><input name="{$module_id}keyword" size=25 value="$keyword"></td>
    </tr>
    <tr>
    	<td style='padding-right: 5px;'>Approval Status:</td>
    	<td><input type='radio' name="{$module_id}not_approved_only" $not_approved_only_checked_off value=0>Any
    		<input type='radio' name="{$module_id}not_approved_only" $not_approved_only_checked_on value=1>Not Approved Only</td>
    <tr>
    	<td style='padding-right: 5px;'></td>
    	<td><input type="submit" value="Go"></td>
    </tr>
</table>


EOT;
	echo cms_mapi_create_admin_form_end();

	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';
	$bookmarks_table_name = cms_db_prefix().'module_bookmarks';


	$db = $cms->db;
	$db->debug = false;

	$sql = "SELECT $bookmarks_to_categories_table_name.category_id
				,$categories_table_name.category_name
				,$bookmarks_table_name.*
		FROM $bookmarks_table_name
		LEFT JOIN $bookmarks_to_categories_table_name
		   ON $bookmarks_table_name.bookmark_id = $bookmarks_to_categories_table_name.bookmark_id
		LEFT JOIN $categories_table_name
		   ON $bookmarks_to_categories_table_name.category_id = $categories_table_name.category_id
	";

	$where = 'WHERE';
	if($not_approved_only)
	{
		$sql .= "$where $bookmarks_table_name.bookmark_approved = 0 ";
		$where = 'AND';
	}
	if(!empty($keyword))
	{
		$sql .= "$where $bookmarks_table_name.bookmark_title LIKE '%$keyword%'
					OR $bookmarks_table_name.bookmark_url LIKE '%$keyword%'
					OR $bookmarks_table_name.bookmark_summary LIKE '%$keyword%' ";
	}
	$order_by = get_request_value($module_id.'order_by', '');
	if(!empty($order_by))
	{
		$sql .= "ORDER BY $bookmarks_table_name.modified_date";
	}
	else
	{
		$sql .= "ORDER BY $categories_table_name.category_order
    				,$categories_table_name.category_name
        	        ,$bookmarks_table_name.bookmark_title ";
	}
	$rs = $db->Execute($sql);

	if ($rs && $rs->RowCount() > 0)
	{
		$number_of_columns = get_request_value($module_id.'columns', 2);

		$num_rows = $rs->RecordCount();
		$rows_per_column = intval($num_rows / $number_of_columns) + 0; /* 10 is a fudge factor to make it look better! */


		$current_category_name = 'unknown'; 
		$row_count = 0;

		$edit_url = $_SERVER['PHP_SELF'] . '?module=Bookmarks&amp;'.$module_id.'action=edit_bookmark&amp;'.$module_id.'bookmark_id';
		$delete_url = $_SERVER['PHP_SELF'] . '?module=Bookmarks&amp;'.$module_id.'action=delete_bookmark&amp;'.$module_id.'bookmark_id';

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
					<td width='33%'><a href='$edit_url=$bookmark_id'>$bookmark_title</a></td>
					<td width='33%'><a class='bookmark-link' href='$bookmark_url' target='_blank'>$bookmark_url</a></td>
					<td align='center'>$bookmark_approved </td>
					<td width='33%'>$bookmark_summary </td>
					
EOT;
			// edit and delete icons - borrwed from content list for consistency
			echo "\t\t\t\t<td align='center'><a href='$edit_url=$bookmark_id'><img src='../images/cms/edit.gif' width='16' height='16' border='0' alt='" . lang('edit') . "' title='" . lang('edit') . "'></a></td>\n";
			echo "\t\t\t\t<td align='center'><a href='$delete_url=$bookmark_id' onclick=\"return confirm('" . lang('deleteconfirm') . "');\"><img src='../images/cms/delete.gif' width='16' height='16' border='0' alt='" . lang('delete') . "' title='" . lang('delete') . "'></a></td>\n";
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

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @desc Response function for edit bookmark. Display edit form if bookmark id
 * 		 is an integer, otherwise redirect to add bookmark.
*/
function bookmarks_module_admin_edit_bookmark($cms, $module_id)
{
	echo "<h4>Edit Bookmark</h4>\n";

	$bookmark_id = get_request_value($module_id.'bookmark_id', -1);
	if($bookmark_id > 0)
	bookmarks_module_admin_display_bookmark_edit_form($cms, $module_id, $bookmark_id);
	else
	{
		redirect($_SERVER['PHP_SELF'] . '?module=Bookmarks');
	}
}

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @param integer $bookmark_id
 * @desc Display bookmark add/edit form
*/
function bookmarks_module_admin_display_bookmark_edit_form($cms, $module_id, $bookmark_id=-1)
{
	$db = $cms->db; /* @var $db ADOConnection */
	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	$categories = commoncode_module_get_categories($db, $categories_table_name);
	$bookmark = bookmarks_module_admin_get_bookmark($db, $bookmark_id, 1);

	$button_text = 'Add';
	if($bookmark_id > 0)
	{
		$button_text = 'Update';
	}

	$approved_checked_off = '';
	$approved_checked_on = '';
	if($bookmark['bookmark_approved'] != 0)
		$approved_checked_on = 'checked=checked';
	else
		$approved_checked_off = 'checked=checked';

	echo cms_mapi_create_admin_form_start("Bookmarks", $module_id);

	echo <<<EOT

<input type='hidden' name='{$module_id}action' value='bookmarks_update'>
<input type="hidden" name="{$module_id}bookmark_id" value="$bookmark_id">

<table border=0 cellpadding=5 cellspacing=0>
    <tr>
        <td align="right">Title:</td>
        <td colspan=3><input name="{$module_id}bookmark_title" size=50 value="{$bookmark['bookmark_title']}"></td>
    </tr>
    <tr>
        <td align="right">Address:</td>
        <td colspan=3><input name="{$module_id}bookmark_url" size=50 value="{$bookmark['bookmark_url']}"></td>
    </tr>
    <tr>
    	<td style='padding-right: 5px;'>Approved:</td>
    	<td><input type='radio' name="{$module_id}bookmark_approved" $approved_checked_on value=1>Yes
    		<input type='radio' name="{$module_id}bookmark_approved" $approved_checked_off value=0>No</td>
    <tr>
    <tr>
        <td align="right">Summary:</td>
        <td colspan=3><input name="{$module_id}bookmark_summary" size=50 value="{$bookmark['bookmark_summary']}"></td>
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
			echo "<div><input type='checkbox' value='$cat_id' name='{$module_id}bookmark_categories[]' $checked>$cat_name</div>\n";
		}
	}
	echo <<<EOT
        </td>
    </tr>
    <tr>
        <td colspan=4 align="right">
            <input type="button" value="Cancel" onclick='javascript:window.history.go(-1)'>
            <input type="submit" value="$button_text">
        </td>
    </tr>
</table>

EOT;
	echo cms_mapi_create_admin_form_end();

}

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @desc Update database as a result of adding/editing a bookmark
*/
function bookmarks_module_admin_update_bookmark($cms, $module_id, $approved=0)
{
	$db = $cms->db; /* @var $db ADOConnection */
	$bookmarks_table_name = cms_db_prefix().'module_bookmarks';
	$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';

	/* @var $rs ADORecordset */
	$user_id = $cms->variables['user_id'];
	$categories = get_request_value($module_id.'bookmark_categories');
	$bookmark_id = get_request_value($module_id.'bookmark_id', -1);
	$bookmark_title = $db->quote(get_request_value($module_id.'bookmark_title'), get_magic_quotes_runtime());
	$bookmark_url = $db->quote(get_request_value($module_id.'bookmark_url'), get_magic_quotes_runtime());
	$bookmark_summary = $db->quote(get_request_value($module_id.'bookmark_summary'), get_magic_quotes_runtime());
	$bookmark_approved = get_request_value($module_id.'bookmark_approved', 0);

	if($approved && $bookmark_id > -1)
	{
		// update
		$sql = "UPDATE $bookmarks_table_name SET
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

		$sql = "INSERT INTO $bookmarks_table_name (
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
	$sql = "DELETE FROM $bookmarks_to_categories_table_name WHERE bookmark_id = $bookmark_id";
	$db->Execute($sql);

	// update bookmarks_to_categories
	foreach($categories as $category_id)
	{
		$category_id = intval($category_id);
		if($category_id > 0)
		{
			$sql = "INSERT INTO $bookmarks_to_categories_table_name
						(category_id, bookmark_id)
					VALUES
						($category_id, $bookmark_id)";
			$db->Execute($sql);
		}
	}

}

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @desc Deletes a bookmark
*/
function bookmarks_module_admin_delete_bookmark($cms, $module_id)
{
	$db = $cms->db; /* @var $db ADOConnection */
	$bookmarks_table_name = cms_db_prefix().'module_bookmarks';
	$bookmarks_to_categories_table_name = cms_db_prefix().'module_bookmarks_to_categories';

	$bookmark_id = get_request_value($module_id.'bookmark_id', -1);

	
	// delete current bookmarks_to_categories records for this bookmark
	$sql = "DELETE FROM $bookmarks_to_categories_table_name WHERE bookmark_id = $bookmark_id";
	$db->Execute($sql);

	// delete this bookmark
	$sql = "DELETE FROM $bookmarks_table_name WHERE bookmark_id = $bookmark_id";
	$db->Execute($sql);
}

/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @param string $return_id
 * @desc Enter description here...
*/
function bookmarks_module_frontend_display_form($cms, $module_id, $return_id, $params)
{
	$db = $cms->db; /* @var $db ADOConnection */
	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	$categories = commoncode_module_get_categories($db, $categories_table_name);
	$bookmark = bookmarks_module_admin_get_bookmark($db, -1);

	$include_back_button = get_param_value($module_id, 'include_back_button', $params, false);
	$email_to = get_param_value($module_id, 'email_to', $params);
	$email_from = get_param_value($module_id, 'email_from', $params);

	$button_text = 'Add';
	if($bookmark_id > 0)
	{
		$button_text = 'Update';
	}

	echo cms_mapi_create_user_form_start("Bookmarks", $module_id, $return_id);

	if($email_to != '')
	{
		echo "<input type='hidden' name='{$module_id}email_to' value='$email_to'>\n";
	}
	if($email_from != '')
	{
		echo "<input type='hidden' name='{$module_id}email_from' value='$email_from'>\n";
	}

	echo <<<EOT

<input type='hidden' name='{$module_id}action' value='bookmarks_useradd'>
<input type="hidden" name="{$module_id}id" value="$bookmark_id">
<input type="hidden" name="{$module_id}referer" value="{$_SERVER['SCRIPT_NAME']}">

<table border=0 cellpadding=5 cellspacing=0>
    <tr>
        <td align="right">Title:</td>
        <td colspan=3><input name="{$module_id}title" size=50 value="{$bookmark['bookmark_title']}"></td>
    </tr>
    <tr>
        <td align="right">Address:</td>
        <td colspan=3><input name="{$module_id}url" size=50 value="{$bookmark['bookmark_url']}"></td>
    </tr>
    <tr>
        <td align="right">Summary:</td>
        <td colspan=3><input name="{$module_id}summary" size=50 value="{$bookmark['bookmark_summary']}"></td>
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
			echo "<div><input type='checkbox' value='$cat_id' name='{$module_id}categories[]' $checked>$cat_name</div>\n";
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
			<input type="button" value="Cancel" onclick='javascript:window.history.go(-1)'>

EOT;
	}

	echo <<<EOT
            <input type="submit" value="Submit">
        </td>
    </tr>
</table>
</form>
EOT;


}


/**
 * @return void
 * @param CmsObject $cms
 * @param string $module_id
 * @param string $return_id
 * @param array $params
 * @desc Enter description here...
*/
function bookmarks_module_frontend_add($cms, $module_id, $return_id, $params)
{
	$approved=0;
	bookmarks_module_admin_update_bookmark($cms, $module_id, $approved);
	$email_to = get_request_value($module_id.'email_to');
	if($email_to != '')
	{
		// get admin email address.
		$email_from = get_request_value($module_id.'email_from', '');
		if($email_from == '')
		{
			// guess an email addresss!
			$email_from = 'bookmarksmodule@example.com';
			$sql = 'SELECT email FROM ' .cms_db_prefix().'users ORDER BY user_id ASC';
			$rs = $cms->db->SelectLimit($sql, 1);
			if($rs)
				$email_from = $rs->fields['email'];
		}

		$subject = 'New Bookmark Submitted';
		$body = "A new bookmark has been submitted.\n\n";
		$body .= "Title: {$params[$module_id.'title']}\n";
		$body .= "Url: {$params[$module_id.'url']}\n";
		$body .= "Summary: {$params[$module_id.'summary']}\n";

		$headers = "From: \"CMS Boomarks Module\" <$email_from>\r\n"
    	    ."Return-Path: \"CMS Boomarks Module\" <$email_from>\r\n"
	        ."X-Mailer: PHP/" . phpversion();
	    $result = @mail($email_to, $subject, $body, $headers);
	}

	$link = cms_mapi_create_content_link_by_page_id($return_id, "Return");
	echo "<p class='cms-module-bookmarks-submitted'>Thank you for your submission. $link.</p>";
}

function bookmarks_module_admin_display_category($cms, $module_id)
{
	$categories_table_name = cms_db_prefix().'module_bookmarks_categories';
	echo <<<EOT
	<p>
	<a href='moduleinterface.php?module=Bookmarks'>Add Bookmark</a>
	| <a href='moduleinterface.php?module=Bookmarks&amp;{$module_id}action=bookmarks'>Manage Bookmarks</a>
	| Manage Categories
	</p>

    <h4>Manage Categories</h4>

EOT;
	commoncode_module_admin_display_category($cms, $module_id, "Bookmarks", $categories_table_name);
	
}
?>