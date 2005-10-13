<?php
class Faq
{
  var $module;
  var $returnid;
  
	function Faq($mod, $rid){
	 $this->module = $mod;
	 $this->returnid = $rid;
	}

 	function get_categories($cms){
	        $db = $cms->db;

		$query  = "SELECT * FROM `";
		$query .= cms_db_prefix()."module_Faq_categories` ;";

		$dbresult = $db->Execute($query);

		$categories = null;

		if ($dbresult && $dbresult->RowCount()) {
			$categories = array();
			$ind = 0;
			while ($row = $dbresult->FetchRow()) {
			        $categories[$ind] = array("id" => $row["id"], "title" => $row["title"]);
	                        $ind++;
			}
		}
		return $categories;
	}
	

	function get_category_title($cms,$id){
	        $db = $cms->db;

		$query  = "SELECT `title` FROM `";
		$query .= cms_db_prefix()."module_Faq_categories` ";
		$query .= "WHERE `id` = '".$id."' LIMIT 1 ;";


		$dbresult = $db->Execute($query);

		$title = null;

		if ($dbresult && $dbresult->RowCount()) {
			$title = "";
			$ind = 0;
			while ($row = $dbresult->FetchRow()) {
			        $title = $row["title"];
			}
		}
		return $title;
	}
	
	
	function get_faq($cms,$id){
	        $db = $cms->db;

		$query  = "SELECT `id`, `question` , `answer` FROM `";
		$query .= cms_db_prefix()."module_Faq_questions` ";
		$query .= "WHERE `id` = '".$id."' LIMIT 1 ;";


		$dbresult = $db->Execute($query);

		$faq = null;

		if ($dbresult && $dbresult->RowCount()) {
			$faq = "";
			$ind = 0;
			while ($row = $dbresult->FetchRow()) {
			        $faq = array("id" => $row["id"],
					"question" => $row["question"],
					"answer" => $row["answer"]);
			}
		}
		return $faq;
	}
	
	

	function ShowCategories($cms,$id)
	{
	global $themeObject;
    $categories = $this->get_categories($cms);

		if (count($categories)) {
			echo '<table cellspacing="0" class="pagetable">'."\n";
			echo '<tr><td>Category</td><td width="16">&nbsp;</td></tr>'."\n";
			$line = 0;
			$ind = 0;
			while ($ind < count($categories)) {
			        $cat = $categories[$ind];
			        echo '<tr class="row'.($line+1).'">';
					echo '<td>';
				
// 			        echo cms_mapi_create_admin_link("faqX",$id,
// 							array("action"=>"show_cat", "Cat"=>$cat["id"]),
// 					$cat["title"]);
					
				echo $this->module->CreateLink($id, 'faqX', $this->returnid, 
        $cat["title"], 
        array("action"=>"show_cat", "Cat"=>$cat["id"]) );					
                echo '</td>';

				echo '<td width="16">';
				
				
// 				echo cms_mapi_create_admin_link("faqX",$id,
// 					array("action"=>"deleteCat", "Cat"=>$cat["id"]),
// 					'<img border="0" src="'.$cms->config["root_url"].'/images/cms/delete.gif"/>',
// 					"Are you sure you want to delete?");
				
				echo $this->module->CreateLink($id, 'faqX', $this->returnid, 
   		$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'),

        array("action"=>"deleteCat", "Cat"=>$cat["id"]),
        "Are you sure you want to delete?");
					
				echo '</td></tr>'."\n";
                                $line = ($line+1) % 2;
                                $ind++;
			}
			echo '</table>';
		}
		else {
			echo '<br/>There aren\'t FAQ.';
		}
	}
	



