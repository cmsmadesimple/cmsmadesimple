<div class="nav">
<h4><?php
  
$gettext->setVar("name", $_SESSION["cms_admin_username"]);
echo $gettext->gettext('Welcome ${name}');
$gettext->reset();
  
?></h4>

<a href="listcontent.php"><?=$gettext->gettext("Content Management")?></a>
<a href="listsections.php"><?=$gettext->gettext("Section Management")?></a>
<a href="listtemplates.php"><?=$gettext->gettext("Template Management")?></a>
<a href="listusers.php"><?=$gettext->gettext("User Management")?></a>
<a href="listgroups.php"><?=$gettext->gettext("Group Management")?></a>
<a href="adminlog.php"><?=$gettext->gettext("Admin Log")?></a>
<a href="editprefs.php"><?=$gettext->gettext("User Preferences")?></a>
<a href="../index.php" target="_new"><?=$gettext->gettext("Show Site")?></a>
<a href="logout.php"><?=$gettext->gettext("Logout")?></a>
</div> 
