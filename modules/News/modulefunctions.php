<?php

function news_module_install($cms)
{
	$db = $cms->db;
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
			$flds, $taboptarray );
	$dict->ExecuteSQLArray( $sqlarray );

	$db->CreateSequence( cms_db_prefix()."module_news_seq" );
	cms_mapi_create_permission( $cms, 'Modify News', 'Modify News');
}


function news_module_upgrade( $cms, $oldversion, $newversion )
{
	$current_version = $oldversion;
	if ($current_version == "1.0")
	{
		$db = $cms->db;
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "start_time T, end_time T, icon C(255)");
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "1.1";
	}
	switch($current_version)
	{
		case "1.1":
			case "1.2":
			case "1.3":
			case "1.4":
			$db = $cms->db;
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_cat C(255)");
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "1.5";
		break;
	}
}

function news_module_uninstall( $cms )
{
	$db = $cms->db;
	$dict = NewDataDictionary( $db );
	$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news" );
	$dict->ExecuteSQLArray($sqlarray);

	$db->DropSequence( cms_db_prefix()."module_news_seq" );
	cms_mapi_remove_permission($cms, 'Modify News');
}

function news_module_execute( $cms, $id, $params )
{
	news_module_executeuser( $cms, $id, $cms->variables['page'], $params );
}


function strip_to_length( $str, $len )
{
	while( $str != strip_tags( $str ) )
	{
		$str = strip_tags($str);
	}

	if( $len > 0 && $len < strlen($str) ) $str=substr($str,0,$len)."...";
	return $str;
}


