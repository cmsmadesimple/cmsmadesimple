<?php
#Linkblog - a module for CMS - CMS Made Simple
#Copyright (c) 2004 by Greg Froese <heavy_g@users.sf.net>
#
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

include_once(dirname(__FILE__).'/linkblog.inc.php');
include_once(dirname(__FILE__).'/linkblog.admin.inc.php');

function linkblog_module_install($cms) {
	//This function should install the database functions and do other basic init stuff for first time use.
	$db = $cms->db;
	$dict = NewDataDictionary($db);

	## Create the actual linkblog table
	$flds = "
		linkblog_id I KEY,
		linkblog_title C(255),
		linkblog_url C(255),
		linkblog_author C(255),
		linkblog_type I,
		create_date T,
		modified_date T,
		status C(10)
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence(cms_db_prefix()."module_linkblog_seq");

	## Create the linkblog_types table and put in the initial data
	$flds = "
		linkblog_type_id I,
		linkblog_type C(255)
	";
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog_types", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);

	$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
	$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
	$query.= "($new_id_type, ".$db->qstr("News").")";
	$dbresult = $db->Execute($query);

	$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
	$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
	$query.= "($new_id_type, ".$db->qstr("Sports").")";
	$dbresult = $db->Execute($query);

	$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
	$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
	$query.= "($new_id_type, ".$db->qstr("Silly").")";
	$dbresult = $db->Execute($query);

	$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
	$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
	$query.= "($new_id_type, ".$db->qstr("Funny").")";
	$dbresult = $db->Execute($query);

	## Create the comments table
	$flds = "
		comment_id I KEY,
		linkblog_id I,
		author C(255),
		comment C(255),
		create_date T
	";
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog_comments", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);
	
	$db->CreateSequence(cms_db_prefix()."module_linkblog_comment_seq");
	cms_mapi_create_permission( $cms, 'Modify Linkblog', 'Modify Linkblog');
}

function linkblog_module_uninstall($cms) {
	//This function should uninstall database tables and generally cleanup.
	$db = $cms->db;
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog");
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog_types");
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog_comments");
	$dict->ExecuteSQLArray($sqlarray);

	$db->DropSequence(cms_db_prefix()."module_linkblog_seq");
	$db->DropSequence(cms_db_prefix()."module_linkblog_type_seq");
	$db->DropSequence(cms_db_prefix()."module_linkblog_comment_seq");

}

function linkblog_module_execute($cms, $id, $params) {
	//This is the entryway into the module.  All requests from CMS will come through here.
	//Default to showing the current days links, if no links from the current day, show the last day with links.

	$return_id = $cms->variables['page'];
	linkblog_module_showLinks($cms, $id, $params, $return_id);
	return;
}

function linkblog_module_executeuser($cms, $id, $return_id, $params) {

	if (isset($params[$id."cancellink"])) {
		cms_mapi_redirect_user_by_pageid($return_id);
		return;
	}

	linkblog_module_user_action($cms, $id, $return_id, $params);
}

function linkblog_module_help($cms) {
	?>
	<h3>What does this do?</h3>
	<p>LinkBlog is a module which allows users to post links and comments about the links to a page within your site.  It can be inserted into a template or content page as a module, however, it makes more sense as a content page because it does produce a complete page.</p>
	<h3>Anything else I should know?</h3>
	<p>You should know that if you uninstall this module it will delete all your links and comments.  If you do not wish to use it for a temporary time period, just deactivate it.</p>
	<h3>How do I use it?</h3>
	<p>As this is just a tag module, it's inserted into your page or template by using the cms_module tag.  Example syntax would be: <br /><code>{cms_module module="LinkBlog" allow_search="true"}</code></p>
	<h3>What parameters are there?</h3>
	<p>allow_search - set allow_search="true" to show a search form - off by default</p>
	<p>category - set category="NAME" where NAME is a category name.  Only links in that category will be visible.  This includes the links generated to previously posted links.</p>
	<p>make_rss_button - creates an image link to the rss feed for the linkblog.  The title for the rss feed is set by a config.php option: $config["linkblog_url"]<br />
The RSS feed title is set with $config["linkblog_rss_title"] in config.php.</p>
	<h3>How do I style the LinkBlog pages?</h3>
	<p>Here is some sample CSS you can throw in your template to make your results <i>slightly</i> prettier.<br />
	<pre>
.modulelinkblog a:hover { color: #000000;
                          text-decoration: none;
                          background-color: #FFFFFF;
}
div.modulelinkblog {
    background-color: #EDF2F5;
    color: #000000;
    text-align: left;
    font-size: 12px;
}

div.modulelinkblogentryheader {
    font-weight: bold;
    padding: 5px;
}

div.modulelinkblogentrybody {
    padding-left: 15px;
}

div.modulelinkbloglink {
    padding: 5px;
}

div.modulelinkblogcomment {
    padding-left: 35px;
    padding-bottom: 10px;
}

div.modulelinkblogentrycommentlink {
    padding-left: 35px;
}

	</pre></p>
	<h3>How can I help?</h3>
	<p>Come into #cms on irc.freenode.net and find out the latest and offer your services.</p>
	<?php

}

# vim:ts=4 sw=4 noet
?>
