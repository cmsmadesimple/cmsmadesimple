<?php

$error = "";
$hiddenfields = "";
$db = $cms->db;
$config = $cms->config;

//$news_cat = "";
$news_cat = "";
if( isset($_POST[$id."news_cat"])) $news_cat = $_POST[$id."news_cat"];
if( strlen($news_cat) == 0 ) {
  if( isset( $_GET[$id."news_cat"])) $news_cat = $_GET[$id."news_cat"];
  if( strlen($news_cat) == 0 ) {
    if (isset($_POST[$id."selcat"])) $news_cat = $_POST[$id."selcat"];
    if( strlen($news_cat) == 0 ) {
      if (isset($_POST[$id."addcat"]) && strlen($_POST[$id."addcat"])) $news_cat = $_POST[$id."addcat"];
    }
  }
}
$title = "";
if (isset($_POST[$id."newstitle"])) $title = $_POST[$id."newstitle"];
$data = "";
if (isset($_POST[$id."newscontent"])) $data = $_POST[$id."newscontent"];
$post_date = "";
if (isset($_POST[$id."post_date"])) $post_date = $_POST[$id."post_date"];
$start_date = "";
if (isset($_POST[$id."start_date"])) $start_date = $_POST[$id."start_date"];
$expiry = "";
if (isset($_POST[$id."expiry"])) $expiry = $_POST[$id."expiry"];
$end_date = "";
if (isset($_POST[$id."end_date"])) $end_date = $_POST[$id."end_date"];
 
if (isset($_POST[$id."cancelsubmit"])) {
        redirect($config["root_url"]."/admin/moduleinterface.php?module=News");
        return;
}
//$hiddenfields .= "<input type=\"hidden\" name=\"".$id."news_cat\" value=\"$newscat\" />";
switch( $moduleaction )
{
  case "Add":
  case "Select":
    echo "<p>Error!</p>"; 
    break;

  case "edit":
    {
      $newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
      $hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeedit\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
      $query = "SELECT * FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
      $dbresult = $db->Execute($query, array($newsid));
      if ($dbresult && $dbresult->RowCount() > 0) 
      {
        $row = $dbresult->FetchRow();
	$news_cat = $row["news_cat"];
        $title = $row["news_title"];
        $data = $row["news_data"];
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
      }
    }
    break;
 
  case "add":
    {
      $newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
      $hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeadd\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
      $post_date = rtrim(ltrim($db->DBTimeStamp(time()), "'"), "'");
    }
    break;

  case "delete":
    {
      $query = "DELETE FROM ".cms_db_prefix()."module_news WHERE news_id = ?";        $dbresult = $db->Execute($query, array((isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"")));
      cms_mapi_audit($cms, (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:""), "News", "Deleted News Item");
       redirect($config["root_url"]."/admin/moduleinterface.php?module=News");
     } 
     break;

  case "completeadd":
    {
      $newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
      $hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeadd\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
      $validinfo = true;

      if ($news_cat == "")                                                            {
	$error .= "<li>No Category given</li>";
        $validinfo = false;
      }
      if ($title == "") {
        $error .= "<li>No title given</li>";
        $validinfo = false;
      }
      if ($data == "") {
        $error .= "<li>No content given</li>";
        $validinfo = false;
      }
      if ($post_date == "") {
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
        $params = array( $news_cat, $new_id, $title, $data, $post_date, $db->DBTimeStamp(time()));
        if (strlen($end_date) > 0)
        {
          $querystart .= ", end_time";
	  $queryend .= ",?";
          array_push($params, $db->DBTimeStamp($end_date));
        }
        else if( $expiry != "" && $expiry != "Never" )
        {
	  $querystart .= ", end_time";
          $queryend .= ",?";
          $time = strtotime("+".$expiry);
          array_push($params, $db->DBTimeStamp($time));
	  if( $start_date == "" ) 
          {
	    $start_date = $db->DBTimeStamp(time());
          }
        }
        if ($start_date != "")
        {
          $querystart .= ", start_time";
	  $queryend .= ",?";
          array_push($params, $db->DBTimeStamp($start_date));
        }
        $query = $querystart . $queryend . ")";
        $dbresult = $db->Execute($query, $params);
        cms_mapi_audit($cms, $new_id, "News", "Added News Item");
        redirect($config["root_url"]."/admin/moduleinterface.php?module=News");
        return;
      }
    }
    break;

  case "completeedit":
    {
      $newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
      $hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeedit\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
                                                                                
      $validinfo = true;
        
      if ($news_cat == "")                                                            {
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
        $params = array($news_cat, $title, $data, $db->DBTimeStamp(time()));
        if ($start_date != "")
        {
          $query .= ", start_time = ?";
          array_push($params, $db->DBTimeStamp($start_date));
        }
        else
        {
          $query .= ", start_time = ?";
          array_push($params, NULL);
        }
        if ($end_date != "")
        {
          $query .= ", end_time = ?";
          array_push($params, $db->DBTimeStamp($end_date));
        }
        else
        {
          $query .= ", end_time = ?";
          array_push($params, NULL);
        }
        if ($post_date != "")
        {
          $query .= ", news_date = ?";
          array_push($params, $db->DBTimeStamp($post_date));
        }
        $query .= " WHERE news_id = ?";
        array_push($params, $newsid);
        $dbresult = $db->Execute($query, $params);
        
        cms_mapi_audit($cms, $newsid, "News", "Edited News Item");
        redirect($config["root_url"]."/admin/moduleinterface.php?module=News");
	return;
      }
    }
    break;

  default:
    echo "ERROR! Unknown action!<BR>";
    return;
    break;
}
  

