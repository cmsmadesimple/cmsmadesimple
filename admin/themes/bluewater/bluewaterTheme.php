<?php

class bluewaterTheme extends AdminTheme
{
    function DisplayTopMenu()
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($this->menuItems as $key=>$menuItem)
            {
            $count = count($menuItem);
            $counter = 1;
            foreach ($menuItem as $thisItem)
                {
                if (! get_preference(get_userid(), 'bookmarks') &&
                      preg_match('/makebookmark\.php/',$thisItem['url']))
                    {
                    continue;
                    }
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
				if ($thisItem['url'] == '../index.php')
					{
					echo ' target="_blank"';
					}
                echo ">".$thisItem['title']."</a>";
                if ($count > 1 && $counter == 1)
                    {
                    echo "<ul>";
                    }
                else if ($count > 1 && $count == $counter)
                    {
                    echo "</li></ul>";
                    }
                else
                    {
                    echo "</li>";
                    }
                $counter++;
                }
            }
        echo "</ul></div>\n";
    }



}
?>