function news_module_executeuser( $cms, $id, $return_id, $params )
{
	if (isset($params["makerssbutton"]))
	{
		$params = array_merge($params, array("showtemplate"=>"false","type"=>"rss"));
		echo cms_mapi_create_user_link('News', $id, $return_id, $params, "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Newsfeed\" />");
		return;
	}

	$db = $cms->db;
	if (isset($params["category"]))
	{
		$query =  "SELECT news_id, news_title, news_data, news_date FROM ";
		$query .= cms_db_prefix()."module_news WHERE news_cat = \"";
		$query .= $params["category"]."\" AND ((".$db->IfNull('start_time',"''");
		$query .= " = '' AND ".$db->IfNull('end_time',"''");
		$query .= " = '') OR (start_time < ".$db->DBTimeStamp(time());
		$query .= " AND end_time > ".$db->DBTimeStamp(time()).")) ORDER BY news_date desc";
	}
	else 
	{
		$query =  "SELECT news_id, news_cat, news_title, news_data, news_date ";
		$query .= "FROM ".cms_db_prefix()."module_news WHERE ";
		$query .= "((".$db->IfNull('start_time',"''")." = '' AND ".$db->ifNull('end_time',"''");
		$query .= " = '') OR (start_time < ".$db->DBTimeStamp(time());
		$query .= " AND end_time > ".$db->DBTimeStamp(time()).")) ORDER BY news_date desc";
	}

	if( isset( $params["number"]))
	{
		$dbresult = $db->SelectLimit($query, $params["number"]);
	}
	else
	{
		$dbresult = $db->Execute( $query );
	}

	$dateformat = "F j, Y, g:i a";
	if (isset($params["dateformat"]))
	{
		$dateformat = $params["dateformat"];
	}

	$type = "text";
	if (isset($params[$id."type"]))
	{
		$type = $params[$id."type"];
	}
	else if (isset($params["type"]))
	{
		$type = $params["type"];
	}

	if ($dbresult && $dbresult->RowCount() > 0)
	{
		if ($type == "rss")
		{
			header('Content-type: text/xml');
			echo "<xml version='1.0'>\n";
			echo "<rss version='2.0'>\n";
			echo "   <channel>\n";
		}

		while( ($row = $dbresult->FetchRow()) )
		{
			if ($type == "rss")
			{
				echo "        <item>\n";
				if( !isset( $params["category"] ) )
				{
					echo "            <title>".$row["news_cat"].": ".$row["news_title"]."</title>\n";
				}
				else
				{
					if( isset( $params["summary"] ) )
					{
						echo "            <title><a name=\"".$config["root_url"]."/index.php?page=".$params["summary"]."#".$row["news_id"]."\">".$row["news_title"]."</a></title>\n";
					}
					else 
					{
						echo "            <title>".strip_tags($row["news_title"])."</title>\n";
					}
				}
				if( isset( $params["summary"] ) )
				{
					echo "            <description>".strip_to_length($row["news_data"],$params["length"])."</description>\n";
				}
				else
				{
					echo "            <description>".strip_to_length($row["news_data"],0)."</description>\n";
				} 
				echo "            <pubDate>".gmdate('D d M, Y H:i:s', $db->UnixTimeStamp($row["news_date"]))." GMT</pubDate>\n";
				echo "        </item>\n";
			}
			else
			{
				echo "<object><div class=\"cms-module-news\">";
				echo "<div class=\"cms-module-news-header\">";
				if (!(isset($params['swaptitledate']) && 
						($params['swaptitledate'] == 'true' || 
						 $params['swaptitledate'] == '1')))
				{
					echo "<span class=\"cms-news-date\">".date($dateformat, $db->UnixTimeStamp($row['news_date']))."</span><br />";
				}
				$prestring = "<span class=\"cms-news-title\">";
				$poststring = "</span><br />";
				if( !isset( $params["no_anchors"] ) )
				{
					$prestring .= "<a name=\"".$row["news_id"]."\"></a>";
				}
				if (isset($params["showcategorywithtitle"]) && ($params["showcategorywithtitle"] == "true" || $params["showcategorywithtitle"] == "1"))
				{
					echo $prestring.$row["news_cat"].": ".$row["news_title"].$poststring;
				}
				else
				{
					echo $prestring.$row["news_title"].$poststring;
				}
				if (isset($params['swaptitledate']) && 
						($params['swaptitledate'] == 'true' || 
						 $params['swaptitledate'] == '1'))
				{
					echo "<span class=\"cms-news-date\">".date($dateformat, $db->UnixTimeStamp($row['news_date']))."</span><br />";
				}
				echo "</div>";
				if( isset( $params["summary"] ) )
				{
					echo "<span class=\"cms-news-content\">".strip_to_length($row["news_data"],$params["length"])."</span>";
					if (strlen($row["news_data"]) >$params["length"])
					{
						$moretext = "more...";
						if (isset($params['moretext']))
						{
							$moretext = $params['moretext'];
						}
						echo "<br><a href=\"index.php?page=".$params["summary"]."#".$row["news_id"]."\">$moretext</a>";
					}
				}
				else
				{
					echo "<span class=\"cms-news-content\">".$row["news_data"]."</span>";
				}
				echo "</div></object>";
			}
		}

		if ($type == "rss")
		{
			echo "   </channel>\n";
			echo "</rss>";
		}
	}
}


function get_var( $var )
{
	if( isset( $_POST[$var] ) )
	{
		return $_POST[$var];
	}
	if( isset( $HTTP_GET_VARS[$var] ) )
	{
		return $HTTP_GET_VARS[$var];
	}
	return;
}

