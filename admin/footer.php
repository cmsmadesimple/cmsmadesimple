
</div>

<DIV ID="footer" CLASS="footer">

</DIV>

<DIV ID="footer1"></DIV>
<DIV ID="footer2"><A HREF="http://www.cmsmadesimple.org">CMS made simple</A> is Free Software released under the GNU/GPL License</DIV>
</BODY>

<div ID="debugfooter">
<?php
if ($config["debug"] == true)
{
	global $sql_queries;
	echo "<div>".$sql_queries."</div>\n";
}
?>
</div>

</HTML>