if ( $error != "" )
{
  echo "<ul class=\"error\">$error</ul>";
} 
?>

<div class="adminform">
 
<?php echo cms_mapi_create_admin_form_start("News", $id)?>
<table width="100%" border="0">
        <?php
	  $query = "SELECT news_cat FROM "
	     .cms_db_prefix()."module_news WHERE news_cat <> '' GROUP BY news_cat";
	  $dbresult = $cms->db->Execute($query);
          if( $dbresult && $dbresult->RowCount() > 0)
          {
	  ?>
	  <tr>
	    <td width="100">Category:</td>
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
     <td>New Category:</td>
     <td><input name="<?php echo $id?>addcat" size="40" maxlength="255" /></td>
  </tr>
  <tr>
    <td width="60">Title:</td>
    <td><input name="<?php echo $id?>newstitle" maxlength="255" value="<?php echo $title?>" class="standard" /></td>
  </tr>
  <tr>
    <td>Content:</td>
    <td><?php echo create_textarea(true, $data, $id.'newscontent', 'syntaxHighlight', $id.'newscontent')?></td>
  </tr>
  <?php //if ($moduleaction == "edit" || $moduleaction == "completeedit") { ?>
  <tr>
    <td>Post Date:</td>
    <td><input type="text"  name="<?php echo $id?>post_date" maxlength="20" size="12" value="<?php echo $post_date?>" /></td>
  </tr>
  <?php //} ?>
  <tr>
    <td>Start Date:</td>
    <td><input type="text"  name="<?php echo $id?>start_date" maxlength="20" size="12" value="<?php echo $start_date?>" /></td>
  </tr>
  <tr>
    <td>Expiry:</td>
    <td>
<?php
   if( $moduleaction == "edit" || $moduleaction == "completeedit" ) {
      echo "<select name=\"".$id."expiry\" disabled=\"disabled\">";
   }
   else {
      echo "<select name=\"".$id."expiry\">";
   }
?>
         <option>1 Day</option>
         <option>1 Week</option>
         <option>2 Weeks</option>
         <option>1 Month</option>
         <option>3 Months</option>
         <option selected="selected">6 Months</option>
         <option>1 Year</option>
         <option>Never</option>
       </select>
    </td>
  </tr>
  <tr>
    <td>End Date:</td>
    <td><input type="text"  name="<?php echo $id?>end_date" maxlength="20" size="12" value="<?php echo $end_date?>" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><em>Note:</em> Dates must be in a 'yyyy-mm-dd hh:mm:ss' format.</td>
  </tr>
  <tr>
    <td><?php echo $hiddenfields?>&nbsp;</td>
    <td>
      <input type="submit" name="<?php echo $id?>submit" value="Submit" />
      <input type="submit" name="<?php echo $id?>cancelsubmit" value="Cancel" />
    </td>
  </tr>
</table>
 
<?php echo cms_mapi_create_admin_form_end()?>
 
</div>
 
