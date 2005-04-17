<?php
#
# Theme Override!
#

class monochromeTheme extends AdminTheme {

    function DisplayTopMenu($menuItems)
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($menuItems as $key=>$menuItem)
            {
            $thisItem = $menuItem[0];
            echo '<li><a href="'.$thisItem['url'].'"';
			if ($thisItem['url'] == '../index.php')
				{				echo ' target="_blank"';				}            
			if ($thisItem['selected'])
                {
                echo ' class="selected"';
                }
            if ($key=='bookmarks' && get_preference(get_userid(), 'bookmarks'))
                {
                echo ' onMouseOver="Javascript:toggleMarkState()"';
                }
            echo ">".$thisItem['title']."</a>";
            echo "</li>\n";
            }
        echo "</ul></div>\n";
    }

    function DisplayRecentPages()
    {
        echo '<div class="recent">';
        echo "<ul><li>".lang('recentpages').'</li>';
        foreach($this->recent as $pg)
            {
            echo "<li>&gt; <a href=\"". $pg->url."\">".$pg->title."</a></li>\n";
            }
        echo "</ul>\n";
        echo "</div>\n";
    }

    function DisplayBookmarks($marks)
    {
        echo '<div id="bookmarks">';
        echo "<p class=\"DashboardCalloutTitle\">";
        echo lang('bookmarks');
        echo "</p>\n";

        echo "<ul>";
        foreach($marks as $mark)
            {
            echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
            }
        echo "</ul>\n";
        echo "</div>\n";
    }


    function StartRighthandColumn()
    {
    }
    function EndRighthandColumn()
    {
    }

    function OutputHeaderJavascript()
    {
?>
<script type="text/javascript">
var marks_on = false;

function toggleMarkState()
    {
    marks_on = ! marks_on;
    if (marks_on)
        {
        document.getElementById('bookmarks').style.visibility='visible';
        var height = getStyle('bookmarks','height');
        if (height != null)
            {
            height=height.replace(/\D/g,'');
            var new_el = 300 - height;
            document.getElementById('bookmarks').style.top = new_el+'px';
            }
        }
    else
        {
        document.getElementById('bookmarks').style.visibility='hidden';
        }
    }

function getStyle(element, style) {
   if(!document.getElementById) return;
   var el = document.getElementById(element);
   var value = null;
   value = el.style[style];
    if(!value)
        {
        if(document.defaultView)
            {
            value = document.defaultView.getComputedStyle(el, '').getPropertyValue(style);
            }
        else
            {
            if(el.currentStyle)
                {
                value = el.currentStyle[style];
                }
            }
        }
     return value;
}

</script>
<?php
    }

}

?>
