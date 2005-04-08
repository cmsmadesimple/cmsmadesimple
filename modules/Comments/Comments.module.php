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

class Comments extends CMSModule
{
	function GetName()
	{
		return 'Comments';
	}

	function GetVersion()
	{
		return '1.3';
	}

	function GetHelp($lang = 'en_US')
	{
		return "
			<h3>What does this do?</h3>
			<p>The comments module is a tag module.  It's used to add comments to individual pages which can be read by users who visit the page later.  The practical reason for this module is for documentation pages, so that users can add additional comments and information to the page.</p>
			<h3>How do I use it?</h3>
			<p>Comments is just a tag module.  It's inserted into your page or template by using the cms_module tag.  Example syntax would be: <code>{cms_module module=\"comments\"}</code></p>
			<h3>What parameters are there?</h3>
			<p>
			<ul>
				<li><em>(optional)</em> number=\"5\" - Maximum number of items to display -- leaving empty will show all items</li>
				<li><em>(optional)</em> dateformat - Date/Time format using parameters from php's strftime function.  See <a href=\"http://php.net/strftime\" target=\"_blank\">here</a> for a parameter list and information.</li>
			</ul>
		";
	}

	function GetAuthor()
	{
		return 'Ted Kulp';
	}

	function GetAuthorEmail()
	{
		return 'wishy@cmsmadesimple.org';
	}

	function Install()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary($db);
		$flds = "
			comment_id I KEY,
			comment_data X,
			comment_date T,
			comment_author C(255),
			page_id C(255),
			create_date T,
			modified_date T
		";
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_comments", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_comments_seq");
	}

	function Uninstall()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_comments");
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix()."module_comments_seq");
	}

	function GetChangeLog()
	{
		return "
			1.3: Module API changes<br />
			1.2: Oct 13, 2004<br />
			Added newline parsing (kickthedonkey)<br />
			Removed a rogue div (kickthedonkey)<br />
			1.1: Sep 28, 2004<br />
			Added <em>number</em> and <em>dateformat</em> options, plus little cosmetic changes.<br />
			1.0: Initial Release
		";
	}

	function IsPluginModule()
	{
		return true;
	}

	function DoAction($name, $id, $params, $return_id='')
	{
		$db = $this->cms->db;
		switch ($name)
		{
			case 'default':
				$query = "SELECT comment_id, comment_author, comment_data, comment_date FROM ".cms_db_prefix()."module_comments WHERE page_id = ".$this->cms->variables['page']." ORDER BY comment_date desc";
				if (isset($params["number"]))
				{
					$dbresult = $db->SelectLimit($query, $params['number']);
				}
				$dbresult = $db->Execute($query);

				$dateformat = "F j, Y, g:i a";
				if (isset($params['dateformat']))
				{
					$dateformat = $params['dateformat'];
				}
				
				echo $this->CreateLink($id, 'addcomment', $return_id, $this->Lang('addacomment'), $params);

				if ($dbresult && $dbresult->RowCount())
				{
					while ($row = $dbresult->FetchRow())
					{
						echo "<div class=\"modulecommentsentry\">\n";
						echo "<div class=\"modulecommentsentryheader\">\n";
						echo "<span class=\"modulecommentsentrydate\">".date($dateformat, $db->UnixTimeStamp($row['comment_date']))."</span>";
						echo " - ";
						echo "<span class=\"modulecommentsentryauthor\">".$row['comment_author']."</span>\n";
						echo "</div>\n";
						echo "<div class=\"modulecommentsentrybody\">".nl2pnbr($row["comment_data"])."</div>\n";
						echo "</div>\n";
					}
				}
				break;

			case 'addcomment':
				if (isset($params['cancelcomment']))
				{
					$this->RedirectContent($return_id);
				}

				$errormsg = "";

				$author = "";
				if (isset($params["author"]))
				{
					$author = $params["author"];
				}
				$content = "";
				if (isset($params["content"]))
				{
					$content = $params["content"];
				}

				$validinfo = true;

				if (isset($params['submitcomment']))
				{
					if ($author == "")
					{
						$validinfo = false;
						$errormsg .= "<li>".$this->Lang('errorenterauthor')."</li>";
					}
				
					if ($content == "")
					{
						$validinfo = false;
						$errormsg .= "<li>".$this->Lang('errorentercomment')."</li>";
					}

					if ($validinfo)
					{
						$db = $this->cms->db;
						$new_id = $db->GenID(cms_db_prefix()."module_comments_seq");
						$query = "INSERT INTO ".cms_db_prefix()."module_comments (comment_id, page_id, comment_author, comment_data, comment_date, create_date) VALUES ($new_id, $return_id, ".$db->qstr($author).", ".$db->qstr($content).",'".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."')";
						$dbresult = $db->Execute($query);
						$this->RedirectContent($return_id);
						return;
					}
				}

				if (strlen($errormsg) > 0)
				{
					echo "<p>".$this->Lang('error').":</p><ul>".$errormsg."</ul>";
				}

				echo $this->CreateFormStart($id, 'addcomment', $return_id);

				?>
				<h3>Add Comment</h3>

				<table>
					<tr>
						<td><?php echo $this->Lang('yourname')?>:</td>
						<td><?php echo $this->CreateInputText($id, 'author', $author)?></td>
					</tr>
					<tr>
						<td><?php echo $this->Lang('comment')?>:</td>
						<td><?php echo $this->CreateTextArea(false, $id, $content, 'content', $id)?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<?php echo $this->CreateInputSubmit($id, 'submitcomment', $this->Lang('submit')) ?>
							<?php echo $this->CreateInputSubmit($id, 'cancelcomment', $this->Lang('cancel')) ?>
						</td>
					</tr>
				</table>
				<?php

				echo $this->CreateFormEnd();
				break;
		}
	}
}

# vim:ts=4 sw=4 noet
?>
