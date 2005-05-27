<?php
//
// class.faqmanager.php
//
// Description
//
// This class allows for easy display of a FAQ that is stored in a MySQL
// database.  You can also perform a rudementary search on the questions
// and answers.
//
// The class requires two tables to exist, with the following structures:
//
//		CREATE TABLE faq_categories (
//		   id int(11) DEFAULT '0' NOT NULL auto_increment,
//		   title varchar(255) NOT NULL,
//         UNIQUE KEY `idx_title` (`title`),
//		   PRIMARY KEY (id),
//		   UNIQUE id_2 (id),
//		   KEY id (id)
//		);
//		
//		CREATE TABLE faq_questions (
//		   id int(11) DEFAULT '0' NOT NULL auto_increment,
//		   cnum int(11) DEFAULT '0' NOT NULL,
//		   question varchar(255) NOT NULL,
//		   answer text,
//		   PRIMARY KEY (id),
//		   UNIQUE id_2 (id),
//		   KEY id (id)
//		);
//
// The names of the tables can be somewhat arbitrary because a class function
// is used to connect to the database and the the table names are passed
// along to it.
//
// Contributions
//
// This class is based on the FAQmanager perl script by Richard Lawrence,
// which can be found at: http://www.fourteenminutes.com/code/faqmanager/
// It looks like his with permission ;)
//
// Pricing
//
// This class is free for non-commerical use.  If you would like to use
// it in a commerical setting, then please contact php@amnuts.com and
// we'll come to some arrangement.
//
// Example of use
//
//		include "class.faqmanager.php";
//		$faq = new faq_viewer("user","pass","localhost");
//		$faq->setDBvars("test","faq_questions","faq_categories");
//		$faq->connectDB();
//		
//		if (isset($search)) $faq->searchQuestions();
//		else if (isset($cat)) $faq->showQuestionList();
//		else $faq->showCategoryList();
//
// To Do:
//
// I have some administration functions written, but not put into a class
// format yet.  The faq_viewer class would be extended with them.
//
// 
// Andrew Collington, 2002
// php@amnuts.com, http://php.amnuts.com/
//

class faq_viewer
{

	var $user;
	var $pass;
	var $host;
	var $id;
	var $db;
	var $tbl_cats;
	var $tbl_ques;
	var $gpcVars;
	var $_self;

	function faq_viewer($user="username",$pass="password",$host="localhost", $_self="")
	{
		global $HTTP_GET_VARS,$PHP_SELF;

		$this->user    = $user;
		$this->pass    = $pass;
		$this->host    = $host;
		$this->id      = NULL;
		$this->gpcVars = $_GET; //$HTTP_GET_VARS;  // could use $_GET in 4.1.0
		$this->_self   = $_self;
		
		$this->_class_name    = "PHP-based FAQ Manager";
		$this->_class_author  = "Andrew Collington";
		$this->_class_version = "1.0.0";
	}

	function setDBvars($db="dbname",$qtbl="faq_questions",$ctbl="faq_categories")
	{
		$this->db       = $db;
		$this->tbl_ques = $qtbl;
		$this->tbl_cats = $ctbl;
	}

	function connectDB()
	{
		$this->id = @mysql_connect($this->host,$this->user,$this->pass) or 
			die("Unable to connect to mysql server: {$this->host}");
		@mysql_select_db($this->db,$this->id) or
			die("Unable to select database: {$this->db}");
	}


	function showCategoryList()
	{
		$cresult = @mysql_query("SELECT * FROM {$this->tbl_cats} ORDER BY id",$this->id)
					or die("Unable to perform query: SELECT * FROM {$this->tbl_cats} ORDER BY id");
		if (!@mysql_num_rows($cresult))
		{
			echo "<p>There are currently no categories in the FAQ.</p>\n";
			return;
		}
		else
		{
			// do the list
			while ($crow = @mysql_fetch_array($cresult,MYSQL_ASSOC))
			{
				echo "<p class=\"category\"><a href=\"{$this->_self}&cat={$crow['id']}\">", stripslashes($crow['title']), "</a></p>\n";
				unset($qresult);
				unset($qrow);
				$qresult = @mysql_query("SELECT * FROM {$this->tbl_ques} WHERE cnum={$crow['id']}",$this->id)
								or die("Unable to perform query: SELECT * FROM {$this->tbl_ques} WHERE cnum={$crow['id']}");
				if (!@mysql_num_rows($qresult))
				{
					echo "<dl><dd class=\"question\">There are currently no questions for this category.</dd></dl><br>\n";
				}
				else
				{
					echo " <ol>\n";
					$num = 1;
					while ($qrow = @mysql_fetch_array($qresult,MYSQL_ASSOC))
					{
						echo "	<li class=\"question\"> <a href=\"{$this->_self}&cat={$crow['id']}#",($num++),"\">", stripslashes($qrow['question']), "</a></li><br>\n";
					}
					echo " </ol>\n";
				}
				@mysql_free_result($qresult);
			}
			@mysql_free_result($cresult);
		}
	}


