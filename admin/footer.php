
</div>

<DIV ID="footer" CLASS="footer">

</DIV>

<DIV ID="footer1"></DIV>
<DIV ID="footer2"><A HREF="http://www.cmsmadesimple.org">CMS made simple</A> is Free Software released under the GNU/GPL License</DIV>

<?php
if ($config["debug"] == true){
	echo '<div ID="debugfooter">';
	global $sql_queries;
	echo "<div>".$sql_queries."</div>\n";
	echo '</div>';
}
?>
</BODY>
</HTML>