	function get_faqs($cms,$cat){
	        $db = $cms->db;

		$query  = "SELECT * FROM `";
		$query .= cms_db_prefix()."module_Faq_questions` WHERE `cnum` =";
		$query .= "'".$cat."' ;";

		$dbresult = $db->Execute($query);

		$faqs = null;

		if ($dbresult && $dbresult->RowCount()) {
			$faqs = array();
			$ind = 0;
			while ($row = $dbresult->FetchRow()) {
			        $faqs[$ind] = array("id" => $row["id"], "question" => $row["question"]);
	                        $ind++;
			}
		}
		return $faqs;
	}


	
	function ShowCategory($cms,$id,$cat)
	{
                $faqs = $this->get_faqs($cms,$cat);
	        
		if (count($faqs)) {
			echo '<table cellspacing="0" class="pagetable">'."\n";
			echo '<tr><td>Question</td>';
			echo '<td align="right">Category <b>';
			echo $this->get_category_title($cms,$cat).'</b></td>';
			echo '<td width="16" class="pageicon">&nbsp;</td><td class="pageicon" width="16">&nbsp;</td></tr>'."\n";
			$line = 0;
			$ind = 0;
			while ($ind < count($faqs)) {
			        $faq = $faqs[$ind];
			        echo '<tr class="row'.($line+1).'">';
			    					
				echo '<td>';
				
				echo $this->module->CreateLink($id, 'faqX', $this->returnid,
				$faq["question"], array("action"=>"edit","id"=>$faq["id"], "Cat"=>$cat) );
				
				echo '</td>';
				echo '<td>&nbsp;</td>';

				echo '<td width="16" class="pageicon">';
				
//         echo cms_mapi_create_admin_link("faqX",$id,
// 					array("action"=>"edit","id"=>$faq["id"], "Cat"=>$cat),
// 					'<img border="0" src="'.$cms->config["root_url"].'/images/cms/edit.gif"/>');


// /admin/themes/default/images/icons/system/edit.gif
	global $themeObject;
	
				echo $this->module->CreateLink($id, 'faqX', $this->returnid, 
		$themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon'),
        array("action"=>"edit","id"=>$faq["id"], "Cat"=>$cat) );						
					
					
				echo '</td>'."\n";
				echo '<td width="16" class="pageicon">';
				
// 				echo cms_mapi_create_admin_link("faqX",$id,
// 					array("action"=>"delete","id"=>$faq["id"], "Cat"=>$cat),
// 					'<img border="0" src="'.$cms->config["root_url"].'/images/cms/delete.gif"/>',
// 					"Are you sure you want to delete?");

				echo $this->module->CreateLink($id, 'faqX', $this->returnid, 
//        '<img border="0" src="'.$cms->config["root_url"].'/images/cms/delete.gif"/>', 
		$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'),

        array("action"=>"delete","id"=>$faq["id"], "Cat"=>$cat),
        "Are you sure you want to delete?");					
				echo '</td>';
					
                                echo '</tr>'."\n";
                                $line = ($line+1) % 2;
                                $ind++;
			}
			echo '</table>';
		}
		else {
			echo '<br/>There aren\'t FAQ in this category.';
		}
	}


	function show_faq($cms, $id, $action){
		if (strcmp($_REQUEST[m1_action], "edit")==0){
            	        $faq = $this->get_faq($cms,$_REQUEST[m1_id]);
		        
		}
		else {
		        $faq = array("id" => "","question" => "", "answer" => "");
		}
		
		echo '<input type="hidden" name="'.$id.'id" value="'.$faq["id"].'"/>';
		echo '<input type="hidden" name="'.$id.'action" value="'.$action.'"/>';
		echo '<table width="100%" border="0">';
		echo '<tr><td align="right">Category:</td><td>';
		echo '<select name="'.$id.'sel_cat" size="4">';
		$categories = $this->get_categories($cms);
		for($i=0;$i<count($categories);$i++){
	    		echo '<option value="'.$categories[$i]["id"].'" ';
	    		if (strcmp($_REQUEST[m1_Cat],$categories[$i]["id"])==0)
				echo ' selected="selected"';
			echo '>'.$categories[$i]["title"].'</option>';
		}
	    	echo '</select>';
		echo '</td></tr>';
	        echo '<tr>';
		echo '<td align="right">New category:</td>';
		echo '<td><input type="text" name="'.$id.'add_cat" value=""/></td>';
	        echo '</tr><tr>';
		echo '<td align="right">Question:</td>';
		echo '<td><input type="text" size="60" name="'.$id.'faq" value="'.$faq["question"].'"/></td>';
	        echo '</tr><tr>';
	        echo '<td align="right">Answer:</td><td>';
		echo create_textarea(true, $faq["answer"], $id.'answer', 'syntaxHighlight', $id.'answer');
		echo '</td></tr>';
		echo '<tr>';
	        echo '<td>&nbsp;</td><td>';
		echo '<input type="submit" name="'.$id.'submit" value="'.strtoupper($action).'"/>';
		echo '</td></tr></table>';
	}
	
	
	function del_faq($cms, $id){
		$db = $cms->db;

		$query  = "DELETE FROM `";
		$query .= cms_db_prefix()."module_Faq_questions` WHERE `id` = '";
		$query .= $_REQUEST[m1_id]."' LIMIT 1;";

		if ($db->Execute($query))
			echo "DELETED.<br/>";
		else
			echo "FAULT on DELETE.<br/>";

	}

