<?php

class bluewaterTheme extends AdminTheme
{
    function DisplayTopMenu()
    {
    	echo "<img class=\"logo\" src=\"themes/bluewater/images/logo9.gif\" />";
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($this->menuItems as $key=>$menuItem)
        	{
        	if ($menuItem['parent'] == -1)
        		{
        		$this->renderMenuSection($key, 0, -1);
        		}
        	}
        echo "</ul></div>\n";
    }



}
?>
