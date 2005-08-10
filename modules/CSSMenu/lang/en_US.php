<?php
$lang['horizontal'] = 'Horizontal Stylesheet';
$lang['vertical'] = 'Vertical Stylesheet';
$lang['help'] = <<<EOF
  <h3>What does this do?</h3>
  <p>Prints a vertical standards compliant CSS-only menu. A small bit of JavaScript is required for IE.</p>
  <p>It was based on these articles, <a href="http://www.alistapart.com/articles/horizdropdowns/">Drop-Down Menus, Horizontal Style</a>, <a href="http://www.nickrigby.com/article/11/drop-down-menus-horizontal-style-pt-2">Drop-Down Menus, Horizontal Style: Pt 2</a>, <a href="http://www.nickrigby.com/article/25/drop-down-menus-horizontal-style-pt-3">Drop-Down Menus, Horizontal Style: Pt 3</a> by Nick Rigby.</p>
  <h3>How do I use it?</h3>
  <p>Just insert the module into your template/page like: <code>{cms_module module='cssmenu'}</code></p>
  <p>Modify /modules/CSSMenu/CSSMenu.css to change the style. Refer to above articles for more information.</p>
  <p>Modify /modules/CSSMenu/CSSMenuHorizontal.css to change the style. On line 14 adjust width: 600px; to suitable value for the width. (try '100%%')
  <h3>What parameters does it take?</h3>
  <p>
  <ul>
    <li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
    <li><em>(optional)</em> <tt>start_element</tt> - the hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu.</li>
    <li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
    <li><em>(optional)</em> <tt>horizontal</tt> - 1/0, whether you want to have a horizontal menu instead of vertical.</li>
  </ul>
  </p>
EOF;
?>