	function del_faq_cat($cms, $id, $cat){
	        $db = $cms->db;

		$faqs = $this->get_faqs($cms,$cat);

		if (!$faqs){
			$query  = "DELETE FROM `";
			$query .= cms_db_prefix()."module_Faq_categories` WHERE `id` = '";
			$query .= $cat."' LIMIT 1;";

			if ($db->Execute($query))
				echo "DELETED.<br/>";
			else
				echo "FAULT on DELETE.<br/>";
		}
		else {
			echo "This category isn't empty.";
		}
	}

	function InsertFaq($cms,$id){
		$db = $cms->db;
		
		if (strcmp($_POST[$id."add_cat"],"")!=0)
		        $cnum = $this->InsertCat($cms,$_POST[$id."add_cat"]);
		else {
		        if (strcmp($_POST[$id."sel_cat"],"")!=0)
				$cnum = $_POST[$id."sel_cat"];
			else {
    				echo 'You MUST select a category or WRITE new one.<br/>';
				return;
			}
		}
		
    $query  = "INSERT INTO `".cms_db_prefix()."module_Faq_questions` ( `id` , `cnum` , `question` , `answer` )";
		$query .= "VALUES (";
		$query .= "'', ";
		$query .= "'".$cnum."', ";
		$query .= "'".$_POST[$id."faq"]."', ";
		$query .= "'".$_POST[$id."answer"]."'";
		$query .= ");";
		
		
		if ($db->Execute($query))
			echo "INSERTED.<br/>";
		else
			echo "FAULT on INSERT.<br/>";

                return $cnum;
                //$this->get_faq($cms,$id);
	}
	
	
	function InsertCat($cms,$title) {
	        $db = $cms->db;
	        
                $query  = "INSERT INTO `".cms_db_prefix()."module_Faq_categories` ( `id` , `title` )";
		$query .= "VALUES (";
		$query .= "'', ";
		$query .= "'".$title."'";
		$query .= ");";
		$dbresult = $db->Execute($query);
		
                $query  = "SELECT `id` FROM `".cms_db_prefix()."module_Faq_categories` WHERE ";
		$query .= "`title` = '".$title."'";
		$query .= " LIMIT 1;";
		$dbresult = $db->Execute($query);
		
                while ($row = $dbresult->FetchRow()) {
		        $cnum = $row["id"];
		}
		
		return $cnum;
	}
	
	
	function UpdateFaq($cms,$id){
  		if (strcmp($_POST[$id."cat"],"")!=0)
		        $cnum = $this->InsertCat($cms,$_POST[$id."sel_cat"]);
		else
		        $cnum = $_POST[$id."sel_cat"];

		$db = $cms->db;
	
		$query  = "UPDATE `";
		$query .= cms_db_prefix()."module_Faq_questions` SET ";
		$query .= " `question` = '".addslashes($_POST[$id."faq"])."', ";
		$query .= " `answer` = '".addslashes($_POST[$id."answer"])."', ";
		$query .= " `cnum` = '".$cnum."' ";
		$query .= " WHERE `id` = '".$_POST[$id."id"]."' LIMIT 1 ;";

		if ($db->Execute($query))
			echo "UPDATED.<br/>";
		else
			echo "FAULT on UPDATE.<br/>";
		
		return $cnum;
	}

}

?>
