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

if (!class_exists("Faq"))
	require_once("class.Faq.php");

function Faq_module_install($cms) {
	//This function should install the database functions and do other basic init stuff for first time use.


	$db = $cms->db;

	$query  = "CREATE TABLE ";
	$query .= cms_db_prefix()."module_Faq_categories (";
	$query .= "id int(11) NOT NULL auto_increment, ";
	$query .= "title varchar(255) NOT NULL, ";
	$query .= "PRIMARY KEY (id), ";
	$query .= "UNIQUE id_2 (id), ";
	$query .= "UNIQUE KEY `idx_title` (`title`), ";
	$query .= "KEY id (id) ";
	$query .= ");";

	$dbresult = $db->Execute($query);

	$query  = "CREATE TABLE `".cms_db_prefix()."module_Faq_questions` (
 `id` int(11) NOT NULL auto_increment, 
 `cnum` int(11) DEFAULT '0' NOT NULL, 
 `question` varchar(255) NOT NULL, 
 `answer` text, 
 PRIMARY KEY (`id`), 
 UNIQUE `id_2` (`id`), 
 KEY `id` (`id`) 
) ;
 ";
 
	$dbresult = $db->Execute($query);
	
}

function Faq_module_upgrade($cms, $oldversion, $newversion)
{

}

function Faq_module_uninstall($cms) {
	//This function should uninstall database tables and generally cleanup.

	$db = $cms->db;
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_Faq_categories");
	$dict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_Faq_questions");
	$dict->ExecuteSQLArray($sqlarray);

}


if (!class_exists("faq_viewer"))
	require_once("class.faqmanager.php");

function Faq_module_execute($module,$id,$returnid)
{
  $cms = $module->cms;
	//This is the entryway into the module.  All requests from CMS will come through here.

	$cfg  = $cms->config;
	$vars = $cms->variables;
	echo '<table class="faq_cat" ><tr><td>';

	$faq = new faq_viewer( $cfg["db_username"],
			       $cfg["db_password"],
			       $cfg["db_hostname"],
			       $cfg["root_url"]."/index.php?page=".$vars["page_name"]);
	$faq->setDBvars($cfg["db_name"],
			cms_db_prefix()."module_Faq_questions",
			cms_db_prefix()."module_Faq_categories");
			
	$faq->connectDB();

	if (isset($search)) $faq->searchQuestions();
	else if (isset($_REQUEST["cat"])) $faq->showQuestionList();
	else $faq->showCategoryList();
	echo '</td></tr></table>';
}



function Faq_module_executeuser($cms, $id, $return_id, $params) {

}



function Faq_module_executeadmin($module,$id,$returnid)
{
  $cms = $module->cms;
// 	$access = cms_mapi_check_permission($cms, "Faq Admin");
// 
// 	if (!$access)  {
// 		echo "<p class=\"error\">You need the 'Modify Faq' permission to perform that function.</p>";
// 		return;
// 	}



//         echo cms_mapi_create_admin_link("faqX",$id, array("action"=>"show_cats"),
// 			'<input type="button" value="Show Categories"/>');
			
			echo $module->CreateLink($id, 'faqX', $returnid, 
        '<input type="button" value="Show Categories"/>', array("action"=>"show_cats"));
			
	     echo "&nbsp;&nbsp;";
	     
// 	     echo $this->module->CreateLink($id, 'new', $this->returnid, 'ADD NEW WORD');

      echo $module->CreateLink($id, 'faqX', $returnid, 
        '<input type="button" value="ADD NEW FAQ"/>', array("action"=>"new", "Cat"=>$_REQUEST["m1_Cat"]));
      
//         echo cms_mapi_create_admin_link("faqX",$id, array("action"=>"new", "Cat"=>$_REQUEST["m1_Cat"]),
// 			'<input type="button" value="ADD NEW FAQ"/>');

        $Faq = new Faq($module, $returnid);

	if (isset($_REQUEST["m1_action"])){
// 	        echo cms_mapi_create_admin_form_start("faqX", $id);
	        echo $module->CreateFormStart($id);
	        
		switch ($_REQUEST["m1_action"]) {
		    case "deleteCat":
		        $Faq->del_faq_cat($cms, $id, $_REQUEST["m1_Cat"]);
	        	$Faq->ShowCategories($cms,$id);
		        break;
		    case "show_cats":
	        	$Faq->ShowCategories($cms,$id);
		        break;
		    case "show_cat":
	        	$Faq->ShowCategory($cms,$id,$_REQUEST["m1_Cat"]);
		        break;
		    case "update":
	        	$cat = $Faq->UpdateFaq($cms,$id);
			$Faq->ShowCategory($cms,$id, $cat);
		        break;
		    case "insert":
	        	$cat = $Faq->InsertFaq($cms,$id);
	        	$Faq->ShowCategory($cms,$id, $cat);
		        break;
		    case "new":
		        $Faq->show_faq($cms, $id, "insert");
		        break;
		    case "edit":
		        $Faq->show_faq($cms, $id, "update");
		        break;
		    case "delete":
		        $Faq->del_faq($cms, $id);
	        	$Faq->ShowCategory($cms,$id, $_REQUEST["m1_Cat"]);
		        break;
		    default:
	        	$Faq->ShowCategories($cms,$id);
		}
//                 echo cms_mapi_create_admin_form_end();
                echo $module->CreateFormEnd();
	}
	else
	        $Faq->ShowCategories($cms,$id);

}

# vim:ts=4 sw=4 noet
?>
