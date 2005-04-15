<?php

class bluewaterTheme extends AdminTheme
{
    function DisplayTopMenu($menuItems)
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($menuItems as $key=>$menuItem)
            {
            $count = count($menuItem);
            $counter = 1;
            foreach ($menuItem as $thisItem)
                {
                echo '<li';
                if ($thisItem['selected'])
                    {
                    echo ' class="selected"';
                    }
                echo '><a href="'.$thisItem['url'].'"';
                if ($thisItem['selected'])
                    {
                    echo ' class="selected"';
                    }
                echo ">".$thisItem['title']."</a>";
                if ($count > 1 && $counter == 1)
                    {
                    echo "<ul>\n";
                    }
                else if ($count > 1 && $count == $counter)
                    {
                    echo "</li></ul>\n";
                    }
                else
                    {
                    echo "</li>\n";
                    }
                $counter++;
                }
            }
        echo "</ul></div>\n";
    }



}
?>
