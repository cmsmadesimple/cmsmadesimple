<?php

require_once("../include.php");

check_login($config);

include_once("header.php");
?>

<div id="nav">
 <ul>
  <li class="sub"><div class="title">Admin Page</div></li>
  <li>
   <ul class"sub">
    <li class="item"><a href="listcontent.php">Content Management</a></li>
    <li class="item"><a href="listsections.php">Section Management</a></li>
    <li class="item"><a href="listtemplates.php">Template Management</a></li>
    <li class="item"><a href="listusers.php">User Management</a></li>
    <li class="item"><a href="listgroups.php">Group Management</a></li>
    <li class="item"><a href="logout.php">Logout</a></li>
   </ul>
 </li>
 </ul>
</div>

<?php
include_once("footer.php");
?>
