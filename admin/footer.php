
</div>

<div id="footer" class="footer">

</div>

<div id="footer1"></div>
<div id="footer2"><a href="http://www.cmsmadesimple.org">CMS made simple</a> is Free Software released under the GNU/GPL License</div>

<?php
if ($config["debug"] == true)
{
	echo '<div id="debugfooter">';
	global $sql_queries;
	echo "<div>".$sql_queries."</div>\n";
	echo '</div>';
}
?>
</body>
</html>
