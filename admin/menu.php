<div id="nav">
 <ul>
  <li class="sub"><div class="title"><?php
  
GetText::setVar("name", $_SESSION["cms_admin_username"]);
echo GetText::gettext('Welcome ${name}');
GetText::reset();
  
  ?></div></li>
  <li>
   <ul class"sub">
    <li class="item"><a href="listcontent.php"><?=GetText::gettext("Content Management")?></a></li>
    <li class="item"><a href="listsections.php"><?=GetText::gettext("Section Management")?></a></li>
    <li class="item"><a href="listtemplates.php"><?=GetText::gettext("Template Management")?></a></li>
    <li class="item"><a href="listusers.php"><?=GetText::gettext("User Management")?></a></li>
    <li class="item"><a href="listgroups.php"><?=GetText::gettext("Group Management")?></a></li>
    <li class="item"><a href="../index.php" target="_new"><?=GetText::gettext("Show Site")?></a></li>
    <li class="item"><a href="logout.php"><?=GetText::gettext("Logout")?></a></li>
   </ul>
 </li>
 </ul>
</div> 
