<div class="nav">
<h4><?php
  
GetText::setVar("name", $_SESSION["cms_admin_username"]);
echo GetText::gettext('Welcome ${name}');
GetText::reset();
  
?></h4>

<a href="listcontent.php"><?=GetText::gettext("Content Management")?></a>
<a href="listsections.php"><?=GetText::gettext("Section Management")?></a>
<a href="listtemplates.php"><?=GetText::gettext("Template Management")?></a>
<a href="listusers.php"><?=GetText::gettext("User Management")?></a>
<a href="listgroups.php"><?=GetText::gettext("Group Management")?></a>
<a href="adminlog.php"><?=GetText::gettext("Admin Log")?></a>
<a href="../index.php" target="_new"><?=GetText::gettext("Show Site")?></a>
<a href="logout.php"><?=GetText::gettext("Logout")?></a>
</div> 