	function showQuestionList()
	{
		// get current id and category title
		$query = "SELECT id,title FROM {$this->tbl_cats} WHERE id={$this->gpcVars['cat']}";
		$cresult = @mysql_query("SELECT id,title FROM {$this->tbl_cats} WHERE id={$this->gpcVars['cat']}",$this->id)
					or die("Unable to perform query: $query");
		$crow = @mysql_fetch_array($cresult,MYSQL_ASSOC);
		// if no category exists any more
		if (!$crow['id'])
		{
			$this->showCategoryList();
			return;
		}

		// show current title
		echo "<p class=\"category\">{$crow['title']}</p>\n";

		// get categories into array to get first/last/total
		$cresult = @mysql_query("SELECT * FROM {$this->tbl_cats}",$this->id)
					or die("Unable to perform query: SELECT * FROM {$this->tbl_cats}");
		while ($crow = @mysql_fetch_array($cresult,MYSQL_ASSOC))
		{
			$cats[] = $crow;
		}
		@mysql_free_result($cresult);
		$at = $this->_indexValue($cats,$this->gpcVars['cat']);
		
		// get 'back to' if needs be
		if ($at > 0)
		{
			$cresult = @mysql_query("SELECT id,title FROM {$this->tbl_cats} WHERE id={$cats[$at-1]['id']}",$this->id);
			$crow = @mysql_fetch_array($cresult,MYSQL_ASSOC);
			echo "<span class=\"navigation\">Backward to <a href=\"{$this->_self}&cat={$crow['id']}\">",stripslashes($crow['title']),"</a></span><br>\n";
		}
		@mysql_free_result($cresult);
		
		// get 'forward to' if needs be
		if (($at < count($cats)-1) && ($at != -1))
		{
			$cresult = @mysql_query("SELECT id,title FROM {$this->tbl_cats} WHERE id={$cats[$at+1]['id']}",$this->id);
			$crow = @mysql_fetch_array($cresult,MYSQL_ASSOC);
			echo "<span class=\"navigation\">Forward to <a href=\"{$this->_self}&cat={$crow['id']}\">",stripslashes($crow['title']),"</a></span><br>\n";
		}
		@mysql_free_result($cresult);

		echo "\n<hr width=\"100%\" size=\"2\" color=\"#000000\" noshade>\n\n";

		// print out the questions
		$qresult = @mysql_query("SELECT * FROM {$this->tbl_ques} WHERE cnum={$this->gpcVars['cat']}",$this->id)
						or die("Unable to perform query: SELECT * FROM {$this->tbl_ques} WHERE cnum={$this->gpcVars['cat']}");
		if (!@mysql_num_rows($qresult))
		{
			echo "<p class=\"answer\">There are no questions curently in this category.</p>\n";
			echo "<p class=\"small\">[ <a href=\"{$this->_self}\">contents</a> ]</p>\n";
		}
		else
		{
			$num = 1;
			while ($qrow = @mysql_fetch_array($qresult,MYSQL_ASSOC))
			{
				echo "<p><a name=\"{$num}\" class=\"question\">{$num}.  ",stripslashes($qrow['question']), "</a><p>\n";
				if ($qrow['answer'] != "") echo "<p class=\"answer\">",stripslashes($qrow['answer']), "</p>\n";
				else echo "<p class=\"answer\">No answer has been given to this question.</p>\n";
				echo "<p class=\"small\">[ <a href=\"{$this->_self}\">contents</a> | <a href=\"{$this->_self}&cat={$this->gpcVars['cat']}\">top</a> ]</p>\n";
				if ($num++ < (@mysql_num_rows($qresult))) echo "<p><table width=\"100%\" border=\"0\" cellpadding=\"0\"><tr><td><hr width=\"100%\" size=\"1\" color=\"#AAAAAA\" noshade></td></tr></table></p>\n";
			}
			@mysql_free_result($qresult);
		}
	}

	// given the array of category rows it'll return the index value
	// in the array for a given category number
	function _indexValue($cats,$id)
	{
		for ($i=0; $i<count($cats); $i++)
		{
			if ($cats[$i]['id'] == $id) return $i;
		}
		return -1;
	}

	// display a form to allow searching of the faq
	function showSearchForm()
	{
		echo "<form action=\"{$this->_self}\">Search the FAQ &nbsp; ";
		echo "<input type=\"text\" name=\"search\" size=\"10\"> ";
		echo "<input type=\"submit\" value=\"search\"></form>";
	}

	function searchQuestions()
	{
		$cresult = @mysql_query("SELECT * FROM {$this->tbl_cats} ORDER BY id",$this->id)
					or die("Unable to perform query: SELECT * FROM {$this->tbl_cats} ORDER BY id");
		if (!@mysql_num_rows($cresult))
		{
			echo "<p class=\"answer\">There are currently no categories in the FAQ.</p>\n";
			return;
		}
		else
		{
			// do the list
			while ($crow = @mysql_fetch_array($cresult,MYSQL_ASSOC))
			{
				echo "<p class=\"category\">", stripslashes($crow['title']), "</p>\n";
				// check existence in a general category
				if (@mysql_num_rows(@mysql_query("SELECT * FROM {$this->tbl_ques} WHERE cnum={$crow['id']} AND (answer LIKE '%$gpcVars[search]%' OR question LIKE '%$gpcVars[search]%')",$this->id)))
				{
					// now get actual questions that match
					$qresult = @mysql_query("SELECT * FROM {$this->tbl_ques} WHERE cnum={$crow['id']}",$this->id);
					$num = 1;
					$found = 0;
					while ($qrow = @mysql_fetch_array($qresult,MYSQL_ASSOC))
					{
						if (eregi($this->gpcVars['search'],$qrow['answer']) || eregi($this->gpcVars['search'],$qrow['question']))
						{
							if (++$found==1) echo "<ol>\n";
							echo "	<li class=\"question\"> <a href=\"{$this->_self}&cat={$crow['id']}#{$num}\" class=\"question\">",stripslashes($qrow['question']),"</a></li><br>\n";
							$found++;
						}
						$num++;
					}
					if ($found) echo "</ol>\n";
					else echo "<p class=\"answer\">No matches were found in this category</p>\n";
				}
				else
				{
					echo "<p class=\"answer\">No matches were found in this category</p>\n";
				}
				@mysql_free_result($qresult);
			}
		}
		@mysql_free_result($cresult);
		echo "<p><a href=\"{$this->_self}\">Show all</a></p>\n";
	}

}

?>
