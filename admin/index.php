<?php

require_once("../include.php");

check_login($config);

?>

<h3>Admin Page</h3>

<ul>
<li><a href="listcontent.php">Content Management</a></li>
<li><a href="listsections.php">Section Management</a></li>
<li><a href="listtemplates.php">Template Management</a></li>
<li><a href="listusers.php">User Management</a></li>
<!--<li><a href="listgroups.php">Group Management</a></li>-->
<li><a href="logout.php">Logout</a></li>
</ul>
