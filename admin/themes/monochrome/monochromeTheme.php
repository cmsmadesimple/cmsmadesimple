<?php
#
# Theme Override!
#

class monochromeTheme extends AdminTheme {

    function DisplayTopMenu($menuItems)
    {
        echo "<div id=\"TopMenu\"><ul>\n";
        foreach ($menuItems as $menuItem)
            {
            echo '<li><a href="'.$menuItem['url'].'"';
            if ($menuItem['selected'])
                {
                echo ' id="TopMenuSelected"';
                }
            if ($menuItem['section']=='bookmarks')
                {
                echo ' onMouseOver="Javascript:toggleMarkState()"';
                }
            echo ">".$menuItem['title']."</a></li>";
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