function news_module_executeadmin($cms,$id)
{
	$access = cms_mapi_check_permission($cms, "Modify News");
	$newscat = get_var( $id."news_cat" );
	if( !isset( $newscat ) || (strlen($newscat) == 0) )
	{
		$newscat = get_var( $id."add_news_cat" );
	}
	$moduleaction = (isset($_POST[$id."action"])?$_POST[$id."action"]:"") . 
		(isset($_GET[$id."action"])?$_GET[$id."action"]:"");

	if (!$access)  {
		echo "<p class=\"error\">You need the 'Modify News' permission to perform that function.</p>";
		return;
	}

	$db = $cms->db;
	if (isset($moduleaction) && strlen($moduleaction) > 0)  
	{
# do the add or edit, etc, etc, etc.
		include_once(dirname(__FILE__)."/adminform.php");
	}
	else
	{
#    if( !isset( $newscat ) || strlen( $newscat ) == 0 ) 
#    {
#      $query = "SELECT news_cat FROM "
#           .cms_db_prefix()."module_news GROUP BY news_cat";
#      $dbresult = $db->Execute($query);
#
#      if ($dbresult && $dbresult->RowCount())
#      {
#        echo cms_mapi_create_admin_form_start("News", $id);
#        echo "<TABLE><TR ALIGN=\"BOTTOM\"><TD ALIGN=\"CENTER\">";
#        echo "<H3>Select Category:</H3>";
#        echo "<SELECT NAME=\"".$id."news_cat\" SIZE=\"4\">";
#        while ($row = $dbresult->FetchRow())
#        {
#          $x = $row["news_cat"];
#          if( strlen($x) == 0 )
#          {
#            $x = "**Empty**";
#          }
# 
#          echo "<OPTION>".$x."</OPTION>";
#        } 
#        echo "</SELECT></TD>";
#        echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"".$id."maction\" VALUE=\"Select\" /></TD>";
#        echo "</TR></TD>";
#        echo cms_mapi_create_admin_form_end();
#      }
#
#      echo cms_mapi_create_admin_form_start("News", $id);
#      echo "<TABLE>";
#      echo "<TR VALIGN=\"BOTTOM\"><TD>";
#      echo "<H3>New Category:</H3>";
#      echo "<INPUT TYPE=\"TEXT\" NAME=\"".$id."add_news_cat\" SIZE=\"40\" MAXLENGTH=\"255\">";
#      echo "</TD>";
#      echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"".$id."maction\" VALUE=\"Add\" /></TD>";
#      echo "</TR></TABLE>";
#      echo "".cms_mapi_create_admin_form_end();
#    }
#    else 
#    {

	$query = "SELECT news_cat FROM "
		.cms_db_prefix()."module_news GROUP BY news_cat";
	$dbresult = $db->Execute($query);
	if ($dbresult && $dbresult->RowCount())
	{
		echo cms_mapi_create_admin_form_start("News", $id);
		echo "<TABLE><TR>";
		echo "<TD><H5>Filter:</H5></TD>";
		echo "<TD><SELECT NAME=\"".$id."news_cat\">";
		echo "<OPTION";
		if ($newscat == "All")
		{
			echo " SELECTED";
		}
		echo ">All</OPTION>";
		while ($row = $dbresult->FetchRow())
		{
			$x = $row["news_cat"];
			if( strlen($x) == 0 )
			{
				$x = "**Empty**";
			}
			echo "<OPTION";
			if ($newscat == $x)
			{
				echo " SELECTED";
			}
			echo ">".$x."</OPTION>";
		}
		echo "</SELECT></TD>";
		echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"".$id."filter\" VALUE=\"Select\" /></TD>";
		echo "</TR></TABLE>";
		echo cms_mapi_create_admin_form_end();
	}

	if( isset($newscat) && strlen($newscat) ) 
	{
		if( $newscat == "All" )
		{
			echo "<H4>All Entries:</H4><BR>";
			$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
				.cms_db_prefix()."module_news ORDER BY news_date desc";
		}
		else if( $newscat == "**Empty**" )
		{
			echo "<H4>**Empty** Entries:</H4><BR>";
			$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
				.cms_db_prefix()."module_news ORDER BY news_date desc";
		}
		else 
		{
			echo "<H4>$newscat Entries:</H4><BR>";
			$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
				.cms_db_prefix()."module_news WHERE news_cat = \""
				.$newscat."\" ORDER BY news_date desc";
		}
	}
	else
	{
		echo "<H4>All Entries:</H4><BR>";
		$query = "SELECT news_id, news_cat, news_title, news_data, news_date FROM "
			.cms_db_prefix()."module_news ORDER BY news_date desc";
	}

	$dbresult = $db->Execute($query);
	if ($dbresult && $dbresult->RowCount())
	{ 
		echo "<table cellspacing=\"0\" class=\"admintable\">\n";                        echo "<tr>\n";
		echo "<td width=\"2%\">&nbsp;</td>\n";
		echo "<td width=\"50%\">Title</td>\n";
		echo "<td width=\"10%\">Category</td>\n";
		echo "<td width=\"20%\">Posting Date</td>\n";
		echo "<td width=\"8%\">&nbsp;</td>\n";
		echo "<td width=\"10%\">&nbsp;</td>\n";
		echo "</tr>\n";
		$rowclass="row1";
		$r=1;
		while ($row = $dbresult->FetchRow()) 
		{
			echo "<tr class=\"$rowclass\">\n";
			echo "<td align=\"right\">".$r."</td>\n"; $r++;
			echo "<td>".$row["news_title"]."</td>\n";
			echo "<td>".$row["news_cat"]."</td>\n";
			echo "<td>".$row["news_date"]."</td>\n";
			echo "<td>".cms_mapi_create_admin_link("News",$id,array("action"=>"edit","news_id"=>$row["news_id"],"news_cat"=>$newscat),"Edit")."</td>\n";
			echo "<td>".cms_mapi_create_admin_link("News",$id,array("action"=>"delete","news_id"=>$row["news_id"]),"Delete", "Are you sure you want to delete?")."</td>\n";
			echo "</tr>\n";
			($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
		}
		echo "</table>\n";
	}
	else
	{
		echo "<p><b>No</b> news items found for category: ".$newscat."</p>";
	}
	echo cms_mapi_create_admin_form_start("News", $id);
	echo "<input type=\"hidden\" name=\"".$id."news_cat\" value=\"$newscat\">";
	echo "<input type=\"hidden\" name=\"".$id."action\" value=\"add\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"Add News Item\">"; 
	echo cms_mapi_create_admin_form_end();
#    }
	}
}

function news_module_help($cms)
{
	?>

		<h3>What does this do?</h3>
		<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to the bottom menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
		<h3>Security</h3>
		<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
		<h3>How do I use it?</h3>
		<p>The easiest way to use it is in conjunction with the cms_module tag.  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{cms_module module="news" number="5" category="beer"}</code></p>
		<h3>What Parameters Exist?</h3>
		<p>
		<ul>
		<li><em>(optional)</em> number="5" - Maximum number of items to display =- leaving empty will show all items</li>
		<li><em>(optional)</em> dateformat - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
		<li><em>(optional)</em> makerssbutton="true" - Make a button to
		link to an RSS feed of the News items</li>
		<li><em>(optional)</em> swaptitledate="true" - Switch the order
		of the date and title</li>
		<li><em>(optional)</em> category="category" - Only display items for that category.  leaving empty, will show all categories</li>
		<li><em>(optional)</em> summary="page" - Activate summary mode, links are placed in the title of each summary article, and the page is trimmed to "length" characters</li>
		<li><em>(optional)</em> length="80" - Used in summary mode (see above) this trims the length of each article to the specified number of characters after stripping all html tags.</li>
		<li><em>(optional)</em> showcategorywithtitle="true" - Display the title with the category in front of it (Category: Title).  Leave false for old style behavior.</li>
		<li><em>(optional)</em> moretext="more..." - Text to display at the end of a news item if it goes over the summary length.  Defaults to "more...".</li>
		</ul>
		</p>
	<?php
}

function news_module_about() 
{
	?>

		<p>Author: Robert Campbell &lt;rob@techcom.dydnsns.org&gt;</p>
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
		</ul>
	<?php
}

# vim:ts=4 sw=4 noet
?>
